<?php

namespace App\Http\Controllers;

use App\Models\account;
use App\Models\cash_out;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExchangeAccountBalanceController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\account  $account
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, account $account)
    {
        if ($request->user()->id == $account->account_owner) {
            $previous_url = route('accounts.receivable');
            $breadcrumb_label = 'Accounts Receivable';
            $activated_link = 1;
        }

        if ($request->user()->id == $account->account_provider) {
            $previous_url = route('accounts.payable');
            $breadcrumb_label = 'Accounts Payable';
            $activated_link = 2;
        }

        $this->authorize('exchange', $account);

        $where = [
            ['account_provider', '=', $account->account_owner],
            ['account_owner', '=', $account->account_provider],
        ];

        $exchange_account = account::where($where)->first();

        $balance_array = [$account->balance, $exchange_account->balance];

        sort($balance_array);

        $exchange_amount = $balance_array[0];

        switch ($request->user()->role) {

            case 'group_admin':
                return view('admins.group_admin.exchange-account-balance', [
                    'account' => $account,
                    'exchange_account' => $exchange_account,
                    'exchange_amount' => $exchange_amount,
                    'previous_url' => $previous_url,
                    'breadcrumb_label' => $breadcrumb_label,
                    'activated_link' => $activated_link,
                ]);
                break;

            case 'operator':
                return view('admins.operator.exchange-account-balance', [
                    'account' => $account,
                    'exchange_account' => $exchange_account,
                    'exchange_amount' => $exchange_amount,
                    'previous_url' => $previous_url,
                    'breadcrumb_label' => $breadcrumb_label,
                    'activated_link' => $activated_link,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.exchange-account-balance', [
                    'account' => $account,
                    'exchange_account' => $exchange_account,
                    'exchange_amount' => $exchange_amount,
                    'previous_url' => $previous_url,
                    'breadcrumb_label' => $breadcrumb_label,
                    'activated_link' => $activated_link,
                ]);
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\account  $account
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, account $account)
    {
        $this->authorize('exchange', $account);

        $where = [
            ['account_provider', '=', $account->account_owner],
            ['account_owner', '=', $account->account_provider],
        ];

        $exchange_account = account::where($where)->first();

        $balance_array = [$account->balance, $exchange_account->balance];

        sort($balance_array);

        $exchange_amount = $balance_array[0];

        // account
        $cash_out = new cash_out();
        $cash_out->account_id = $account->id;
        $cash_out->transaction_code = 6;
        $cash_out->transaction_id = $exchange_account->id;
        $cash_out->name = $exchange_account->owner->name;
        $cash_out->username = $exchange_account->owner->name;
        $cash_out->amount = $exchange_amount;
        $cash_out->date = date(config('app.date_format'));
        $cash_out->old_balance = $account->balance;
        $cash_out->new_balance = $account->balance - $exchange_amount;
        $cash_out->month = date(config('app.month_format'));
        $cash_out->year = date(config('app.year_format'));
        $cash_out->note = "Exchange Money with " . $exchange_account->owner->name;
        $cash_out->save();

        DB::transaction(function () use ($account, $exchange_amount) {
            $the_account = account::lockForUpdate()->find($account->id);
            $the_account->balance = $the_account->balance - $exchange_amount;
            $the_account->save();
        });

        // exchange account
        $cash_out = new cash_out();
        $cash_out->account_id = $exchange_account->id;
        $cash_out->transaction_code = 6;
        $cash_out->transaction_id = $account->id;
        $cash_out->name = $account->owner->name;
        $cash_out->username = $account->owner->name;
        $cash_out->amount = $exchange_amount;
        $cash_out->date = date(config('app.date_format'));
        $cash_out->old_balance = $exchange_account->balance;
        $cash_out->new_balance = $exchange_account->balance - $exchange_amount;
        $cash_out->month = date(config('app.month_format'));
        $cash_out->year = date(config('app.year_format'));
        $cash_out->note = "Exchange Money with " . $account->owner->name;
        $cash_out->save();

        DB::transaction(function () use ($exchange_account, $exchange_amount) {
            $the_account = account::lockForUpdate()->find($exchange_account->id);
            $the_account->balance = $the_account->balance - $exchange_amount;
            $the_account->save();
        });

        return redirect()->route('accounts.payable')->with('success', 'Exchange Saved Successfully!');
    }
}
