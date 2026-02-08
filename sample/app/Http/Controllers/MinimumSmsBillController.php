<?php

namespace App\Http\Controllers;

use App\Models\minimum_sms_bill;
use App\Models\operator;
use Illuminate\Http\Request;

class MinimumSmsBillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $minimum_bills = minimum_sms_bill::all();
        return view('admins.developer.minimum-sms-bills', [
            'minimum_bills' => $minimum_bills,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $operators = operator::all();

        return view('admins.developer.minimum-sms-bill-create', [
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
        $minimum_sms_bill = new minimum_sms_bill();
        $minimum_sms_bill->operator_id = $request->operator_id;
        $minimum_sms_bill->amount = $request->amount;
        $minimum_sms_bill->save();

        return redirect()->route('minimum_sms_bills.index')->with('success', 'Minimum SMS Bill setting has been saved');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\minimum_sms_bill  $minimum_sms_bill
     * @return \Illuminate\Http\Response
     */
    public function show(minimum_sms_bill $minimum_sms_bill)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\minimum_sms_bill  $minimum_sms_bill
     * @return \Illuminate\Http\Response
     */
    public function edit(minimum_sms_bill $minimum_sms_bill)
    {
        return view('admins.developer.minimum-sms-bill-edit', [
            'minimum_sms_bill' => $minimum_sms_bill,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\minimum_sms_bill  $minimum_sms_bill
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, minimum_sms_bill $minimum_sms_bill)
    {

        $minimum_sms_bill->amount = $request->amount;
        $minimum_sms_bill->save();

        return redirect()->route('minimum_sms_bills.index')->with('success', 'Minimum SMS bill Setting updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\minimum_sms_bill  $minimum_sms_bill
     * @return \Illuminate\Http\Response
     */
    public function destroy(minimum_sms_bill $minimum_sms_bill)
    {
        $minimum_sms_bill->delete();

        return redirect()->route('minimum_sms_bills.index')->with('success', 'Minimum SMS Bill Setting deleted!');
    }
}
