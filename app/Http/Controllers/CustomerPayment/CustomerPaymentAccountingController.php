<?php

namespace App\Http\Controllers\CustomerPayment;

use App\Http\Controllers\BillingHelper;
use App\Http\Controllers\CacheController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\enum\BillingTerms;
use App\Jobs\CustomerPaymentsEmailNotificationJob;
use App\Models\account;
use App\Models\cash_in;
use App\Models\cash_out;
use App\Models\customer_payment;
use App\Models\operator;
use App\Models\operators_income;
use App\Models\payment_gateway;
use Illuminate\Support\Facades\DB;

class CustomerPaymentAccountingController extends Controller
{

    /**
     * Accounts Payable and Accounts Receivable
     *
     * @param  \App\Models\customer_payment  $customer_payment
     * @return \Illuminate\Http\Response
     */
    public function distributePaymet(customer_payment $customer_payment)
    {
        // Has the amount to distribute?
        if ($customer_payment->amount_paid == 0) {
            return 1;
        }

        // package
        $package = CacheController::getPackage($customer_payment->package_id);
        if (!$package) {
            return 0;
        }

        $parent_package = $package->parent_package;
        $master_package = $package->master_package;

        // Who has collected money? Can Collect money for downstream, but cannot for upstream
        if ($customer_payment->type == 'Cash' || $customer_payment->type == 'RechargeCard' || $customer_payment->type == 'adjustment') {
            $payment_collector = operator::findOrFail($customer_payment->operator_id);
        } else {
            $payment_gateway = payment_gateway::findOrFail($customer_payment->payment_gateway_id);
            $payment_collector = operator::findOrFail($payment_gateway->operator_id);
        }

        // payment_owner
        $payment_owner = operator::findOrFail($customer_payment->operator_id);

        // group admin
        $gadmin = operator::findOrFail($payment_owner->gid);

        // master admin
        $madmin = operator::findOrFail($payment_owner->mgid);

        // Purchased Minutes. The standard for calculating accounts.
        $purchased_minutes = BillingHelper::getPurchasedMinutes($customer_payment);
        $validity_period = BillingHelper::getValidityPeriod($purchased_minutes, BillingTerms::INTERVAL_UNIT_MINUTE->value);
        $customer_payment->validity_period = $validity_period;
        $customer_payment->save();

        //If payment Collector is sub_operator (role)
        if ($payment_collector->role == 'sub_operator') {

            // From sub_operator to Operator
            $operator_amount = round(($package->operator_price / $master_package->total_minute) * ($purchased_minutes));
            if ($payment_collector->account_type == 'credit') {
                $account_provider =  $payment_collector;
                $account_owner = $gadmin;
                $this->storeCashIn($account_provider, $account_owner, $customer_payment, $operator_amount);
            } else {
                $account_provider = $gadmin;
                $account_owner = $payment_collector;
                $this->storeCashOut($account_provider, $account_owner, $customer_payment, $operator_amount);
            }

            // From operator/gadmin to madmin
            $admin_amount = round(($parent_package->operator_price / $master_package->total_minute) * ($purchased_minutes));
            if ($gadmin->account_type == 'credit') {
                $account_provider =  $gadmin;
                $account_owner = $madmin;
                $this->storeCashIn($account_provider, $account_owner, $customer_payment, $admin_amount);
            } else {
                $account_provider = $madmin;
                $account_owner = $gadmin;
                $this->storeCashOut($account_provider, $account_owner, $customer_payment, $admin_amount);
            }

            // sub operators income
            $sub_operators_amount = round($customer_payment->store_amount - $operator_amount);
            $this->operatorsIncome($payment_owner, $customer_payment, $sub_operators_amount);
            $customer_payment->third_party = $sub_operators_amount;

            // operator's income
            $operator_income = round($operator_amount - $admin_amount);
            $this->operatorsIncome($gadmin, $customer_payment, $operator_income);
            $customer_payment->second_party = $operator_income;

            // admin's income
            $this->operatorsIncome($madmin, $customer_payment, $admin_amount);
            $customer_payment->first_party = $admin_amount;
            $customer_payment->save();
        }

        //If payment Collector is operator(role)
        if ($payment_collector->role == 'operator') {

            if ($payment_owner->role == 'sub_operator') {
                // to sub operator
                $operator_amount =  round(($package->operator_price / $master_package->total_minute) * ($purchased_minutes));
                $sub_operator_amount = round($customer_payment->store_amount - $operator_amount);
                $account_provider = $payment_collector;
                $account_owner = $payment_owner;
                $this->storeCashIn($account_provider, $account_owner, $customer_payment, $sub_operator_amount);

                // to master admin
                $madmin_amount = round(($parent_package->operator_price / $master_package->total_minute) * ($purchased_minutes));
                if ($payment_collector->account_type == 'credit') {
                    $account_provider =  $payment_collector;
                    $account_owner = $madmin;
                    $this->storeCashIn($account_provider, $account_owner, $customer_payment, $madmin_amount);
                } else {
                    $account_provider = $madmin;
                    $account_owner = $payment_collector;
                    $this->storeCashOut($account_provider, $account_owner, $customer_payment, $madmin_amount);
                }

                // sub operators income
                $this->operatorsIncome($payment_owner, $customer_payment, $sub_operator_amount);
                $customer_payment->third_party = $sub_operator_amount;

                // admin's income
                $this->operatorsIncome($madmin, $customer_payment, $madmin_amount);
                $customer_payment->first_party = $madmin_amount;

                // operator's income
                $operator_income = round($operator_amount - $madmin_amount);
                $this->operatorsIncome($payment_collector, $customer_payment, $operator_income);
                $customer_payment->second_party = $operator_income;
                $customer_payment->save();
            } else {
                $amount = round(($package->operator_price / $master_package->total_minute) * ($purchased_minutes));

                if ($payment_collector->account_type == 'credit') {
                    $account_provider =  $payment_collector;
                    $account_owner = $madmin;
                    $this->storeCashIn($account_provider, $account_owner, $customer_payment, $amount);
                } else {
                    $account_provider = $madmin;
                    $account_owner = $payment_collector;
                    $this->storeCashOut($account_provider, $account_owner, $customer_payment, $amount);
                }

                // operator's income
                $operator_income = round($customer_payment->store_amount - $amount);
                $this->operatorsIncome($payment_collector, $customer_payment, $operator_income);
                $customer_payment->second_party = $operator_income;

                // admin's income
                $this->operatorsIncome($madmin, $customer_payment, $amount);
                $customer_payment->first_party = $amount;
                $customer_payment->save();
            }
        }

        // If Payment Collector is Group Admin (role)
        if ($payment_collector->role == 'group_admin' || $payment_collector->role == 'super_admin') {

            // Super Admin to Group Admin
            if ($payment_collector->role == 'super_admin') {
                $account_provider = $payment_collector;
                $account_owner = $madmin;
                $amount = $customer_payment->store_amount;
                $this->storeCashIn($account_provider, $account_owner, $customer_payment, $amount);
            }

            // group_admin
            if ($payment_owner->role == 'group_admin') {
                $this->operatorsIncome($payment_owner, $customer_payment, $customer_payment->store_amount);
                $customer_payment->first_party = $customer_payment->store_amount;
                $customer_payment->save();
            }

            // operator
            if ($payment_owner->role == 'operator') {
                $admin_amount = round(($package->operator_price / $master_package->total_minute) * ($purchased_minutes));
                $amount = round($customer_payment->store_amount - $admin_amount);
                $account_provider = $madmin;
                $account_owner = $payment_owner;
                $this->storeCashIn($account_provider, $account_owner, $customer_payment, $amount);

                // group admin's income
                $this->operatorsIncome($madmin, $customer_payment, $admin_amount);
                $customer_payment->first_party = $admin_amount;

                // operator's income
                $this->operatorsIncome($payment_owner, $customer_payment, $amount);
                $customer_payment->second_party = $amount;
                $customer_payment->save();
            }

            // sub_operator
            if ($payment_owner->role == 'sub_operator') {
                // madmin to gadmin
                $madmin_amount = round(($parent_package->operator_price / $master_package->total_minute) * ($purchased_minutes));
                $gadmin_amount = round($customer_payment->store_amount - $madmin_amount);
                $account_provider = $madmin;
                $account_owner = $gadmin;
                $this->storeCashIn($account_provider, $account_owner, $customer_payment, $gadmin_amount);

                // operator to sub operator
                $operator_amount = round(($package->operator_price / $master_package->total_minute) * ($purchased_minutes));
                $sub_operator_amount  =  round($customer_payment->store_amount - $operator_amount);
                $account_provider = $gadmin;
                $account_owner = $payment_owner;
                $this->storeCashIn($account_provider, $account_owner, $customer_payment, $sub_operator_amount);

                // sup operator's income
                $this->operatorsIncome($payment_owner, $customer_payment, $sub_operator_amount);
                $customer_payment->third_party = $sub_operator_amount;

                // group admin's income
                $this->operatorsIncome($madmin, $customer_payment, $madmin_amount);
                $customer_payment->first_party = $madmin_amount;

                // operator's income | মধ্যস্থ ব্যবসায়ী | পায় - দেয়
                $operators_income = $gadmin_amount - $sub_operator_amount;
                $this->operatorsIncome($gadmin, $customer_payment, $operators_income);
                $customer_payment->second_party = $operators_income;
                $customer_payment->save();
            }
        }

        if ($customer_payment->type == 'adjustment') {
            $customer_payment->delete();
        } else {
            // Email Notification
            if ($payment_owner->require_customer_payment_email) {
                $connection = config('app.env') == 'production' ? 'redis' : 'database';
                CustomerPaymentsEmailNotificationJob::dispatch($customer_payment)
                    ->onConnection($connection)
                    ->onQueue('default');
            }
        }
    }


