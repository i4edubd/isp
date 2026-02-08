<?php

namespace App\Console\Commands;

use App\Http\Controllers\Customer\CustomerBillGenerateController;
use App\Models\customer_bill;
use App\Models\customer_payment;
use App\Models\Freeradius\customer;
use App\Models\operator;
use Illuminate\Console\Command;

class BillUnbilledCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bill_unbilled_customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bill Unbilled Customers';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

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

        $gadmins = operator::where('role', 'group_admin')->get();

        while ($gadmin = $gadmins->shift()) {

            $model = new customer();
            $model->setConnection($gadmin->node_connection);

            $customer_where = [
                ['mgid', '=', $gadmin->id],
                ['connection_type', '=', 'PPPoE'],
                ['payment_status', '=', 'paid'],
                ['status', '=', 'active'],
            ];

            $customers = $model->where($customer_where)->get();

            while ($customer = $customers->shift()) {

                $payment_where = [
                    ['operator_id', '=', $customer->operator_id],
                    ['customer_id', '=', $customer->id],
                    ['month', '=', date(config('app.month_format'))],
                    ['year', '=', date(config('app.year_format'))]
                ];

                if (customer_payment::where($payment_where)->count()) {
                    continue;
                } else {
                    CustomerBillGenerateController::generateBill($customer);
                }

                $bills_where = [
                    ['operator_id', '=', $customer->operator_id],
                    ['customer_id', '=', $customer->id],
                ];

                if (customer_bill::where($bills_where)->count()) {
                    $info = "operator id: " . $customer->operator_id . " customer id: " . $customer->id;
                    $this->info($info);
                }
            }
        }

        return 0;
    }
}
