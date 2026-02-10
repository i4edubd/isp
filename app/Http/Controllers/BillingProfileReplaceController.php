<?php

namespace App\Http\Controllers;

use App\Jobs\CustomerBillingProfileUpdateJob;
use App\Models\billing_profile;
use App\Models\Freeradius\customer;
use Illuminate\Http\Request;

class BillingProfileReplaceController extends Controller
{

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\billing_profile  $billing_profile
     * @return \Illuminate\Http\Response
     */
    public function edit(billing_profile $billing_profile)
    {
        $billing_profiles = billing_profile::where('mgid', $billing_profile->mgid)->get();

        $profiles = $billing_profiles->except($billing_profile->id);

        return view('admins.group_admin.billing-profiles-replace', [
            'profiles' => $profiles,
            'billing_profile' => $billing_profile,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\billing_profile  $billing_profile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, billing_profile $billing_profile)
    {
        $request->validate([
            'billing_profile_id' => 'required'
        ]);

        $new_billing_profile = billing_profile::findOrFail($request->billing_profile_id);

        $customers = customer::where('billing_profile_id', $billing_profile->id)->get();

        foreach ($customers as $customer) {
            CustomerBillingProfileUpdateJob::dispatch($customer, $new_billing_profile, 0)
                ->onConnection('database')
                ->onQueue('default');
        }

        return redirect()->route('billing_profiles.index')->with('success', 'Billing profile has been replaced successfully');
    }
}
