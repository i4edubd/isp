<?php

namespace App\Http\Controllers\CustomerPayment;

use App\Http\Controllers\Controller;
use App\Models\customer_bill;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DueNotifyProfileController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $operator = $request->user();

        $bills = customer_bill::where('operator_id', $operator->id)
            ->select('due_date')
            ->distinct()
            ->get()
            ->sortBy('due_date');

        $to_j = Carbon::now()->format('j');
        $to_n  = Carbon::now()->format('n');

        $payment_dates = [];

        while ($bill = $bills->shift()) {
            $due_j = date_format(date_create($bill->due_date), 'j');
            $due_n = date_format(date_create($bill->due_date), 'n');
            if ($due_n == $to_n) {
                if ($due_j >= $to_j) {
                    $where = [
                        ['operator_id', '=', $operator->id],
                        ['due_date', '=', $bill->due_date],
                    ];
                    $payment_dates[$bill->due_date] = customer_bill::where($where)->count();
                }
            }
        }

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.due-notifier-profile', [
                    'payment_dates' => $payment_dates,
                ]);
                break;

            case 'operator':
                return view('admins.operator.due-notifier-profile', [
                    'payment_dates' => $payment_dates,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.due-notifier-profile', [
                    'payment_dates' => $payment_dates,
                ]);
                break;
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
            'due_date' => 'required',
        ]);

        $due_date = date_format(date_create($request->due_date), 'j');

        return redirect()->route('due-date.due-notifier.create', ['due_date' => $due_date]);
    }
}
