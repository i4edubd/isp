<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\CacheController;
use App\Http\Controllers\Controller;
use App\Jobs\CallingStationIdUpdateOrCreateJob;
use App\Models\Freeradius\customer;
use App\Models\pgsql\pgsql_radcheck;

class RadCallingStationIdController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return void
     */
    public static function updateOrCreate(customer $customer)
    {
        CallingStationIdUpdateOrCreateJob::dispatch($customer)
            ->onConnection('database')
            ->onQueue('calling_station_id_attribute');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return void
     */
    public static function doUpdateOrCreate(customer $customer)
    {
        $operator = CacheController::getOperator($customer->operator_id);
        $radcheck = new pgsql_radcheck();
        $radcheck->setConnection($operator->pgsql_connection);

        if ($customer->mac_bind == 1 && is_null($customer->login_mac_address) == false) {
            $radcheck->updateOrCreate(
                [
                    'mgid' => $customer->mgid,
                    'customer_id' => $customer->id,
                    'username' => $customer->username,
                    'attribute' => 'Calling-Station-Id',
                ],
                [
                    'op' => '==',
                    'value' => $customer->login_mac_address,
                ]
            );
        } else {
            $where = [
                ['mgid', '=', $customer->mgid],
                ['customer_id', '=', $customer->id],
                ['attribute', '=', 'Calling-Station-Id'],
            ];
            $radcheck->where($where)->delete();
        }
    }
}
