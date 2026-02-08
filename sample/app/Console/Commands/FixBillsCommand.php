<?php

namespace App\Console\Commands;

use App\Http\Controllers\Customer\CustomerBillGenerateController;
use App\Http\Controllers\Customer\PPPoECustomersFramedIPAddressController;
use App\Http\Controllers\PPPCustomerDisconnectController;
use App\Models\customer_bill;
use App\Models\Freeradius\customer;
use App\Models\operator;
use Illuminate\Console\Command;

class FixBillsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:bills {mgid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Temporary command to generate bills of suspended customers has billed status';

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
        $mgid = $this->argument('mgid');

        $madmin = operator::find($mgid);

        $model = new customer();
        $model->setConnection($madmin->node_connection);

        $customers = $model->where('mgid', $mgid)
            ->where('connection_type', 'PPPoE')
            ->where('payment_status', 'billed')
            ->where('status', 'suspended')
            ->get();

        $bar = $this->output->createProgressBar(count($customers));

        $bar->start();

        foreach ($customers as $customer) {
            if (customer_bill::where('operator_id', $customer->operator_id)->where('customer_id', $customer->id)->count()) {
                $bar->advance();
                continue;
            }
            $customer->status = 'active';
            $customer->save();
            PPPoECustomersFramedIPAddressController::updateOrCreate($customer);
            PPPCustomerDisconnectController::disconnect($customer);
            CustomerBillGenerateController::generateBill($customer);
            $bar->advance();
        }
        $bar->finish();
    }
}
