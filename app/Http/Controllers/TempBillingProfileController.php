<?php

namespace App\Http\Controllers;

use App\Models\billing_profile;
use App\Models\Freeradius\customer;
use App\Models\operator;
use App\Models\temp_billing_profile;
use Illuminate\Http\Request;

class TempBillingProfileController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admins.group_admin.temp-billing-profiles-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'profile_for' => 'required|in:monthly_billing,daily_billing,free_customer'
        ]);

        temp_billing_profile::where('mgid', $request->user()->id)->delete();

        $profile_for = $request->profile_for;

        switch ($profile_for) {
            case 'monthly_billing':
                $temp_billing_profile = new temp_billing_profile();
                $temp_billing_profile->mgid = $request->user()->id;
                $temp_billing_profile->profile_for = $request->profile_for;
                $temp_billing_profile->cycle_ends_with_month = 'no';
                $temp_billing_profile->save();
                return redirect()->route('temp_billing_profiles.edit', ['temp_billing_profile' => $temp_billing_profile->id]);
                break;

            case 'daily_billing':
                $temp_billing_profile = new temp_billing_profile();
                $temp_billing_profile->mgid = $request->user()->id;
                $temp_billing_profile->profile_for = $request->profile_for;
                $temp_billing_profile->save();
                return redirect()->route('temp_billing_profiles.edit', ['temp_billing_profile' => $temp_billing_profile->id]);
                break;

            case 'free_customer':
                $free_profiles_where = [
                    ['mgid', '=', $request->user()->id],
                    ['billing_type', '=', 'Free'],
                ];
                $free_profiles = billing_profile::where($free_profiles_where)->get();

                $free_profiles_count = $free_profiles->count();

                if ($free_profiles_count == 0) {
                    $billing_profile = new billing_profile();
                    $billing_profile->mgid = $request->user()->id;
                    $billing_profile->billing_type = 'Free';
                    $billing_profile->minimum_validity = 0;
                    $billing_profile->profile_name = 'Free Customer';
                    $billing_profile->billing_due_date = 28;
                    $billing_profile->auto_bill = 'no';
                    $billing_profile->auto_lock = 'no';
                    $billing_profile->cycle_ends_with_month = 'no';
                    $billing_profile->save();
                    return redirect()->route('billing_profiles.index')->with('success', 'Billing Profile added successfully');
                }

                if ($free_profiles_count == 1) {
                    foreach ($free_profiles as $free_profile) {
                        $free_profile->billing_type = 'Free';
                        $free_profile->minimum_validity = 0;
                        $free_profile->profile_name = 'Free Customer';
                        $free_profile->billing_due_date = 28;
                        $free_profile->auto_bill = 'no';
                        $free_profile->auto_lock = 'no';
                        $free_profile->cycle_ends_with_month = 'no';
                        $free_profile->save();
                        return redirect()->route('billing_profiles.index')->with('success', 'Billing Profile saved successfully');
                    }
                }

                if ($free_profiles_count > 1) {
                    $first_free_profile = $free_profiles->first();
                    $first_free_profile->billing_type = 'Free';
                    $first_free_profile->minimum_validity = 0;
                    $first_free_profile->profile_name = 'Free Customer';
                    $first_free_profile->billing_due_date = 28;
                    $first_free_profile->auto_bill = 'no';
                    $first_free_profile->auto_lock = 'no';
                    $first_free_profile->cycle_ends_with_month = 'no';
                    $first_free_profile->save();

                    $free_profiles_except_first = $free_profiles->except([$first_free_profile->id]);

                    foreach ($free_profiles_except_first as $other_profile) {
                        $where = [
                            ['mgid', '=', $request->user()->id],
                            ['billing_profile_id', '=', $other_profile->id],
                        ];
                        customer::where($where)->update(['billing_profile_id' => $first_free_profile->id]);

                        if ($request->user()->can('delete', $other_profile)) {
                            $other_profile->delete();
                        }
                    }
                    return redirect()->route('billing_profiles.index')->with('success', 'Billing Profiles saved successfully');
                }
                break;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\temp_billing_profile  $temp_billing_profile
     * @return \Illuminate\Http\Response
     */
    public function edit(temp_billing_profile $temp_billing_profile)
    {
        $this->authorize('update', $temp_billing_profile);

        switch ($temp_billing_profile->profile_for) {
            case 'daily_billing':
                return view('admins.group_admin.temp-billing-profiles-edit-for-daily-billing', [
                    'temp_billing_profile' => $temp_billing_profile,
                ]);
                break;

            case 'monthly_billing':
                return view('admins.group_admin.temp-billing-profiles-edit', [
                    'temp_billing_profile' => $temp_billing_profile,
                ]);
                break;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\temp_billing_profile  $temp_billing_profile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, temp_billing_profile $temp_billing_profile)
    {
        $this->authorize('update', $temp_billing_profile);

        switch ($temp_billing_profile->profile_for) {
            case 'daily_billing':
                $request->validate([
                    'minimum_validity' => 'required|numeric',
                ]);
                $temp_billing_profile->minimum_validity = $request->minimum_validity;
                $temp_billing_profile->save();
                return $this->updateOrCreateDailyBillingProfile($temp_billing_profile);
                break;

            case 'monthly_billing':
                $request->validate([
                    'cycle_ends_with_month' => 'required|in:yes,no',
                ]);
                $temp_billing_profile->cycle_ends_with_month = $request->cycle_ends_with_month;
                $temp_billing_profile->save();
                return $this->updateOrCreateMonthlyBillingProfile($temp_billing_profile);
                break;
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\temp_billing_profile  $temp_billing_profile
     * @return void
     */
    public function updateOrCreateDailyBillingProfile(temp_billing_profile $temp_billing_profile)
    {
        $gadmin = operator::findOrFail($temp_billing_profile->mgid);

        $where = [
            ['mgid', '=', $temp_billing_profile->mgid],
            ['billing_type', '=', 'Daily'],
            ['minimum_validity', '=', $temp_billing_profile->minimum_validity],
        ];

        $partial_profiles = billing_profile::where($where)->get();

        $profile_count = $partial_profiles->count();

        if ($profile_count == 0) {
            $billing_profile = new billing_profile();
            $billing_profile->mgid = $temp_billing_profile->mgid;
            $billing_profile->billing_type = 'Daily';
            $billing_profile->minimum_validity = $temp_billing_profile->minimum_validity;
            $billing_profile->profile_name = 'Daily|Minimum : ' . $temp_billing_profile->minimum_validity . ' Days';
            $billing_profile->billing_due_date = $temp_billing_profile->minimum_validity;
            $billing_profile->auto_bill = 'yes';
            $billing_profile->auto_lock = 'yes';
            $billing_profile->cycle_ends_with_month = 'no';
            $billing_profile->save();
        }

        if ($profile_count == 1) {
            foreach ($partial_profiles as $partial_profile) {
                $partial_profile->billing_type = 'Daily';
                $partial_profile->minimum_validity = $temp_billing_profile->minimum_validity;
                $partial_profile->profile_name = 'Daily|Minimum : ' . $temp_billing_profile->minimum_validity . ' Days';
                $partial_profile->billing_due_date = $temp_billing_profile->minimum_validity;
                $partial_profile->auto_bill = 'yes';
                $partial_profile->auto_lock = 'yes';
                $partial_profile->cycle_ends_with_month = 'no';
                $partial_profile->save();
            }
        }

        if ($profile_count > 1) {
            $first_partial_profile = $partial_profiles->first();
            $first_partial_profile->billing_type = 'Daily';
            $first_partial_profile->minimum_validity = $temp_billing_profile->minimum_validity;
            $first_partial_profile->profile_name = 'Daily|Minimum : ' . $temp_billing_profile->minimum_validity . ' Days';
            $first_partial_profile->billing_due_date = $temp_billing_profile->minimum_validity;
            $first_partial_profile->auto_bill = 'yes';
            $first_partial_profile->auto_lock = 'yes';
            $first_partial_profile->cycle_ends_with_month = 'no';
            $first_partial_profile->save();

            $except_first_partial_profiles = $partial_profiles->except($first_partial_profile->id);

            foreach ($except_first_partial_profiles as $other_profile) {
                $where = [
                    ['mgid', '=', $temp_billing_profile->mgid],
                    ['billing_profile_id', '=', $other_profile->id],
                ];
                customer::where($where)->update(['billing_profile_id' => $first_partial_profile->id]);

                if ($gadmin->can('delete', $other_profile)) {
                    $other_profile->delete();
                }
            }
        }

        if (MinimumConfigurationController::hasPendingConfig($gadmin)) {
            return redirect()->route('configuration.next', ['operator' => $gadmin->id]);
        } else {
            return redirect()->route('billing_profiles.index')->with('success', 'Billing Profiles Created Successfully!');
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\temp_billing_profile  $temp_billing_profile
     * @return  \Illuminate\Http\Response
     */
    public function updateOrCreateMonthlyBillingProfile(temp_billing_profile $temp_billing_profile)
    {
        for ($i = 1; $i < 31; $i++) {
            billing_profile::updateOrCreate(
                [
                    'mgid' => $temp_billing_profile->mgid,
                    'billing_type' => 'Monthly',
                    'billing_due_date' => $i,
                ],
                [
                    'minimum_validity' => 30,
                    'profile_name' => null,
                    'auto_bill' => 'yes',
                    'auto_lock' => 'yes',
                    'cycle_ends_with_month' => $temp_billing_profile->cycle_ends_with_month,
                ]
            );
        }

        $madmin = CacheController::getOperator($temp_billing_profile->mgid);

        if (MinimumConfigurationController::hasPendingConfig($madmin)) {
            return redirect()->route('configuration.next', ['operator' => $madmin->id]);
        } else {
            return redirect()->route('billing_profiles.index')->with('success', 'Billing Profiles Created Successfully!');
        }
    }
}
