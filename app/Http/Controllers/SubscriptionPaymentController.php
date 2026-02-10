<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Payment\BkashCheckoutController;
use App\Http\Controllers\Payment\bKashTokenizedSubscriptionPaymentController;
use App\Http\Controllers\Payment\EasypaywayTransactionController;
use App\Http\Controllers\Payment\NagadPaymentGatewayController;
use App\Http\Controllers\Payment\SslcommerzTransactionController;
use App\Jobs\AffiliateCommissionJob;
use App\Mail\SoftwareSubscriptionPaymentReceived;
use App\Models\account;
use App\Models\cash_in;
use App\Models\subscription_payment;
use App\Models\subscription_bill;
use App\Models\operator;
use App\Models\operators_income;
use App\Models\payment_gateway;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SubscriptionPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();

        switch ($operator->role) {

            case 'super_admin':
                $subscription_payments = subscription_payment::orderBy('id', 'desc')->paginate(15);
                return view('admins.super_admin.subscription-payments', [
                    'subscription_payments' => $subscription_payments,
                ]);
                break;

            case 'group_admin':
                $subscription_payments = subscription_payment::where('mgid', $operator->id)->orderBy('id', 'desc')->paginate(15);
                return view('admins.group_admin.subscription-payments', [
                    'subscription_payments' => $subscription_payments,
                ]);
                break;
        }
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, subscription_bill $subscription_bill)
    {
        $request->validate([
            'payment_gateway_id' => 'required'
        ]);

        //operator
        $operator = operator::findOrFail($subscription_bill->mgid);
        //payment_gateway
        $payment_gateway = payment_gateway::findOrFail($request->payment_gateway_id);
        //mer_txnid
        $mer_txnid = random_int(1000, 9999) . Carbon::now(config('app.timezone'))->timestamp;
        //subscription_payment
        $subscription_payment = new subscription_payment();
        $subscription_payment->mgid = $subscription_bill->mgid;
        $subscription_payment->subscription_bill_id = $subscription_bill->id;
        $subscription_payment->payment_gateway_id = $payment_gateway->id;
        $subscription_payment->payment_gateway_name = $payment_gateway->provider_name;
        $subscription_payment->operator_name = $subscription_bill->operator_name;
        $subscription_payment->operator_email = $subscription_bill->operator_email;
        $subscription_payment->user_count = $subscription_bill->user_count;
        $subscription_payment->type = 'Online';
        $subscription_payment->amount_paid = $subscription_bill->amount;
        $subscription_payment->store_amount = 0;
        $subscription_payment->transaction_fee = 0;
        $subscription_payment->mer_txnid = $mer_txnid;
        $subscription_payment->pgw_txnid = 0;
        $subscription_payment->date = date(config('app.date_format'));
        $subscription_payment->week = date(config('app.week_format'));
        $subscription_payment->month = date(config('app.month_format'));
        $subscription_payment->year = date(config('app.year_format'));
        $subscription_payment->save();

        switch ($payment_gateway->provider_name) {
            case 'easypayway':
                return redirect()->route('easypayway.subscription_payment.initiate', ['subscription_payment' => $subscription_payment->id]);
                break;
            case 'sslcommerz':
                return redirect()->route('sslcommerz.subscription_payment.initiate', ['subscription_payment' => $subscription_payment->id]);
                break;
            case 'bkash_checkout':
                return redirect()->route('bkash.subscription_payment.initiate', ['subscription_payment' => $subscription_payment->id]);
                break;
            case 'nagad':
                return redirect()->route('nagad.subscription_payment.initiate', ['subscription_payment' => $subscription_payment->id]);
                break;
            case 'send_money':
                return redirect()->route('send_money.subscription_payment.create', ['subscription_payment' => $subscription_payment->id]);
                break;
            case 'bkash_tokenized_checkout':
                return redirect()->route('bkash_tokenized.subscription_payment.initiate', ['subscription_payment' => $subscription_payment->id]);
                break;
        }
    }


    /**
     * Recheck the Pending Payment Status.
     *
     * @param \App\Models\subscription_payment $subscription_payment
     * @return \Illuminate\Http\Response
     */

    public function recheckPayment(subscription_payment $subscription_payment)
    {

        if ($subscription_payment->payment_gateway_id == 0) {
            $subscription_payment->delete();
            return redirect()->route('subscription_payments.index')->with('error', 'Payment Was Failed');
        }

        $payment_gateway = payment_gateway::findOrFail($subscription_payment->payment_gateway_id);

        $status = 0;

        switch ($payment_gateway->provider_name) {
            case 'easypayway':
                $controller = new EasypaywayTransactionController();
                $status = $controller->recheckSubscriptionPayment($subscription_payment);
                break;
            case 'sslcommerz':
                $controller = new SslcommerzTransactionController();
                $status = $controller->recheckSubscriptionPayment($subscription_payment);
                break;
            case 'bkash_checkout':
                $controller = new BkashCheckoutController();
                $status = $controller->recheckSubscriptionPayment($subscription_payment);
                break;
            case 'nagad':
                $controller = new NagadPaymentGatewayController();
                $status = $controller->recheckSubscriptionPayment($subscription_payment);
                break;
            case 'bkash_tokenized_checkout':
                $controller = new bKashTokenizedSubscriptionPaymentController();
                $status = $controller->recheckPayment($subscription_payment);
                break;
        }

        if ($status) {
            return redirect()->route('subscription_payments.index')->with('success', 'Payment was Successfull');
        } else {
            return redirect()->route('subscription_payments.index')->with('error', 'Payment Was Failed');
        }
    }


    /**
     * Record Software Incomes.
     *
     * @param \App\Models\subscription_payment $subscription_payment
     */
    public static function recordIncomes(subscription_payment $subscription_payment)
    {
        $subscription_payment->refresh();

        if ($subscription_payment->used == 1) {
            return 0;
        } else {
            $subscription_payment->used = 1;
            $subscription_payment->save();
        }

        if (strlen(config('consumer.accountant_email')) && strlen(config('mail.mailers.smtp.username'))) {
            Mail::to(config('consumer.accountant_email'))->send(new SoftwareSubscriptionPaymentReceived($subscription_payment));
        }

        $operator = operator::find($subscription_payment->mgid);

        SubscriptionStatusController::activate($operator);

        $developer = operator::where('role', 'developer')->first();

        $developers_share = config('consumer.developers_share');

        $developers_amount = round(($developers_share / 100) * $subscription_payment->store_amount);

        if ($developers_amount > 0) {

            $where = [
                ['account_provider', '=', $operator->sid],
                ['account_owner', '=', $developer->id],
            ];

            // account
            $account = account::where($where)->firstOr(function () use ($operator, $developer) {
                $account = new account();
                $account->account_provider = $operator->sid;
                $account->account_owner = $developer->id;
                $account->balance = 0;
                $account->save();
                return $account;
            });

            // cash in
            $cash_in = new cash_in();
            $cash_in->account_id = $account->id;
            $cash_in->transaction_code = 2;
            $cash_in->transaction_id = $subscription_payment->id;
            $cash_in->name = $subscription_payment->operator_name;
            $cash_in->username = $subscription_payment->operator_email;
            $cash_in->amount = $developers_amount;
            $cash_in->date = date(config('app.date_format'));
            $cash_in->old_balance = $account->balance;
            $cash_in->new_balance = $account->balance + $developers_amount;
            $cash_in->month = date(config('app.month_format'));
            $cash_in->year = date(config('app.year_format'));
            $cash_in->save();

            // update account balance
            DB::transaction(function () use ($account, $developers_amount) {
                $the_account = account::lockForUpdate()->find($account->id);
                $the_account->balance = $the_account->balance + $developers_amount;
                $the_account->save();
            });

            // developer's income
            $operators_income = new operators_income();
            $operators_income->operator_id = $developer->id;
            $operators_income->payment_id = $subscription_payment->id;
            $operators_income->source_operator_id = $subscription_payment->mgid;
            $operators_income->source = 'subscription_fee';
            $operators_income->amount = $developers_amount;
            $operators_income->date = date(config('app.date_format'));
            $operators_income->week = date(config('app.week_format'));
            $operators_income->month = date(config('app.month_format'));
            $operators_income->year = date(config('app.year_format'));
            $operators_income->save();
        }

        $operators_income = new operators_income();
        $operators_income->operator_id = $operator->sid;
        $operators_income->payment_id = $subscription_payment->id;
        $operators_income->source_operator_id = $subscription_payment->mgid;
        $operators_income->source = 'subscription_fee';
        $operators_income->amount = $subscription_payment->store_amount - $developers_amount;
        $operators_income->date = date(config('app.date_format'));
        $operators_income->week = date(config('app.week_format'));
        $operators_income->month = date(config('app.month_format'));
        $operators_income->year = date(config('app.year_format'));
        $operators_income->save();

        AffiliateCommissionJob::dispatch($subscription_payment)
            ->onConnection('database')
            ->onQueue('default');
    }
}
