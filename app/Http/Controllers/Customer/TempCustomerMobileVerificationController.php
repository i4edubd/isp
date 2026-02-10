<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\AllCustomerController;
use App\Http\Controllers\CacheController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\Sms\SmsGatewayController;
use App\Http\Controllers\SmsGenerator;
use App\Models\all_customer;
use App\Models\Freeradius\customer;
use App\Models\operator;
use App\Models\temp_customer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TempCustomerMobileVerificationController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\temp_customer $temp_customer
     * @return \Illuminate\Http\Response
     */
    public function create(temp_customer $temp_customer)
    {
        $operator = CacheController::getOperator($temp_customer->operator_id);
        $key = 'temp_customer_otp_' . $temp_customer->mobile;
        $otp = Cache::remember($key, 300, function () use ($temp_customer) {
            $otp = random_int(1000, 9999);
            $temp_customer->otp = $otp;
            $temp_customer->save();

            $operator = CacheController::getOperator($temp_customer->operator_id);
            $message = SmsGenerator::OTP($operator, $otp);

            try {
                SmsGatewayController::sendSms($operator, $temp_customer->mobile, $message, 0);
            } catch (\Throwable $th) {
                Log::channel('stack')->error('@temp_customers.mobile-verification.create => ' . $th);
            }

            return $otp;
        });

        return view('customers.temp-customer-mobile-verification-form', [
            'operator' => $operator,
            'temp_customer' => $temp_customer,
            'otp' => $otp,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\temp_customer $temp_customer
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, temp_customer $temp_customer)
    {
        $request->validate([
            'otp' => 'required',
        ]);

        if ($temp_customer->otp != $request->otp) {
            return redirect()->route('temp_customers.mobile-verification.create', ['temp_customer' => $temp_customer])->with('error', 'Invalid PIN');
        }

        // <<operator>>
        $operator = operator::find($temp_customer->operator_id);

        // <<trial package>>
        $package = PackageController::trialPackage($operator);
        $master_package = $package->master_package;

        // <<expiry_time>>
        $expiry_time = Carbon::now(getTimeZone($temp_customer->operator_id))->addMinutes($master_package->total_minute)->isoFormat(config('app.expiry_time_format'));

        // <<duplicate check>>
        $model = new customer();
        $model->setConnection($operator->radius_db_connection);

        if ($model->where('username', $temp_customer->username)->count()) {
            // <<save new informations>>
            $mac_customer = $model->where('username', $temp_customer->username)->first();
            $mac_customer->password = $temp_customer->password;
            $mac_customer->router_ip = $temp_customer->router_ip;
            $mac_customer->router_id = $temp_customer->router_id;
            $mac_customer->login_mac_address = $temp_customer->login_mac_address;
            $mac_customer->save();

            // <<internet login>>
            HotspotInternetLoginController::login($mac_customer);

            // <<web login>>
            $all_customer = all_customer::where('operator_id', $mac_customer->operator_id)->where('customer_id', $mac_customer->id)->firstOr(function () {
                abort(404, 'customer in all_customers not found');
            });
            CustomerWebLoginController::startWebSession($all_customer);
            $request->session()->regenerate();
            return redirect()->route('customers.home');
        }

        // <<new customer>>
        $customer = new customer();
        $customer->setConnection($operator->radius_db_connection);
        $customer->mgid = $temp_customer->mgid;
        $customer->gid = $temp_customer->gid;
        $customer->operator_id = $temp_customer->operator_id;
        $customer->company = $temp_customer->company;
        $customer->connection_type = $temp_customer->connection_type;
        $customer->billing_type = $temp_customer->billing_type;
        $customer->zone_id = 0;
        $customer->device_id = 0;
        $customer->name = $temp_customer->name;
        $customer->mobile = $temp_customer->mobile;
        $customer->verified_mobile = 1;
        $customer->mobile_verified_at = date(config('app.date_format'));
        $customer->username = trim($temp_customer->username);
        $customer->password = trim($temp_customer->password);
        $customer->package_id = $package->id;
        $customer->package_name = $package->name;
        $customer->package_started_at = Carbon::now(getTimeZone($customer->operator_id))->isoFormat(config('app.expiry_time_format'));
        $customer->package_expired_at = $expiry_time;
        $customer->exptimestamp = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($customer->operator_id), 'en')->timestamp;
        $customer->total_octet_limit = $master_package->total_octet_limit;
        $customer->payment_status = 'paid';
        $customer->status = 'active';
        $customer->router_ip = $temp_customer->router_ip;
        $customer->router_id = $temp_customer->router_id;
        $customer->link_login_only = $temp_customer->link_login_only;
        $customer->link_logout = $temp_customer->link_logout;
        $customer->login_ip = $temp_customer->login_ip;
        $customer->login_mac_address = $temp_customer->login_mac_address;

        // <<Registration timestamp>>
        $customer->registration_date = date(config('app.date_format'));
        $customer->registration_week = date(config('app.week_format'));
        $customer->registration_month = date(config('app.month_format'));
        $customer->registration_year = date(config('app.year_format'));
        $customer->save();
        $customer->parent_id = $customer->id;
        $customer->save();

        // <<Central customer information>>
        AllCustomerController::updateOrCreate($customer);

        // <<radius information>>
        HotspotCustomersRadAttributesController::updateOrCreate($customer);

        // <<clean garbage>>
        $key = 'temp_customer_otp_' . $temp_customer->mobile;
        if (Cache::has($key)) {
            Cache::forget($key);
        }
        $temp_customer->delete();

        // <<welcome message>>
        try {
            SmsMessagesForCustomerController::hotspotRegistration($customer);
        } catch (\Throwable $th) {
            Log::channel('stack')->error('@temp_customers.mobile-verification.store => ' . $th);
        }

        // <<web session>>
        $all_customer = all_customer::where('operator_id', $customer->operator_id)->where('customer_id', $customer->id)->firstOr(function () {
            abort(404, 'customer in all_customers not found');
        });
        CustomerWebLoginController::startWebSession($all_customer);
        $request->session()->regenerate();
        return redirect()->route('customers.home');
    }
}
