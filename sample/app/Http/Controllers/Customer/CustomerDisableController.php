<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\CacheController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PPPCustomerDisconnectController;
use App\Models\Freeradius\customer;
use Illuminate\Http\Request;

class CustomerDisableController extends Controller
{

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(customer $customer)
    {
        $this->authorize('disable', $customer);

        $customer->status = 'disabled';

        $customer->save();

        switch ($customer->connection_type) {
            case 'PPPoE':
                // from active || suspended
                CustomersRadPasswordController::updateOrCreate($customer);
                // disconnect
                PPPCustomerDisconnectController::disconnect($customer);
                break;
        }

        return 'Customer has been disabled successfully';
    }
}
