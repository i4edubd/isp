<?php

namespace App\Http\Controllers;

use App\Models\billing_profile;
use App\Models\Freeradius\customer;
use App\Models\Freeradius\nas;
use App\Models\package;
use App\Models\temp_customer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TempCustomerTechInfoController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\temp_customer  $temp_customer
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, temp_customer $temp_customer)
    {
        $operator = $request->user();

        $packages = $operator->packages;

        $connection_type = $temp_customer->connection_type;

        $packages = $packages->filter(function ($package) use ($connection_type) {
            return $package->master_package->connection_type == $connection_type && $package->name !== 'Trial';
        });

        $billing_profile = billing_profile::find($temp_customer->billing_profile_id);

        $routers = nas::where('mgid', $request->user()->id)->get();

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.temp-customer-techinfo', [
                    'temp_customer' => $temp_customer,
                    'packages' => $packages,
                    'billing_profile' => $billing_profile,
                    'routers' => $routers,
                ]);
                break;

            case 'operator':
                return view('admins.operator.temp-customer-techinfo', [
                    'temp_customer' => $temp_customer,
                    'packages' => $packages,
                    'billing_profile' => $billing_profile,
                    'routers' => $routers,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.temp-customer-techinfo', [
                    'temp_customer' => $temp_customer,
                    'packages' => $packages,
                    'billing_profile' => $billing_profile,
                    'routers' => $routers,
                ]);
                break;

            case  'manager':
                return view('admins.manager.temp-customer-techinfo', [
                    'temp_customer' => $temp_customer,
                    'packages' => $packages,
                    'billing_profile' => $billing_profile,
                    'routers' => $routers,
                ]);
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\temp_customer  $temp_customer
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, temp_customer $temp_customer)
    {
        if ($temp_customer->connection_type == 'PPPoE') {
            $request->validate([
                'username' => 'required|string|max:64',
                'password' => 'required|string|max:64',
            ]);

            if (trim($request->username) !== getUserName($request->username)) {
                return redirect()->route('temp_customer.tech_info.create', ['temp_customer' => $temp_customer->id])->with('error', 'Rejected: User-Name contains white space or Invalid characters');
            }

            $duplicate = customer::where('username', trim($request->username))->count();

            if ($duplicate) {
                return redirect()->route('temp_customer.tech_info.create', ['temp_customer' => $temp_customer->id])->with('error', 'Duplicate Username');
            }

            $temp_customer->username = trim($request->username);
            $temp_customer->password = trim($request->password);
            $temp_customer->save();
        }


        $request->validate([
            'package_id' => 'required|numeric',
        ]);
        $package = package::findOrFail($request->package_id);
        $master_package = $package->master_package;
        $package_started_at = Carbon::now(getTimeZone($temp_customer->operator_id))->isoFormat(config('app.expiry_time_format'));
        if ($temp_customer->connection_type == 'PPPoE') {
            // pool error
            if (!$master_package->pppoe_profile->ipv4pool->id) {
                $profile_name = $master_package->pppoe_profile->name;
                $profile_id = $master_package->pppoe_profile->id;
                $error = "IPv4 Pool for profile $profile_name not found. Please change the IPv4 pool of the profile of id $profile_id";
                abort(500, $error);
            }

            $pool = $master_package->pppoe_profile->ipv4pool;
            $free_ip = $pool->broadcast - $pool->gateway - $pool->used_space;
            if ($free_ip < 1) {
                $error = "Not enough IPv4 address in the pool " . $pool->name . ". Please change subnet!";
                abort(500, $error);
            }
        }
        $temp_customer->package_id = $package->id;
        $temp_customer->package_name = $package->name;
        $temp_customer->package_started_at = $package_started_at;
        $temp_customer->rate_limit = $master_package->rate_limit;
        $temp_customer->total_octet_limit = $master_package->total_octet_limit;
        $temp_customer->save();

        switch ($temp_customer->connection_type) {
            case 'PPPoE':
                if ($temp_customer->billing_type == 'Daily') {
                    $request->validate([
                        'validity' => 'required|numeric|min:1',
                    ]);
                    $expiration_date = Carbon::now(getTimeZone($temp_customer->operator_id))->addDays($request->validity)->isoFormat(config('app.expiry_time_format'));
                } else {
                    $billing_profile = billing_profile::findOrFail($temp_customer->billing_profile_id);
                    $expiration_date = Carbon::createFromFormat(config('app.date_format'), $billing_profile->next_payment_date, getTimeZone($temp_customer->operator_id))->isoFormat(config('app.expiry_time_format'));
                }
                break;

            case 'Hotspot':
                $package = package::findOrFail($temp_customer->package_id);
                $expiration_date = Carbon::now(getTimeZone($temp_customer->operator_id))->addDays($package->master_package->validity)->isoFormat(config('app.expiry_time_format'));
                break;
            case 'StaticIp':
            case 'Other':
                $billing_profile = billing_profile::findOrFail($temp_customer->billing_profile_id);
                $expiration_date = Carbon::createFromFormat(config('app.date_format'), $billing_profile->next_payment_date, getTimeZone($temp_customer->operator_id))->isoFormat(config('app.expiry_time_format'));
                break;
        }

        $temp_customer->package_expired_at = $expiration_date;
        $temp_customer->save();

        if ($temp_customer->connection_type == 'StaticIp') {
            $request->validate([
                'router_id' => 'required|numeric',
                'login_ip' => 'string|required|max:128',
            ]);
            $router = nas::findOrFail($request->router_id);
            $temp_customer->router_ip = $router->nasname;
            $temp_customer->router_id = $router->id;
            $temp_customer->login_ip = $request->login_ip;
            $temp_customer->save();
        }

        if ($request->filled('sms_password')) {
            $temp_customer->sms_password = 1;
        } else {
            $temp_customer->sms_password = 0;
        }
        $temp_customer->save();

        return redirect()->route('temp_customer.bill_info.create', ['temp_customer' => $temp_customer->id]);
    }
}
