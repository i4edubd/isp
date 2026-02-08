<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CustomerPayment\CustomerPaymentAccountingController;
use App\Models\customer_payment;
use App\Models\operator;
use Illuminate\Http\Request;

class ManagersCashCollectionAccountingController extends Controller
{
    /**
     * Do managers cash collection accounting
     *
     * @param  \App\Models\customer_payment $customer_payment
     * @return bool
     */
    public static function doAccounts(customer_payment $customer_payment)
    {
        if ($customer_payment->type !== 'Cash') {
            return false;
        }

        if ($customer_payment->cash_collector_id == 0) {
            return false;
        }

        $cash_collector = operator::find($customer_payment->cash_collector_id);

        if (!$cash_collector) {
            return false;
        }

        if ($cash_collector->role !== 'manager') {
            return false;
        }

        $account_provider = $cash_collector;
        $account_owner = operator::find($customer_payment->operator_id);
        $amount = $customer_payment->store_amount;

        $ac = new CustomerPaymentAccountingController();
        $ac->storeCashIn($account_provider, $account_owner, $customer_payment, $amount);
        return true;
    }
}
