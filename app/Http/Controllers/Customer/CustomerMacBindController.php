<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Freeradius\customer;
use App\Models\Freeradius\radacct;

class CustomerMacBindController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static function create(radacct $radacct)
    {
        $customer = $radacct->customer;

        // was Done!
        if ($customer->mac_bind == 1) {
            return 'Done!';
        }

        // new
        $customer->login_mac_address = $radacct->callingstationid;
        $customer->mac_bind = 1;
        $customer->save();

        RadCallingStationIdController::updateOrCreate($customer);

        return 'Done!';

        return redirect()->route('online_customers.index', ['refresh' => 1])->with('success', 'MAC Bind Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public static function destroy(customer $customer)
    {
        // was Done!
        if ($customer->mac_bind == 0) {
            return 'MAC Bind Removed Successfully';
        }

        // new
        $customer->mac_bind = 0;
        $customer->save();

        RadCallingStationIdController::updateOrCreate($customer);

        return 'MAC Bind Removed Successfully';
    }
}
