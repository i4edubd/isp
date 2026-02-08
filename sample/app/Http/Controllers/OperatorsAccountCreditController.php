<?php

namespace App\Http\Controllers;

use App\Models\account;
use App\Models\cash_in;
use App\Models\operator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperatorsAccountCreditController extends Controller
{

    /**
     * Credit Operators Account
     *
     * @param  \App\Models\operator $operator
     * @param  int $amount
     * @param  string $note
     *
     * @return void
     */
    public static function store(operator $operator, int $amount, ?string $note = "")
    {
        $authorization = 0;

        if ($operator->role === 'operator' || $operator->role === 'sub_operator') {
            $authorization = 1;
        }

        if ($authorization == 0) {
            abort(403);
        }

        $admin = operator::find($operator->gid);

        $where = [
            ['account_provider', '=', $admin->id],
            ['account_owner', '=', $operator->id],
        ];

        // account
        $account = account::where($where)->firstOr(function () use ($admin, $operator) {
            $account = new account();
            $account->account_provider = $admin->id;
            $account->account_owner = $operator->id;
            $account->balance = 0;
            $account->save();
            return $account;
        });

        // cash in
        $cash_in = new cash_in();
        $cash_in->account_id = $account->id;
        $cash_in->transaction_code = 4;
        $cash_in->name = $admin->name;
        $cash_in->username = $admin->email;
        $cash_in->amount = $amount;
        $cash_in->date = date(config('app.date_format'));
        $cash_in->old_balance = $account->balance;
        $cash_in->new_balance = $account->balance + $amount;
        $cash_in->month = date(config('app.month_format'));
        $cash_in->year = date(config('app.year_format'));
        if (strlen($note)) {
            $cash_in->note = $note;
        }
        $cash_in->save();

        // update account balance
        DB::transaction(function () use ($account, $amount) {
            $the_account = account::lockForUpdate()->find($account->id);
            $the_account->balance = $the_account->balance + $amount;
            $the_account->save();
        });
    }
}
