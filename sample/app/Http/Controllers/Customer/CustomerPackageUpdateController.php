<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\customer_bill;
use App\Models\Freeradius\customer;
use App\Models\package;
use Illuminate\Http\Request;

class CustomerPackageUpdateController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @param \App\Models\package $new_package
     * @return \Illuminate\Http\Response
     */
    public static function update(customer $customer, package $new_package)
    {
        $master_package = $new_package->master_package;

        if ($customer->connection_type !== $master_package->connection_type) {
            return 0;
        }

        //update customer
        $customer->package_id = $new_package->id;
        $customer->package_name = $new_package->name;
        $customer->rate_limit = $master_package->rate_limit;
        $customer->total_octet_limit = ($master_package->total_octet_limit > 1) ? $master_package->total_octet_limit : 0;
        $customer->save();

        if ($customer->connection_type == 'PPPoE') {
            PPPoECustomersRadAttributesController::updateOrCreate($customer);
        }

        if ($customer->connection_type == 'Hotspot') {
            HotspotCustomersRadAttributesController::updateOrCreate($customer);
        }

        if ($customer->connection_type == 'StaticIp') {
            StaticIpCustomersFirewallController::updateOrCreate($customer);
        }

        $bills_where = [
            ['operator_id', '=', $customer->operator_id],
            ['customer_id', '=', $customer->id],
        ];
        customer_bill::where($bills_where)->update(['package_id' => $new_package->id]);

        return 0;
    }
}
