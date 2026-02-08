<?php

namespace App\Http\Controllers;

use App\Models\operator;
use App\Models\subscription_bill;
use App\Models\subscription_payment;
use Illuminate\Http\Request;

class SubscriptionPaymentVerificationController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $request->validate([
            'action' => 'required|in:accept,reject',
            'subscription_payment_id' => 'required|numeric',
        ]);

        $subscription_payment = subscription_payment::find($request->subscription_payment_id);

        if ($request->action == 'accept') {
            $subscription_payment->store_amount = $subscription_payment->amount_paid;
            $subscription_payment->pay_status = 'Successful';
            $subscription_payment->save();

            SubscriptionPaymentController::recordIncomes($subscription_payment);

            return redirect()->route('subscription_payments.index')->with('success', 'Payment Successful');
        }

        // subscription_bill
        $group_admin = operator::find($subscription_payment->mgid);

        $subscription_bill = new subscription_bill();
        $subscription_bill->sid = $group_admin->sid;
        $subscription_bill->mgid = $group_admin->id;
        $subscription_bill->operator_name = $group_admin->name;
        $subscription_bill->operator_email = $group_admin->email;
        $subscription_bill->user_count = $subscription_payment->user_count;
        $subscription_bill->amount = $subscription_payment->amount_paid;
        $subscription_bill->calculated_price = $subscription_payment->amount_paid;
        $subscription_bill->month = $subscription_payment->month;
        $subscription_bill->year = $subscription_payment->year;
        $subscription_bill->due_date = $subscription_payment->date;
        $subscription_bill->save();

        // delete subscription_payment
        $subscription_payment->delete();

        return redirect()->route('subscription_payments.index')->with('success', 'Payment Rejected');
    }
}
