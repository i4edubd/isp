<?php

namespace App\Console\Commands;

use App\Http\Controllers\AllCustomerController;
use App\Models\Freeradius\customer;
use Illuminate\Console\Command;

class UpdateAllCustomersE164NumberCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:all_customers_e164_number';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update All Customers E164 Number Command';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Job Started ...');

        foreach (customer::lazy() as $customer) {
            $this->info(AllCustomerController::updateOrCreate($customer));
        }

        $this->info('Job Finished');

        return 0;
    }
}
