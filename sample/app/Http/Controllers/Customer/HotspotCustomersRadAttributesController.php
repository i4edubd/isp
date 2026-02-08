<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PgsqlCustomerController;
use App\Jobs\HotspotCustomersRadAttributesUpdateOrCreateJob;
use App\Models\Freeradius\customer;
use Illuminate\Http\Request;

class HotspotCustomersRadAttributesController extends Controller
{

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return void
     */
    public static function updateOrCreate(customer $customer)
    {
        HotspotCustomersRadAttributesUpdateOrCreateJob::dispatch($customer)
            ->onConnection('database')
            ->onQueue('hotspot_customers_rad_attributes');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return void
     */
    public static function doUpdateOrCreate(customer $customer)
    {
        #pgsql_customer
        PgsqlCustomerController::updateOrCreate($customer);

        #radcheck
        CustomersRadPasswordController::updateOrCreate($customer);
        HotspotCustomersExpirationController::updateOrCreate($customer);

        #radreply
        CustomersRadLimitController::updateOrCreate($customer);
    }
}
