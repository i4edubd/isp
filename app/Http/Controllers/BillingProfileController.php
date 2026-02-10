<?php

namespace App\Http\Controllers;

use App\Models\billing_profile;
use App\Models\Freeradius\customer;
use Illuminate\Http\Request;

class BillingProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();

        $profiles = billing_profile::where('mgid', $request->user()->id)->get();

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.billing-profiles', [
                    'profiles' => $profiles,
                ]);
                break;

            case 'operator':
                return view('admins.operator.billing-profiles', [
                    'profiles' => $operator->billing_profiles,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.billing-profiles', [
                    'profiles' => $operator->billing_profiles,
                ]);
                break;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\billing_profile  $billing_profile
     * @return \Illuminate\Http\Response
     */
    public function edit(billing_profile $billing_profile)
    {
        return view('admins.group_admin.billing-profiles-edit', [
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
            'profile_name' => 'required|string|max:128',
        ]);

        if ($request->filled('minimum_validity')) {
            $billing_profile->minimum_validity = $request->minimum_validity;
        }

        if ($request->filled('billing_due_date')) {
            $billing_due_date =  date_format(date_create($request->billing_due_date), 'j');
            $billing_profile->billing_due_date = $billing_due_date;
        }

        $billing_profile->profile_name = $request->profile_name;

        if ($request->filled('auto_bill')) {
            $billing_profile->auto_bill = $request->auto_bill;
        }

        if ($request->filled('auto_lock')) {
            $billing_profile->auto_lock = $request->auto_lock;
        }

        $billing_profile->save();

        return redirect()->route('billing_profiles.index')->with('success', 'Billing Profile updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\billing_profile  $billing_profile
     * @return \Illuminate\Http\Response
     */
    public function destroy(billing_profile $billing_profile)
    {
        $customer_count = customer::where('billing_profile_id', $billing_profile->id)->count();

        if ($customer_count) {
            return view('admins.group_admin.billing-profile-delete-exception', [
                'billing_profile' => $billing_profile,
                'customer_count' => $customer_count,
            ]);
        }

        $billing_profile->delete();

        return redirect()->route('billing_profiles.index')->with('success', 'Billing Profile has been deleted successfully!');
    }

    /**
     * Default Profile
     *
     * @return \App\Models\billing_profile
     */
    public static function defaultProfile()
    {
        return billing_profile::make([
            'id' => 0,
            'billing_type' => 'Monthly',
            'minimum_validity' => 30,
            'profile_name' => '',
            'billing_due_date' => 5,
            'auto_bill' => 'no',
            'auto_lock' => 'no',
            'cycle_ends_with_month' => 'no',
        ]);
    }
}
