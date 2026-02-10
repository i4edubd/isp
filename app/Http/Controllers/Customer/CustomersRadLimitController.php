<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\CacheController;
use App\Http\Controllers\Controller;
use App\Models\Freeradius\customer;
use App\Models\pgsql\pgsql_radreply;

class CustomersRadLimitController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return void
     */
    public static function updateOrCreate(customer $customer)
    {
        $operator = CacheController::getOperator($customer->operator_id);

        if (!$operator) {
            return 0;
        }

        $package = CacheController::getPackage($customer->package_id);

        if (!$package) {
            return 0;
        }

        $master_package = $package->master_package;

        $type_allowed = ['PPPoE', 'Hotspot'];

        if (in_array($customer->connection_type, $type_allowed) == false) {
            return 0;
        }

        // Port-Limit for Hotspot
        /*
        if ($customer->connection_type == 'Hotspot') {
            $radreply = new pgsql_radreply();
            $radreply->setConnection($operator->pgsql_connection);
            $radreply->updateOrCreate(
                [
                    'mgid' => $customer->mgid,
                    'customer_id' => $customer->id,
                    'username' => $customer->username,
                    'attribute' => 'Port-Limit',
                ],
                [
                    'value' => 1,
                ]
            );
        }
        */

        //Mikrotik-Total-Limit
        if ($customer->total_octet_limit > 0) {
            $radreply = new pgsql_radreply();
            $radreply->setConnection($operator->pgsql_connection);
            $radreply->updateOrCreate(
                [
                    'mgid' => $customer->mgid,
                    'customer_id' => $customer->id,
                    'username' => $customer->username,
                    'attribute' => 'Mikrotik-Total-Limit',
                ],
                [
                    'value' => $customer->total_octet_limit,
                ]
            );
        } else {
            $where = [
                ['mgid', '=', $customer->mgid],
                ['customer_id', '=', $customer->id],
                ['attribute', '=', 'Mikrotik-Total-Limit'],
            ];
            $radreply = new pgsql_radreply();
            $radreply->setConnection($operator->pgsql_connection);
            $radreply->where($where)->delete();
        }


        //Mikrotik-Rate-Limit
        if ($customer->rate_limit > 0 && $master_package->speed_controller == 'Radius_Server') {
            $radreply = new pgsql_radreply();
            $radreply->setConnection($operator->pgsql_connection);
            $radreply->updateOrCreate(
                [
                    'mgid' => $customer->mgid,
                    'customer_id' => $customer->id,
                    'username' => $customer->username,
                    'attribute' => 'Mikrotik-Rate-Limit',
                ],
                [
                    'value' => $customer->rate_limit . $master_package->rate_unit,
                ]
            );
        } else {
            $where = [
                ['mgid', '=', $customer->mgid],
                ['customer_id', '=', $customer->id],
                ['attribute', '=', 'Mikrotik-Rate-Limit'],
            ];
            $radreply = new pgsql_radreply();
            $radreply->setConnection($operator->pgsql_connection);
            $radreply->where($where)->delete();
        }
    }
}
