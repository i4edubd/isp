<?php

namespace App\Http\Controllers;

use App\Models\operator;
use App\Models\subscription_discount;
use Illuminate\Http\Request;

class SubscriptionDiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $discounts = subscription_discount::all();

        return view('admins.developer.subscription-discounts', [
            'discounts' => $discounts,
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

        return view('admins.developer.subscription-discounts-create', [
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
        $discount = new subscription_discount();
        $discount->operator_id = $request->operator_id;
        $discount->amount = $request->amount;
        $discount->save();
        return redirect()->route('subscription_discounts.index')->with('success', 'Discount has been saved successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\subscription_discount  $subscription_discount
     * @return \Illuminate\Http\Response
     */
    public function show(subscription_discount $subscription_discount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\subscription_discount  $subscription_discount
     * @return \Illuminate\Http\Response
     */
    public function edit(subscription_discount $subscription_discount)
    {
        return view('admins.developer.subscription-discounts-edit', [
            'subscription_discount' => $subscription_discount,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\subscription_discount  $subscription_discount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, subscription_discount $subscription_discount)
    {
        $subscription_discount->amount = $request->amount;
        $subscription_discount->save();
        return redirect()->route('subscription_discounts.index')->with('success', 'Discount updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\subscription_discount  $subscription_discount
     * @return \Illuminate\Http\Response
     */
    public function destroy(subscription_discount $subscription_discount)
    {
        $subscription_discount->delete();
        return redirect()->route('subscription_discounts.index')->with('success', 'Discount deleted successfully!');
    }
}
