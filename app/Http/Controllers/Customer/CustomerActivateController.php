<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PPPCustomerDisconnectController;
use App\Models\Freeradius\customer;

class CustomerActivateController extends Controller
{

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(customer $customer)
    {
        $this->authorize('activate', $customer);

        $customer->status = 'active';
        $customer->save();

        switch ($customer->connection_type) {
            case 'PPPoE':
                // from disabled
                CustomersRadPasswordController::updateOrCreate($customer);
                // from suspeded || FUP
                PPPoECustomersFramedIPAddressController::updateOrCreate($customer);
                // from FUP
                CustomersRadLimitController::updateOrCreate($customer);
                //disconnect
                PPPCustomerDisconnectController::disconnect($customer);
                break;
            case 'StaticIp':
                StaticIpCustomersFirewallController::updateOrCreate($customer);
                break;
        }

        return 'Customer has been activated successfully';
    }
}
