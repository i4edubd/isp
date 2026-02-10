<?php

namespace App\Http\Controllers;

use App\Models\billing_profile;
use App\Models\temp_customer;
use Illuminate\Http\Request;

class TempCustomerBillingProfileController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\temp_customer  $temp_customer
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, temp_customer $temp_customer)
    {
        $operator = $request->user();

        $billing_profiles = $operator->billing_profiles;

        $selected_profile = BillingHelper::getDefaultBillingProfile($request->user());

        if ($selected_profile) {
            $billing_profiles = $billing_profiles->except([$selected_profile->id]);
        }

        switch ($temp_customer->connection_type) {

            case 'PPPoE':
                break;

            case 'Hotspot':
                $temp_customer->billing_type = 'Daily';
                $temp_customer->save();
                return redirect()->route('temp_customer.tech_info.create', ['temp_customer' => $temp_customer->id]);
                break;

            case 'StaticIp':
            case 'Other':
                $billing_profiles = $billing_profiles->filter(function ($billing_profile) {
                    return $billing_profile->billing_type != 'Daily';
                });
                break;
        }

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.temp-customer-billing-profile', [
                    'temp_customer' => $temp_customer,
                    'billing_profiles' => $billing_profiles,
                    'selected_profile' => $selected_profile,
                ]);
                break;

            case 'operator':
                return view('admins.operator.temp-customer-billing-profile', [
                    'temp_customer' => $temp_customer,
                    'billing_profiles' => $billing_profiles,
                    'selected_profile' => $selected_profile,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.temp-customer-billing-profile', [
                    'temp_customer' => $temp_customer,
                    'billing_profiles' => $billing_profiles,
                    'selected_profile' => $selected_profile,
                ]);
                break;

            case  'manager':
                return view('admins.manager.temp-customer-billing-profile', [
                    'temp_customer' => $temp_customer,
                    'billing_profiles' => $billing_profiles,
                    'selected_profile' => $selected_profile,
                ]);
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\temp_customer  $temp_customer
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, temp_customer $temp_customer)
    {
        $request->validate([
            'billing_profile_id' => 'required|numeric'
        ]);

        $billing_profile = billing_profile::findOrFail($request->billing_profile_id);

        $temp_customer->billing_profile_id = $billing_profile->id;

        switch ($billing_profile->billing_type) {
            case 'Daily':
                $temp_customer->billing_type = 'Daily';
                $temp_customer->save();
                break;

            case 'Free':
                $temp_customer->billing_type = 'Free';
                $temp_customer->save();
                break;

            default:
                $temp_customer->billing_type = 'Monthly';
                $temp_customer->save();
                break;
        }

        return redirect()->route('temp_customer.tech_info.create', ['temp_customer' => $temp_customer->id]);
    }
}
