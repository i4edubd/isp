<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\CacheController;
use App\Http\Controllers\Controller;
use App\Models\Freeradius\customer;
use App\Models\pgsql\pgsql_radreply;
use Illuminate\Http\Request;

class PPPoECustomersMikrotikGroupController extends Controller
{


    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return void
     */
    public static function updateOrCreate(customer $customer)
    {
        if ($customer->connection_type !== 'PPPoE') {
            return 0;
        }

        $operator = CacheController::getOperator($customer->operator_id);

        $package = CacheController::getPackage($customer->package_id);

        $master_package = $package->master_package;

        $pppoe_profile = $master_package->pppoe_profile;

        if (!strlen($pppoe_profile->name)) {
            return 0;
        }

        //updateOrCreate Mikrotik-Group Attribute

        $radreply = new pgsql_radreply();

        $radreply->setConnection($operator->pgsql_connection);

        $radreply->updateOrCreate(
            [
                'mgid' => $customer->mgid,
                'customer_id' => $customer->id,
                'username' => $customer->username,
                'attribute' => 'Mikrotik-Group',
            ],
            [
                'value' => $pppoe_profile->name,
            ]
        );
    }
}