    /**
     * Track Accounts Cash Ins
     *
     * @param  \App\Models\operator  $account_provider
     * @param  \App\Models\operator  $account_owner
     * @param  \App\Models\customer_payment  $customer_payment
     * @param  string  $amount
     * @return \Illuminate\Http\Response
     */
    public function storeCashIn(operator $account_provider, operator $account_owner, customer_payment $customer_payment, string $amount)
    {
        if ($customer_payment->dnb_operator == 1) {
            return 0;
        }

        if ($amount != 0) {
            $where = [
                ['account_provider', '=', $account_provider->id],
                ['account_owner', '=', $account_owner->id],
            ];
            //account
            $account = account::where($where)->firstOr(function () use ($account_provider, $account_owner) {
                $account = new account();
                $account->account_provider = $account_provider->id;
                $account->account_owner = $account_owner->id;
                $account->balance = 0;
                $account->save();
                return $account;
            });
            //cash in
            $cash_in = new cash_in();
            $cash_in->account_id = $account->id;
            $cash_in->transaction_code = 1;
            $cash_in->transaction_id = $customer_payment->id;
            $cash_in->name = $customer_payment->name;
            $cash_in->username = $customer_payment->username;
            $cash_in->amount = $amount;
            $cash_in->date = date(config('app.date_format'));
            $cash_in->old_balance = $account->balance;
            $cash_in->new_balance = $account->balance + $amount;
            $cash_in->month = date(config('app.month_format'));
            $cash_in->year = date(config('app.year_format'));
            $cash_in->save();
            //update account balance
            DB::transaction(function () use ($account, $amount) {
                $the_account = account::lockForUpdate()->find($account->id);
                $the_account->balance = $the_account->balance + $amount;
                $the_account->save();
            });
        }
    }


