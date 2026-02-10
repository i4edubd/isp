<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\BlackListRemoveController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PgsqlCustomerController;
use App\Jobs\PPPoECustomersRadAttributesUpdateOrCreateJob;
use App\Models\Freeradius\customer;

class PPPoECustomersRadAttributesController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return void
     */
    public static function updateOrCreate(customer $customer)
    {
        PPPoECustomersRadAttributesUpdateOrCreateJob::dispatch($customer)
            ->onConnection('database')
            ->onQueue('pppoe_customers_rad_attributes');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return void
     */
    public static function doUpdateOrCreate(customer $customer)
    {
        #pgsql_customer (single mode)
        PgsqlCustomerController::updateOrCreate($customer);

        #radcheck
        CustomersRadPasswordController::updateOrCreate($customer); # (dual mode)
        PPPoECustomersExpirationController::updateOrCreate($customer); # (single mode)

        #radreply
        CustomersRadLimitController::updateOrCreate($customer); # (single mode)
        PPPoECustomersMikrotikGroupController::updateOrCreate($customer); # (single mode)
        PPPoECustomersFramedIPAddressController::updateOrCreate($customer); # (dual mode)

        # remove black list
        BlackListRemoveController::update($customer); # (single mode)
    }
}
