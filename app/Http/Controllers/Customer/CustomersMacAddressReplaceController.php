<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\CacheController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Sms\SmsGatewayController;
use App\Http\Controllers\SmsGenerator;
use App\Models\Freeradius\customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CustomersMacAddressReplaceController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $all_customer = $request->user('customer');
        $operator = CacheController::getOperator($all_customer->operator_id);
        return view('customers.suspicious-login', [
            'operator' => $operator,
            'all_customer' => $all_customer,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $all_customer = $request->user('customer');
        $customer = CacheController::getCustomerUseAllCustomer($all_customer);
        $operator = CacheController::getOperator($all_customer->operator_id);

        //send and save otp
        $key = self::getCacheKey($customer);
        $otp = Cache::remember($key, 300, function () use ($customer) {
            $otp = random_int(1000, 9999);

            $operator = CacheController::getOperator($customer->operator_id);
            $message = SmsGenerator::OTP($operator, $otp);

            try {
                SmsGatewayController::sendSms($operator, $customer->mobile, $message, $customer->id);
            } catch (\Throwable $th) {
                Log::channel('stack')->error('@mac_address_replace.mobile-verification.create => ' . $th);
            }

            return $otp;
        });

        return view('customers.mac-replace-form', [
            'operator' => $operator,
            'customer' => $customer,
            'otp' => $otp,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'otp' => 'required',
        ]);

        $all_customer = $request->user('customer');
        $customer = CacheController::getCustomerUseAllCustomer($all_customer);

        $key = self::getCacheKey($customer);
        $otp = Cache::get($key, random_int(1000, 9999));

        if ($otp != $request->otp) {
            return redirect()->route('customers.replace-mac-address.create')->with('error', 'Invalid PIN');
        }

        if (Cache::has($key)) {
            Cache::forget($key);
        }

        $customer->verified_mobile = 1;
        $customer->mobile_verified_at = date(config('app.date_time_format'));
        $customer->mac_replace_date = date(config('app.date_format'));
        $customer->username = trim($customer->login_mac_address);
        $customer->password = trim($customer->login_mac_address);
        $customer->save();

        HotspotCustomersRadAttributesController::updateOrCreate($customer);

        HotspotInternetLoginController::login($customer);

        return redirect()->route('customers.profile');
    }

    /**
     * Cache Key for this class.
     *
     * @param  \App\Models\Freeradius\customer $customer
     * @return  string
     */
    public static function getCacheKey(customer $customer)
    {
        return 'mac_address_replace_otp_' . $customer->mobile;
    }
}
