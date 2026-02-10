<?php

namespace App\Http\Controllers\Sms;

use App\Http\Controllers\Controller;
use App\Models\operator;
use App\Models\sms_balance_history;
use App\Models\sms_payment;
use Illuminate\Http\Request;

class SmsBalanceHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();

        $where = [];

        $where[] = ['operator_id', '=', $operator->id];

        // year
        if ($request->filled('year')) {
            $where[] = ['year', '=', $request->year];
        }

        // month
        if ($request->filled('month')) {
            $where[] = ['month', '=', $request->month];
        }

        $sms_balance_histories = sms_balance_history::where('operator_id', $operator->id)->get();

        switch ($operator->role) {

            case 'super_admin':
                return view('admins.super_admin.sms_balance_histories', [
                    'sms_balance_histories' => $sms_balance_histories,
                ]);
                break;

            case 'group_admin':
                return view('admins.group_admin.sms_balance_histories', [
                    'sms_balance_histories' => $sms_balance_histories,
                ]);
                break;

            case 'operator':
                return view('admins.operator.sms_balance_histories', [
                    'sms_balance_histories' => $sms_balance_histories,
                ]);
                break;

            case 'sub_operator':
                return view('admins.group_admin.sms_balance_histories', [
                    'sms_balance_histories' => $sms_balance_histories,
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
    public static function store(sms_payment $sms_payment)
    {
        $sms_payment->refresh();

        if ($sms_payment->pay_for == 'balance' && $sms_payment->used == 0) {
            $sms_payment->used = 1;
            $sms_payment->save();
            $operator =  operator::find($sms_payment->operator_id);
            $old_balance = $operator->sms_balance;
            $new_balance = $operator->sms_balance + $sms_payment->store_amount;
            $operator->sms_balance = $new_balance;
            $operator->save();
            sms_balance_history::create([
                'operator_id' => $operator->id,
                'type' => 'in',
                'sms_payment_id' => $sms_payment->id,
                'amount' => $sms_payment->store_amount,
                'old_balance' => $old_balance,
                'new_balance' => $new_balance,
            ]);
        }
        return 1;
    }
}
