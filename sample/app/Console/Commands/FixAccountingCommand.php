<?php

namespace App\Console\Commands;

use App\Http\Controllers\CustomerPayment\CustomerPaymentAccountingController;
use App\Models\account;
use App\Models\cash_in;
use App\Models\cash_out;
use App\Models\customer_payment;
use App\Models\operator;
use App\Models\operators_income;
use Illuminate\Console\Command;

class FixAccountingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:accounts {operator_id} {transaction_model} {last_known_good_transaction_id} {first_known_bad_payment_id} {--dry_run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix Accounts';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dry_run = $this->option('dry_run');
        $operator_id = $this->argument('operator_id');
        $transaction_model = $this->argument('transaction_model');
        $transaction_id = $this->argument('last_known_good_transaction_id');
        $payment_id = $this->argument('first_known_bad_payment_id');

        $operator = operator::findOrFail($operator_id);
        $info = 'Email: ' . $operator->email . ' Name: ' . $operator->name . ' Role: ' . $operator->role;
        $this->info($info);

        if ($transaction_model == 'cash_in') {
            $transaction = cash_in::findOrFail($transaction_id);
        } else {
            $transaction = cash_out::findOrFail($transaction_id);
        }
        $info = 'Last Known Correct Balance: ' . $transaction->new_balance . ' Date: ' . $transaction->date;
        $this->info($info);

        $payment = customer_payment::findOrFail($payment_id);
        $info = 'First Payment to Fix. Amount: ' . $payment->amount_paid . ' Date: ' . $payment->date;
        $this->info($info);

        if (!$this->confirm('Do you want to continue?')) {
            return 0;
        }

        $account = account::findOrFail($transaction->account_id);
        $account->balance = $transaction->new_balance;
        if ($dry_run == false) {
            $account->save();
        }

        $payments = customer_payment::where('operator_id', $operator_id)->where('id', '>', $payment_id - 1)->get();

        foreach ($payments as $payment) {
            if ($dry_run) {
                $this->info('processing payment .. ' . $payment->id);
            } else {
                $this->info('processing payment .. ' . $payment->id);

                cash_in::where('account_id', $account->id)
                    ->where('transaction_id', $payment->id)
                    ->delete();

                cash_out::where('account_id', $account->id)
                    ->where('transaction_id', $payment->id)
                    ->delete();

                operators_income::where('payment_id', $payment->id)
                    ->where('source', 'customers_payment')
                    ->delete();

                $AccountingController = new CustomerPaymentAccountingController();
                $AccountingController->distributePaymet($payment);

                $cash_ins = cash_in::where('account_id', $account->id)
                    ->where('transaction_id', $payment->id)
                    ->get();

                foreach ($cash_ins as $cash_in) {
                    $cash_in->date = $payment->date;
                    $cash_in->month = $payment->month;
                    $cash_in->year = $payment->year;
                    $cash_in->save();
                }

                $cash_outs = cash_out::where('account_id', $account->id)
                    ->where('transaction_id', $payment->id)
                    ->get();

                foreach ($cash_outs as $cash_out) {
                    $cash_out->date = $payment->date;
                    $cash_out->month = $payment->month;
                    $cash_out->year = $payment->year;
                    $cash_out->save();
                }

                $operators_incomes = operators_income::where('payment_id', $payment->id)
                    ->where('source', 'customers_payment')
                    ->get();

                foreach ($operators_incomes as $operators_income) {
                    $operators_income->date = $payment->date;
                    $operators_income->month = $payment->month;
                    $operators_income->year = $payment->year;
                    $operators_income->save();
                }
            }
        }

        return 0;
    }
}
