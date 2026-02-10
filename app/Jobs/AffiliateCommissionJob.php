<?php

namespace App\Jobs;

use App\Models\account;
use App\Models\cash_in;
use App\Models\operator;
use App\Models\operators_income;
use App\Models\subscription_payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class AffiliateCommissionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The subscription_payment instance.
     *
     * @var \App\Models\subscription_payment
     */
    public $subscription_payment;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(subscription_payment $subscription_payment)
    {
        $this->subscription_payment = $subscription_payment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (config('consumer.has_support_programme') == false) {
            return 0;
        }

        if (config('consumer.support_programme_director') == 0) {
            return 0;
        }

        $subscription_payment = $this->subscription_payment;

        $group_admin = operator::find($subscription_payment->mgid);

        if (!$group_admin) {
            return 0;
        }

        if ($group_admin->marketer_id == 0) {
            return 0;
        }

        $marketer = operator::find($group_admin->marketer_id);

        if (!$marketer) {
            return 0;
        }

        $programme_director = operator::find(config('consumer.support_programme_director'));

        if (!$programme_director) {
            return 0;
        }

        $commission_rate = config('consumer.affiliate_commission_rate');

        $amount =  round(($commission_rate / 100) * $subscription_payment->store_amount);

        if ($amount < 1) {
            return 0;
        }

        $account = account::where('account_provider', $programme_director->id)
            ->where('account_owner', $marketer->id)
            ->firstOr(function () use ($programme_director, $marketer) {
                $account = new account();
                $account->account_provider = $programme_director->id;
                $account->account_owner = $marketer->id;
                $account->balance = 0;
                $account->save();
                return $account;
            });

        // cash_in
        $note = $commission_rate . ' % commission of ' . $subscription_payment->store_amount;
        $cash_in = new cash_in();
        $cash_in->account_id = $account->id;
        $cash_in->transaction_code = 6;
        $cash_in->transaction_id = $subscription_payment->mer_txnid;
        $cash_in->name = $group_admin->company;
        $cash_in->username = $group_admin->email;
        $cash_in->amount = $amount;
        $cash_in->date = date(config('app.date_format'));
        $cash_in->old_balance = $account->balance;
        $cash_in->new_balance = $account->balance + $amount;
        $cash_in->month = date(config('app.month_format'));
        $cash_in->year = date(config('app.year_format'));
        $cash_in->note = $note;
        $cash_in->save();

        // update balance
        DB::transaction(function () use ($account, $amount) {
            $the_account = account::lockForUpdate()->find($account->id);
            $the_account->balance = $the_account->balance + $amount;
            $the_account->save();
        });

        // marketer's income
        $operators_income = new operators_income();
        $operators_income->operator_id = $marketer->id;
        $operators_income->payment_id = $subscription_payment->id;
        $operators_income->source_operator_id = $group_admin->id;
        $operators_income->source = 'subscription_fee';
        $operators_income->amount = $amount;
        $operators_income->date = date(config('app.date_format'));
        $operators_income->week = date(config('app.week_format'));
        $operators_income->month = date(config('app.month_format'));
        $operators_income->year = date(config('app.year_format'));
        $operators_income->save();

        return 1;
    }
}
