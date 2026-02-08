<?php

namespace App\Http\Controllers;

use App\Models\complain_ledger;
use App\Models\customer_complain;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ComplainLedgerController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public static function storeComment(customer_complain $customer_complain, complain_ledger $complain_ledger)
    {

        if ($customer_complain->ledger_head == 0) {
            $days_diff = Carbon::createFromFormat(config('app.date_time_format'), $customer_complain->start_time, config('app.timezone'))->diffInDays(Carbon::now(config('app.timezone')));
            $hour_diff = Carbon::createFromFormat(config('app.date_time_format'), $customer_complain->start_time, config('app.timezone'))->diffInHours(Carbon::now(config('app.timezone')));
            $minute_diff = Carbon::createFromFormat(config('app.date_time_format'), $customer_complain->start_time, config('app.timezone'))->diffInMinutes(Carbon::now(config('app.timezone')));
            $seconds_diff = Carbon::createFromFormat(config('app.date_time_format'), $customer_complain->start_time, config('app.timezone'))->diffInSeconds(Carbon::now(config('app.timezone')));

            $comment = "After Receiving the complaint, After $days_diff days $hour_diff hours $minute_diff minute";

            $complain_ledger->comment = $comment;
            $complain_ledger->diff_in_seconds = $seconds_diff;
            $complain_ledger->save();
        } else {
            $previous_ledger = complain_ledger::find($customer_complain->ledger_head);

            $days_diff = Carbon::createFromFormat(config('app.date_time_format'), $previous_ledger->start_time, config('app.timezone'))->diffInDays(Carbon::now(config('app.timezone')));
            $hour_diff = Carbon::createFromFormat(config('app.date_time_format'), $previous_ledger->start_time, config('app.timezone'))->diffInHours(Carbon::now(config('app.timezone')));
            $minute_diff = Carbon::createFromFormat(config('app.date_time_format'), $previous_ledger->start_time, config('app.timezone'))->diffInMinutes(Carbon::now(config('app.timezone')));
            $seconds_diff = Carbon::createFromFormat(config('app.date_time_format'), $previous_ledger->start_time, config('app.timezone'))->diffInSeconds(Carbon::now(config('app.timezone')));

            $comment = "Then after $days_diff days $hour_diff hours $minute_diff minute";

            $complain_ledger->comment = $comment;
            $complain_ledger->diff_in_seconds = $seconds_diff;
            $complain_ledger->save();
        }

        return 0;
    }
}
