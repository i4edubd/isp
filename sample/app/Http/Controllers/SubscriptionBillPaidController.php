<?php

namespace App\Http\Controllers;

use App\Models\subscription_bill;
use App\Models\subscription_payment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SubscriptionBillPaidController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\subscription_bill  $subscription_bill
     * @return \Illuminate\Http\Response
     */
    public function create(subscription_bill $subscription_bill)
    {
        return view('admins.super_admin.subscription-bill-paid', [
            'subscription_bill' => $subscription_bill,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\subscription_bill  $subscription_bill
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, subscription_bill $subscription_bill)
    {
        // delete failed payments
        subscription_payment::where('subscription_bill_id', $subscription_bill->id)->delete();

        // insert payment
        $subscription_payment = new subscription_payment();
        $subscription_payment->mgid = $subscription_bill->mgid;
        $subscription_payment->subscription_bill_id = $subscription_bill->id;
        $subscription_payment->operator_name = $subscription_bill->operator_name;
        $subscription_payment->operator_email = $subscription_bill->operator_email;
        $subscription_payment->user_count = $subscription_bill->user_count;
        $subscription_payment->pay_status = 'Successful';
        $subscription_payment->amount_paid = $subscription_bill->amount;
        $subscription_payment->store_amount = $subscription_bill->amount;
        $subscription_payment->mer_txnid = random_int(1000, 9999) . Carbon::now(config('app.timezone'))->timestamp;
        $subscription_payment->bank_txnid = $request->bank_txnid;
        $subscription_payment->date = date(config('app.date_format'));
        $subscription_payment->week = date(config('app.week_format'));
        $subscription_payment->month = date(config('app.month_format'));
        $subscription_payment->year = date(config('app.year_format'));
        $subscription_payment->save();

        $subscription_bill->delete();

        //Record Incomes
        SubscriptionPaymentController::recordIncomes($subscription_payment);

        //Show Subscription Payments
        return redirect()->route('subscription_payments.index')->with('success', 'Payment Successful');
    }
}
