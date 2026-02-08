<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Freeradius\customer;
use App\Models\operator;
use App\Models\pgsql\pgsql_radacct_history;
use Carbon\Carbon;

class TempInternetController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public static function store(customer $customer)
    {
        // disabled >> replaced with walled-garden
        return 0;

        //expiration
        if ($customer->package_expired_at) {

            $expiration = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($customer->operator_id), 'en');

            $now = Carbon::now(getTimeZone($customer->operator_id));

            if ($expiration < $now) {

                $life = Carbon::now(getTimeZone($customer->operator_id))->addMinutes(3)->isoFormat(config('app.expiry_time_format'));

                $customer->package_expired_at = $life;
                $customer->exptimestamp = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($customer->operator_id), 'en')->timestamp;
                $customer->save();

                HotspotCustomersExpirationController::updateOrCreate($customer);
            }
        }

        //Mikrotik-Total-Limit
        if ($customer->total_octet_limit) {

            $operator = operator::find($customer->operator_id);

            $pgsql_radacct_history = new pgsql_radacct_history();

            $pgsql_radacct_history->setConnection($operator->pgsql_connection);

            $download = $pgsql_radacct_history->where('username', '=', $customer->username)->sum('acctoutputoctets');

            $upload = $pgsql_radacct_history->where('username', '=', $customer->username)->sum('acctinputoctets');

            $usage =  $download + $upload;

            $required_limit = $usage + 5000000;

            if ($customer->total_octet_limit < $required_limit) {

                $customer->total_octet_limit = $required_limit;

                $customer->save();

                CustomersRadLimitController::updateOrCreate($customer);
            }
        }

        //Internet Login
        HotspotInternetLoginController::login($customer);
    }
}
