<?php

namespace App\Console\Commands;

use App\Models\Freeradius\customer;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateExptimestampCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:exptimestamp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update exptimestamp';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $customers = customer::whereNull('exptimestamp')->get();
        $bar = $this->output->createProgressBar(count($customers));
        $bar->start();
        while ($customer = $customers->shift()) {
            $customer->exptimestamp =  Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($customer->operator_id), 'en')->timestamp;
            $customer->save();
            $bar->advance();
        }
        $bar->finish();
        return 0;
    }
}
