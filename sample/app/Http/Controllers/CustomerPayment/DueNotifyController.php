<?php

namespace App\Http\Controllers\CustomerPayment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SmsGenerator;
use App\Jobs\NotifyDues;
use App\Models\customer_bill;
use App\Models\event_sms;
use Illuminate\Http\Request;

class DueNotifyController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $due_date
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, int $due_date)
    {
        $operator = $request->user();

        $date = date($due_date . '-m-Y');

        $date = date_format(date_create($date), config('app.date_format'));

        $where = [
            ['operator_id', '=', $operator->id],
            ['due_date', '=', $date],
        ];

        $bills_count = customer_bill::where($where)->count();

        $event_sms = event_sms::where('event', 'DUE_NOTICE')
            ->where('operator_id', $operator->id)
            ->firstOr(function () {
                return event_sms::where('event', 'DUE_NOTICE')
                    ->where('operator_id', 0)
                    ->first();
            });
        $sms = SmsGenerator::getSMS($request->user(), $event_sms);

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.due-notifier', [
                    'bills_count' => $bills_count,
                    'due_date' => $due_date,
                    'date' => $date,
                    'event_sms' => $event_sms,
                    'sms' => $sms,
                ]);
                break;

            case 'operator':
                return view('admins.operator.due-notifier', [
                    'bills_count' => $bills_count,
                    'due_date' => $due_date,
                    'date' => $date,
                    'event_sms' => $event_sms,
                    'sms' => $sms,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.due-notifier', [
                    'bills_count' => $bills_count,
                    'due_date' => $due_date,
                    'date' => $date,
                    'event_sms' => $event_sms,
                    'sms' => $sms,
                ]);
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $due_date
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, int $due_date)
    {

        $operator = $request->user();

        $date = date($due_date . '-m-Y');

        $date = date_format(date_create($date), config('app.date_format'));

        $where = [
            ['operator_id', '=', $operator->id],
            ['due_date', '=', $date],
        ];

        $bills_count = customer_bill::where($where)->get()->count();

        if ($bills_count) {
            NotifyDues::dispatch($operator->id, $due_date)
                ->onConnection('database')
                ->onQueue('default');
        }

        return redirect()->route('sms_histories.index')->with('success', 'Wait a minute, work is in progress!');
    }
}
