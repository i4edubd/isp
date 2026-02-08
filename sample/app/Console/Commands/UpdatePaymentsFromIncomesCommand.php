<?php

namespace App\Console\Commands;

use App\Models\operator;
use App\Models\operators_income;
use Illuminate\Console\Command;

class UpdatePaymentsFromIncomesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_payments_from_incomes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (config('local.host_type') !== 'central') {
            return 0;
        }

        $operators = operator::all();
        $bar = $this->output->createProgressBar(count($operators));
        $bar->start();
        foreach ($operators as $operator) {
            $operator_incomes = operators_income::with('payment')->where('operator_id', $operator->id)->orderBy('id', 'desc')->limit(1000)->get();
            while ($operator_income = $operator_incomes->shift()) {
                $customer_payment = $operator_income->payment;
                if ($customer_payment->id > 1) {
                    switch ($operator->role) {
                        case 'group_admin':
                            $customer_payment->first_party = $operator_income->amount;
                            $customer_payment->save();
                            break;
                        case 'operator':
                            $customer_payment->second_party = $operator_income->amount;
                            $customer_payment->save();
                            break;
                        case 'sub_operator':
                            $customer_payment->third_party = $operator_income->amount;
                            $customer_payment->save();
                            break;
                    }
                }
            }
            $bar->advance();
        }
        $bar->finish();
        return 0;
    }
}
