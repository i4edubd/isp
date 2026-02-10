<?php

namespace App\Http\Controllers;

use App\Models\complain_comment;
use App\Models\complain_ledger;
use App\Models\customer_complain;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ComplainCommentController extends Controller
{


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\customer_complain  $customer_complain
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, customer_complain $customer_complain)
    {
        $request->validate([
            'comment' => 'required',
        ]);

        if ($request->user()) {
            $operator_id = $request->user()->id;
        } else {
            $operator_id = 0;
        }

        if ($request->filled('done')) {
            $customer_complain->status = "Done";
            $customer_complain->is_active = 0;
            $customer_complain->stop_time = date(config('app.date_time_format'));
            $customer_complain->diff_in_seconds =  Carbon::createFromFormat(config('app.date_time_format'), $customer_complain->start_time, config('app.timezone'))->diffInSeconds(Carbon::now(config('app.timezone')));
            $customer_complain->save();

            $complain_ledger = new complain_ledger();
            $complain_ledger->complain_id = $customer_complain->id;
            $complain_ledger->operator_id = $operator_id;
            $complain_ledger->topic = "done";
            $complain_ledger->start_time = date(config('app.date_time_format'));
            $complain_ledger->save();

            ComplainLedgerController::storeComment($customer_complain, $complain_ledger);
        } else {
            $complain_ledger = new complain_ledger();
            $complain_ledger->complain_id = $customer_complain->id;
            $complain_ledger->operator_id = $operator_id;
            $complain_ledger->topic = "comment";
            $complain_ledger->start_time = date(config('app.date_time_format'));
            $complain_ledger->save();

            ComplainLedgerController::storeComment($customer_complain, $complain_ledger);
        }

        $complain_comment = new complain_comment();
        $complain_comment->complain_id = $customer_complain->id;
        $complain_comment->operator_id = $operator_id;
        $complain_comment->comment = $request->comment;
        $complain_comment->comment_time = date(config('app.date_time_format'));
        $complain_comment->save();

        $complain_ledger->comment_id = $complain_comment->id;
        $complain_ledger->save();

        $customer_complain->ledger_head = $complain_ledger->id;
        $customer_complain->save();

        return redirect()->route('customer_complains.show', ['customer_complain' => $customer_complain->id]);
    }
}
