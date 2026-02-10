<?php

namespace App\Http\Controllers;

use App\Models\subscription_bill;
use App\Models\operator;
use App\Models\customer_count;
use App\Models\max_subscription_payment;
use App\Models\subscription_discount;
use Illuminate\Http\Request;

class SubscriptionBillController extends Controller
{


    /**
     * Generate Subscription Bill
     */

    public static function generateBill()
    {

        $operator_where = [];

        # 1 Only Group Admin
        $operator_where[] = ['role', '=', 'group_admin'];

        # 2 Paid Operator
        $operator_where[] = ['subscription_type', '=', 'Paid'];

        # 3 provisioning status
        $operator_where[] = ['provisioning_status', '=', '2'];

        # get group admins
        $operators = operator::where($operator_where)->get();

        foreach ($operators as $operator) {

            self::generateOperatorBill($operator->id);
        }
    }


    /**
     * Generate Operator Subscription Bill
     */

    public static function generateOperatorBill($mgid)
    {

        $operator = operator::findOrFail($mgid);

        if ($operator->role !== 'group_admin') {
            return 0;
        }

        if ($operator->subscription_type !== 'Paid') {
            return 0;
        }

        if ($operator->provisioning_status != 2) {
            return 0;
        }

        $avg_coustomer = customer_count::where('mgid', $operator->id)->avg('customer_count');

        $user_count = round($avg_coustomer);

        $amount = SubscriptionPolicyController::getBillAmount($operator);

        // max payment
        $max_payment = max_subscription_payment::where('operator_id', $operator->id)->firstOr(function () {
            return
                max_subscription_payment::make([
                    'id' => 0,
                    'operator_id' => 0,
                    'amount' => 0,
                ]);
        });

        $max_amount = $max_payment->amount;

        if ($max_amount > 0) {
            if ($amount > $max_amount) {
                $amount = $max_amount;
            }
        }

        //generate bill
        $subscription_bill = new subscription_bill();
        $subscription_bill->sid = $operator->sid;
        $subscription_bill->mgid = $operator->id;
        $subscription_bill->operator_name = $operator->name;
        $subscription_bill->operator_email = $operator->email;
        $subscription_bill->user_count = $user_count;
        $subscription_bill->amount = $amount;
        $subscription_bill->calculated_price = SubscriptionPolicyController::getCalculatedPrice($operator);
        $subscription_bill->month = date(config('app.month_format'));
        $subscription_bill->year = date(config('app.year_format'));
        $subscription_bill->due_date = date('15-m-Y');
        $subscription_bill->save();

        //delete counts
        customer_count::where('mgid', $operator->id)->delete();
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();

        switch ($operator->role) {
            case 'super_admin':
                $filter = [];
                $filter[] = ['sid', '=', $operator->id];
                if ($request->filled('year')) {
                    $filter[] = ['year', '=', $request->year];
                }
                if ($request->filled('month')) {
                    $filter[] = ['month', '=', $request->month];
                }
                $subscription_bills = subscription_bill::where($filter)->get();
                $total = subscription_bill::where($filter)->sum('amount');
                return view('admins.super_admin.subscription-bills', [
                    'subscription_bills' => $subscription_bills,
                    'total' => $total,
                ]);
                break;
            case 'group_admin':
                $subscription_bills = subscription_bill::where('mgid', $operator->id)->get();
                return view('admins.group_admin.subscription-bills', [
                    'subscription_bills' => $subscription_bills,
                ]);
                break;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboardIndex(Request $request)
    {
        $operator = $request->user();

        $subscription_bills = subscription_bill::where('mgid', $operator->id)->get();
        return view('admins.components.dashboard-subscription-bills', [
            'subscription_bills' => $subscription_bills,
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\subscription_bill  $subscription_bill
     * @return \Illuminate\Http\Response
     */
    public function edit(subscription_bill $subscription_bill)
    {
        return view('admins.super_admin.subscription-bills-edit', [
            'subscription_bill' => $subscription_bill,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\subscription_bill  $subscription_bill
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, subscription_bill $subscription_bill)
    {
        $subscription_bill->amount = $request->amount;
        $subscription_bill->save();
        return redirect()->route('subscription_bills.index')->with('success', 'Bill has been updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\subscription_bill  $subscription_bill
     * @return \Illuminate\Http\Response
     */
    public function destroy(subscription_bill $subscription_bill)
    {
        $subscription_bill->delete();
        return redirect()->route('subscription_bills.index')->with('success', 'Bill has been deleted successfully');
    }
}
