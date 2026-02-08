<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Freeradius\customer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomerTimeLimitController extends Controller
{

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(customer $customer)
    {
        $this->authorize('editSpeedLimit', $customer);

        return view('admins.group_admin.customer-time-limit-edit', [
            'customer' => $customer,
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
        $request->validate([
            'validity' => 'required|integer',
        ]);

        if ($request->validity > 0) {
            $package_expired_at = Carbon::now(getTimeZone($customer->operator_id))->addDays($request->validity)->isoFormat(config('app.expiry_time_format'));
        } else {
            $package_expired_at = Carbon::now(getTimeZone($customer->operator_id))->isoFormat(config('app.expiry_time_format'));
        }

        $customer->status = 'active';
        $customer->package_expired_at = $package_expired_at;
        $customer->exptimestamp = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($customer->operator_id), 'en')->timestamp;
        $customer->save();

        HotspotCustomersExpirationController::updateOrCreate($customer);

        return redirect()->route('customers.index')->with('success', 'Time Limit has been Extended successfully');
    }
}
