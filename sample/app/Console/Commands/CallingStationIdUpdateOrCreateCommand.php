<?php

namespace App\Console\Commands;

use App\Http\Controllers\Customer\RadCallingStationIdController;
use App\Models\Freeradius\customer;
use Illuminate\Console\Command;

class CallingStationIdUpdateOrCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UpdateOrCreate:CallingStationId';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Or Create CallingStationId';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $customers = customer::all();
        $bar = $this->output->createProgressBar(count($customers));
        $bar->start();
        foreach ($customers as $customer) {
            RadCallingStationIdController::updateOrCreate($customer);
            $bar->advance();
        }
        $bar->finish();
        return 0;
    }
}
