<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Freeradius\customer;
use Illuminate\Http\Request;

class CustomerSpeedLimitController extends Controller
{

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(customer $customer)
    {
        return view('admins.group_admin.customer-speed-limit-edit', [
            'customer' => $customer
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, customer $customer)
    {
        //validate
        $request->validate([
            'rate_limit' => ['required', 'integer'],
        ]);

        $customer->status = 'active';
        $customer->rate_limit = $request->rate_limit;
        $customer->save();

        CustomersRadLimitController::updateOrCreate($customer);

        return redirect()->route('customers.index')->with('success', 'Speed Limit has been updated successfully');
    }
}