    /**
     * Track Accounts Cash outs
     *
     * @param  \App\Models\operator  $account_provider
     * @param  \App\Models\operator  $account_owner
     * @param  \App\Models\customer_payment  $customer_payment
     * @param  string  $amount
     * @return \Illuminate\Http\Response
     */
    public function storeCashOut(operator $account_provider, operator $account_owner, customer_payment $customer_payment, string $amount)
    {
        if ($customer_payment->dnb_operator == 1) {
            return 0;
        }

        if ($amount != 0) {
            $where = [
                ['account_provider', '=', $account_provider->id],
                ['account_owner', '=', $account_owner->id],
            ];
            //account
            $account = account::where($where)->firstOr(function () use ($account_provider, $account_owner) {
                $account = new account();
                $account->account_provider = $account_provider->id;
                $account->account_owner = $account_owner->id;
                $account->balance = 0;
                $account->save();
                return $account;
            });
            //cash out
            $cash_out = new cash_out();
            $cash_out->account_id = $account->id;
            $cash_out->transaction_code = 1;
            $cash_out->transaction_id = $customer_payment->id;
            $cash_out->name = $customer_payment->name;
            $cash_out->username = $customer_payment->username;
            $cash_out->amount = $amount;
            $cash_out->date = date(config('app.date_format'));
            $cash_out->old_balance = $account->balance;
            $cash_out->new_balance = $account->balance - $amount;
            $cash_out->month = date(config('app.month_format'));
            $cash_out->year = date(config('app.year_format'));
            $cash_out->save();
            //update account balance
            DB::transaction(function () use ($account, $amount) {
                $the_account = account::lockForUpdate()->find($account->id);
                $the_account->balance = $the_account->balance - $amount;
                $the_account->save();
            });
        }
    }


    /**
     * Record Incomes
     *
     * @param  \App\Models\operator $operator
     * @return \Illuminate\Http\Response
     */

    public function operatorsIncome(operator $operator, customer_payment $customer_payment, $amount)
    {
        if ($amount != 0) {
            $operators_income = new operators_income();
            $operators_income->operator_id = $operator->id;
            $operators_income->payment_id = $customer_payment->id;
            $operators_income->source_operator_id = $customer_payment->operator_id;
            $operators_income->source = 'customers_payment';
            $operators_income->amount = $amount;
            $operators_income->date = date(config('app.date_format'));
            $operators_income->week = date(config('app.week_format'));
            $operators_income->month = date(config('app.month_format'));
            $operators_income->year = date(config('app.year_format'));
            $operators_income->save();
        }
    }
}
