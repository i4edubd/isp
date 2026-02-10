<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Freeradius\customer;
use App\Models\operator;
use App\Models\pgsql\pgsql_radcheck;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HotspotCustomersExpirationController extends Controller
{

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return void
     */
    public static function updateOrCreate(customer $customer)
    {

        if ($customer->connection_type !== 'Hotspot') {
            return 0;
        }

        try {
            $package_expired_at = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($customer->operator_id), 'en')->isoFormat(config('app.expiry_time_format'));
        } catch (\Throwable $th) {
            return 0;
        }

        $operator = operator::find($customer->operator_id);

        if ($customer->package_expired_at) {
            $radcheck = new pgsql_radcheck();
            $radcheck->setConnection($operator->pgsql_connection);
            $radcheck->updateOrCreate(
                [
                    'mgid' => $customer->mgid,
                    'customer_id' => $customer->id,
                    'username' => $customer->username,
                    'attribute' => 'Expiration',
                ],
                [
                    'value' => $package_expired_at,
                ]
            );
        } else {
            $where = [
                ['mgid', '=', $customer->mgid],
                ['customer_id', '=', $customer->id],
                ['attribute', '=', 'Expiration'],
            ];
            $radcheck = new pgsql_radcheck();
            $radcheck->setConnection($operator->pgsql_connection);
            $radcheck->where($where)->delete();
        }
    }
}
