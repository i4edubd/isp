<?php

namespace App\Http\Controllers;

use App\Models\complain_ledger;
use App\Models\customer_complain;
use Illuminate\Http\Request;

class ComplainAcknowledgeController extends Controller
{


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\customer_complain  $customer_complain
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, customer_complain $customer_complain)
    {

        $complain_ledger = new complain_ledger();
        $complain_ledger->complain_id = $customer_complain->id;
        $complain_ledger->operator_id = $request->user()->id;
        $complain_ledger->topic = "acknowledge";
        $complain_ledger->start_time = date(config('app.date_time_format'));
        $complain_ledger->save();

        ComplainLedgerController::storeComment($customer_complain, $complain_ledger);

        $customer_complain->ack_status = 1;
        $customer_complain->ack_by = $request->user()->id;
        $customer_complain->status = "In Progress";
        $customer_complain->ledger_head = $complain_ledger->id;
        $customer_complain->save();

        return redirect()->route('customer_complains.index')->with('success', 'Complaint acknowledged!');
    }
}
