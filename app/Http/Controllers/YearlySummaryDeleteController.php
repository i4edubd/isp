<?php

namespace App\Http\Controllers;

use App\Models\account;
use App\Models\operator;
use App\Models\yearly_card_distributor_payment;
use App\Models\yearly_cash_in;
use App\Models\yearly_cash_out;
use App\Models\yearly_expense;
use App\Models\yearly_operators_income;
use Illuminate\Http\Request;

class YearlySummaryDeleteController extends Controller
{

    /**
     * Delete Yearly Summary for the operator
     *
     * @param  \App\Models\operator  $operator
     * @return int
     */
    public static function operatorSummary(operator $operator)
    {
        // yearly_card_distributor_payments
        yearly_card_distributor_payment::where('operator_id', $operator->id)->delete();

        // yearly_expenses
        yearly_expense::where('operator_id', $operator->id)->delete();

        // yearly_operators_incomes
        yearly_operators_income::where('operator_id', $operator->id)->delete();

        $accounts = account::where('account_provider', $operator->id)
            ->orWhere('account_owner', $operator->id)->get();

        // yearly_cash_ins
        foreach ($accounts as $account) {
            yearly_cash_in::where('account_id', $account->id)->delete();
        }

        // yearly_cash_outs
        foreach ($accounts as $account) {
            yearly_cash_out::where('account_id', $account->id)->delete();
        }

        return 0;
    }

    /**
     * purge Yearly Summary regularly
     *
     * @return int
     */
    public static function purge()
    {
        yearly_card_distributor_payment::where('created_at', '<', now()->subYears(10)->format('Y-m-d H:i:s'))->delete();
        yearly_expense::where('created_at', '<', now()->subYears(10)->format('Y-m-d H:i:s'))->delete();
        yearly_operators_income::where('created_at', '<', now()->subYears(10)->format('Y-m-d H:i:s'))->delete();
        yearly_cash_in::where('created_at', '<', now()->subYears(10)->format('Y-m-d H:i:s'))->delete();
        yearly_cash_out::where('created_at', '<', now()->subYears(10)->format('Y-m-d H:i:s'))->delete();
        return 0;
    }
}
