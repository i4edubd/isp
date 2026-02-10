<?php

namespace App\Http\Controllers;

use App\Models\max_subscription_payment;
use App\Models\operator;
use Illuminate\Http\Request;

class MaxSubscriptionPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $max_payments = max_subscription_payment::all();

        return view('admins.developer.max-subscription-payments', [
            'max_payments' => $max_payments,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $operators = operator::where('role', 'group_admin')->get();

        return view('admins.developer.subscription-max-create', [
            'operators' => $operators,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $max_payment = new max_subscription_payment();
        $max_payment->operator_id = $request->operator_id;
        $max_payment->amount = $request->amount;
        $max_payment->save();

        return redirect()->route('max_subscription_payments.index')->with('success', 'Set Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\max_subscription_payment  $max_subscription_payment
     * @return \Illuminate\Http\Response
     */
    public function show(max_subscription_payment $max_subscription_payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\max_subscription_payment  $max_subscription_payment
     * @return \Illuminate\Http\Response
     */
    public function edit(max_subscription_payment $max_subscription_payment)
    {
        return view('admins.developer.max_subscription_payment_edit', [
            'max_subscription_payment' => $max_subscription_payment,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\max_subscription_payment  $max_subscription_payment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, max_subscription_payment $max_subscription_payment)
    {
        $max_subscription_payment->amount = $request->amount;
        $max_subscription_payment->save();
        return redirect()->route('max_subscription_payments.index')->with('success', 'Set Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\max_subscription_payment  $max_subscription_payment
     * @return \Illuminate\Http\Response
     */
    public function destroy(max_subscription_payment $max_subscription_payment)
    {
        $max_subscription_payment->delete();
        return redirect()->route('max_subscription_payments.index')->with('success', 'Deleted Successfully!');
    }
}
