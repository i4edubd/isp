<?php

namespace App\Console\Commands;

use App\Models\customer_bill;
use App\Models\Freeradius\customer;
use Illuminate\Console\Command;

class ClearBillsOfDailyBillingCustomersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily_billing:clear_bills';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear Bills of Daily Billing Customers';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $customers = customer::where('connection_type', 'PPPoE')->where('billing_type', 'Daily')->where('payment_status', 'billed')->get();


        $bar = $this->output->createProgressBar(count($customers));
        $bar->start();

        foreach ($customers as $customer) {

            $customer_bill_count = customer_bill::where('customer_id', $customer->id)
                ->where('operator_id', $customer->operator_id)
                ->delete();

            $this->info($customer_bill_count . ' bills deleted!');

            $customer->payment_status = 'paid';
            $customer->save();

            $bar->advance();
        }

        $bar->finish();

        return 0;
    }
}
