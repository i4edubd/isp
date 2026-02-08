<?php

namespace App\Http\Controllers\Sms;

use App\Http\Controllers\Controller;
use App\Models\sms_bill;
use App\Models\sms_gateway;
use Illuminate\Http\Request;

class SmsBillController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();
        $merchant = sms_gateway::where('operator_id', $operator->id)->count();
        if ($merchant) {
            if (sms_bill::where('operator_id', $operator->id)->count()) {
                $sms_bills = sms_bill::where('operator_id', $operator->id)->get();
                $merchant = 0;
            } else {
                $sms_bills = sms_bill::where('merchant_id', $operator->id)->get();
            }
        } else {
            $sms_bills = sms_bill::where('operator_id', $operator->id)->get();
        }

        switch ($operator->role) {

            case 'super_admin':
                return view('admins.super_admin.sms-bills', [
                    'sms_bills' => $sms_bills,
                    'merchant' => $merchant,
                ]);
                break;

            case 'group_admin':
                return view('admins.group_admin.sms-bills', [
                    'sms_bills' => $sms_bills,
                    'merchant' => $merchant,
                ]);
                break;

            case 'operator':
                return view('admins.operator.sms-bills', [
                    'sms_bills' => $sms_bills,
                    'merchant' => $merchant,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.sms-bills', [
                    'sms_bills' => $sms_bills,
                    'merchant' => $merchant,
                ]);
                break;
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\sms_bill  $sms_bill
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, sms_bill $sms_bill)
    {

        $operator = $request->user();

        if ($operator->id !== $sms_bill->merchant_id) {
            abort(403);
        }

        switch ($operator->role) {

            case 'super_admin':
                return view('admins.super_admin.sms-bills-edit', [
                    'sms_bill' => $sms_bill,
                ]);
                break;

            case 'group_admin':
                return view('admins.group_admin.sms-bills-edit', [
                    'sms_bill' => $sms_bill,
                ]);
                break;

            case 'operator':
                return view('admins.operator.sms-bills-edit', [
                    'sms_bill' => $sms_bill,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.sms-bills-edit', [
                    'sms_bill' => $sms_bill,
                ]);
                break;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\sms_bill  $sms_bill
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, sms_bill $sms_bill)
    {
        if ($request->user()->id !== $sms_bill->merchant_id) {
            abort(403);
        }

        $sms_bill->sms_cost = $request->sms_cost;
        $sms_bill->save();
        return redirect()->route('sms_bills.index')->with('success', 'Bill Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\sms_bill  $sms_bill
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, sms_bill $sms_bill)
    {
        if ($request->user()->id !== $sms_bill->merchant_id) {
            abort(403);
        }

        $sms_bill->delete();
        return redirect()->route('sms_bills.index')->with('success', 'Bill Deleted Successfully');
    }
}
