<?php

namespace App\Console\Commands;

use App\Http\Controllers\Customer\HotspotCustomersExpirationController;
use App\Http\Controllers\Customer\PPPoECustomersExpirationController;
use App\Http\Controllers\Customer\PPPoECustomersFramedIPAddressController;
use App\Http\Controllers\PPPCustomerDisconnectController;
use App\Jobs\DisconnectPPPCustomer;
use App\Models\Freeradius\customer;
use Illuminate\Console\Command;

class UpdateExpirationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:Expiration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Expiration';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('The command is running at ');
        $this->info(config('app.name'));

        $operator_id = $this->ask('operator_id ? Enter 0 for all customers');

        if ($operator_id == 0) {
            $customers = customer::all();
        } else {
            $customers = customer::where('operator_id', $operator_id)->get();
        }

        $bar = $this->output->createProgressBar(count($customers));
        $bar->start();
        foreach ($customers as $customer) {
            switch ($customer->connection_type) {
                case 'PPPoE':
                    PPPoECustomersFramedIPAddressController::updateOrCreate($customer);
                    PPPoECustomersExpirationController::updateOrCreate($customer);
                    if ($customer->status != 'active') {
                        PPPCustomerDisconnectController::disconnect($customer);
                    }
                    break;

                case 'Hotspot':
                    HotspotCustomersExpirationController::updateOrCreate($customer);
                    break;
            }
            $bar->advance();
        }
        $bar->finish();
    }
}
