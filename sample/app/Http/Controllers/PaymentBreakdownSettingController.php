<?php

namespace App\Http\Controllers;

use App\Models\operator;
use Illuminate\Http\Request;

class PaymentBreakdownSettingController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->user()->role == 'group_admin') {
            return view('admins.components.payment-breakdown-setting');
        }
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
            'show_payment_breakdown' => 'required|in:yes,no'
        ]);

        if ($request->user()->role == 'group_admin') {
            operator::where('mgid', $request->user()->id)->update(['show_payment_breakdown' => $request->show_payment_breakdown]);
            return redirect()->route('customer_payments.index')->with('info', 'Settings saved successfully');
        }
    }
}
