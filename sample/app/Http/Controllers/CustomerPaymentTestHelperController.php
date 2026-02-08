<?php

namespace App\Http\Controllers;

use App\Models\account;
use App\Models\cash_in;
use App\Models\cash_out;
use App\Models\customer_payment;
use App\Models\operator;
use Illuminate\Http\Request;

class CustomerPaymentTestHelperController extends Controller
{

    /**
     * Get Account
     *
     * @param  \App\Models\operator  $account_provider
     * @param  \App\Models\operator  $account_owner
     * @return \App\Models\account
     */
    public static function account(operator $account_provider, operator $account_owner)
    {
        $where = [
            ['account_provider', '=', $account_provider->id],
            ['account_owner', '=', $account_owner->id],
        ];

        return account::where($where)->first();
    }

    /**
     * Get Cash In Record
     *
     * @param  \App\Models\account  $account
     * @param  \App\Models\customer_payment  $customer_payment
     * @return  \App\Models\cash_in
     */
    public static function cashIn(account $account, customer_payment $customer_payment)
    {
        $cash_in_where = [
            ['account_id', '=', $account->id],
            ['transaction_id', '=', $customer_payment->id],
        ];

        return cash_in::where($cash_in_where)->first();
    }

    /**
     * Get Cash Out Record
     *
     * @param  \App\Models\account  $account
     * @param  \App\Models\customer_payment  $customer_payment
     * @return \App\Models\cash_out
     */
    public static function cashOut(account $account, customer_payment $customer_payment)
    {

        $cash_out_where = [
            ['account_id', '=', $account->id],
            ['transaction_id', '=', $customer_payment->id],
        ];

        return cash_out::where($cash_out_where)->first();
    }
}
