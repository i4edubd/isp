<?php

namespace App\Console\Commands;

use App\Http\Controllers\CacheController;
use App\Http\Controllers\CustomerPayment\CustomersPaymentProcessController;
use App\Models\customer_payment;
use App\Models\Freeradius\customer;
use App\Models\operator;
use App\Models\operators_income;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FindUnprocessedPaymentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'find:unprocessed_payments {from_date : d-m-Y} {to_date : d-m-Y} {from_hour : 18} {to_hour : 12}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'find unprocessed payments';

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

        $from_date = date_format(date_create($this->argument('from_date')), config('app.date_format'));
        $to_date = date_format(date_create($this->argument('to_date')), config('app.date_format'));
        $created_from = Carbon::createFromFormat(config('app.date_format'), $from_date)->setHour($this->argument('from_hour'));
        $created_to = Carbon::createFromFormat(config('app.date_format'), $to_date)->setHour($this->argument('to_hour'));

        $customer_payments = customer_payment::whereBetween('created_at', [$created_from, $created_to])->get();

        foreach ($customer_payments as $customer_payment) {
            if (operators_income::where('payment_id', $customer_payment->id)->where('source', 'customers_payment')->count()) {
                continue;
            } else {
                $info = 'Payment ID:  ' . $customer_payment->id;
                $info .= ' Operator ID: ' . $customer_payment->operator_id;
                $info .= ' Customer ID: ' . $customer_payment->customer_id;
                $info .= ' Amount : ' . $customer_payment->amount_paid;
                $info .= ' package_started_at: ' . $customer_payment->package_started_at;
                $info .= ' package_expired_at: ' . $customer_payment->package_expired_at;
                $info .= ' Time: ' . $customer_payment->created_at;

                $this->info($info);

                $operator = CacheController::getOperator($customer_payment->operator_id);
                $model = new customer();
                $model->setConnection($operator->node_connection);
                $customer = $model->find($customer_payment->customer_id);

                $info = 'Node : ' . $operator->node_connection;
                $info .= ' customer package started at :' . $customer->package_started_at;
                $info .= ' Custoemr Package Expired at: ' . $customer->package_expired_at;
                $this->info($info);

                if ($this->confirm('null package exp date?', true)) {
                    $customer_payment->package_started_at = null;
                    $customer_payment->package_expired_at = null;
                    $customer_payment->save();
                }

                if ($this->confirm('Do you wish process the payment now?', true)) {
                    $customer_payment->used = 0;
                    $customer_payment->save();
                    CustomersPaymentProcessController::store($customer_payment);
                }
            }
        }

        return 0;
    }
}
