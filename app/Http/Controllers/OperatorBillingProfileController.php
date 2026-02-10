<?php

namespace App\Http\Controllers;

use App\Models\billing_profile;
use App\Models\billing_profile_operator;
use App\Models\operator;
use Illuminate\Http\Request;

class OperatorBillingProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function index(operator $operator)
    {
        return view('admins.group_admin.operator-billing-profiles', [
            'operator' => $operator,
            'profiles' => $operator->billing_profiles,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function create(operator $operator)
    {
        $assigned_profiles =  $operator->billing_profiles;

        $assigned_profile_ids = [];

        foreach ($assigned_profiles as $assigned_profile) {
            $assigned_profile_ids[] = $assigned_profile->id;
        }

        $profiles = billing_profile::where('mgid', $operator->mgid)
            ->whereNotIn('id', $assigned_profile_ids)
            ->get();

        return view('admins.group_admin.operator-billing-profiles-create', [
            'operator' => $operator,
            'assigned_profiles' => $assigned_profiles,
            'profiles' => $profiles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, operator $operator)
    {
        if ($request->billing_profile_ids) {

            //delete previous permissions
            billing_profile_operator::where('operator_id', $operator->id)->delete();

            //insert new permissions
            foreach ($request->billing_profile_ids as $billing_profile_id) {
                $operator_billing_profile = new billing_profile_operator();
                $operator_billing_profile->operator_id = $operator->id;
                $operator_billing_profile->billing_profile_id = $billing_profile_id;
                $operator_billing_profile->save();
            }

            if (MinimumConfigurationController::hasPendingConfig($request->user())) {
                return redirect()->route('configuration.next', ['operator' => $request->user()->id]);
            }

            return redirect()->route('operators.index')->with('success', 'Billing Profiles has been assigned successfully!');
        } else {

            //delete previous permissions
            billing_profile_operator::where('operator_id', $operator->id)->delete();

            if (MinimumConfigurationController::hasPendingConfig($request->user())) {
                return redirect()->route('configuration.next', ['operator' => $request->user()->id]);
            }

            return redirect()->route('operators.index')->with('error', 'No Billing Profiles Selected!');
        }
    }
}
