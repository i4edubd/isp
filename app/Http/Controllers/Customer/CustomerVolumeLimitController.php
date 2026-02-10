<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Freeradius\customer;
use Illuminate\Http\Request;

class CustomerVolumeLimitController extends Controller
{

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(customer $customer)
    {
        return view('admins.group_admin.customer-volume-limit-edit', [
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
            'volume_limit' => ['required', 'integer'],
        ]);

        $customer->status = 'active';
        $customer->total_octet_limit = $request->volume_limit * 1000 * 1000 * 1000;
        $customer->save();

        CustomersRadLimitController::updateOrCreate($customer);

        return redirect()->route('customers.index')->with('success', 'Volume Limit Edited successfully');
    }
}
