<?php

namespace App\Http\Controllers;

use App\Models\cash_out;
use App\Models\account;
use App\Models\pending_transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashOutController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(pending_transaction $pending_transaction)
    {

        $account = account::findOrFail($pending_transaction->account_id);

        if ($pending_transaction->amount > $account->balance) {
            $pending_transaction->delete();
            return redirect()->route('pending_transactions.index')->with('error', 'Insufficient balance!');
        }

        //store cash out
        $cash_out = new cash_out();
        $cash_out->account_id = $account->id;
        $cash_out->transaction_code = 3;
        $cash_out->transaction_id = 0;
        $cash_out->name = $account->provider->name;
        $cash_out->username = $account->provider->email;
        $cash_out->amount = $pending_transaction->amount;
        $cash_out->date = date(config('app.date_format'));
        $cash_out->old_balance = $account->balance;
        $cash_out->new_balance = $account->balance - $pending_transaction->amount;
        $cash_out->month = date(config('app.month_format'));
        $cash_out->year = date(config('app.year_format'));
        $cash_out->note = $pending_transaction->note;
        $cash_out->save();
        //update balance

        DB::transaction(function () use ($account, $pending_transaction) {
            $the_account = account::lockForUpdate()->find($account->id);
            $the_account->balance = $the_account->balance - $pending_transaction->amount;
            $the_account->save();
        });

        //delete pending transactions
        $pending_transaction->delete();

        // return view
        return redirect()->route('pending_transactions.index')->with('success', 'Transaction Saved successfully!');
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\cash_out  $cash_out
     * @return \Illuminate\Http\Response
     */
    public function show(cash_out $cash_out)
    {
        $transaction = $cash_out->transaction;
        if ($transaction == false) {
            return "No Details";
        }
        return view('admins.components.cash_out_details', [
            'transaction' => $transaction,
        ]);
    }
}
