<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\CacheController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PPPCustomerDisconnectController;
use App\Models\Freeradius\customer;
use Illuminate\Http\Request;

class CustomerSuspendController extends Controller
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
        $this->authorize('suspend', $customer);

        $customer->status = 'suspended';
        $customer->suspend_reason = 'suspended_by_operator';
        $customer->save();

        switch ($customer->connection_type) {
            case 'PPPoE':
                // from disabled
                CustomersRadPasswordController::updateOrCreate($customer);
                // from active
                PPPoECustomersFramedIPAddressController::updateOrCreate($customer);
                // disconnect
                PPPCustomerDisconnectController::disconnect($customer);
                break;
            case 'StaticIp':
                StaticIpCustomersFirewallController::updateOrCreate($customer);
                break;
        }

        return 'The customer has been suspended successfully';
    }
}
