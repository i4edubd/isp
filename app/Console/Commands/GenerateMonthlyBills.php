<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BillingService;
use Carbon\Carbon;

class GenerateMonthlyBills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:generate-monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly bills for all active customers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to generate monthly bills...');

        $service = new BillingService();
        $result = $service->generateMonthlyBills(Carbon::now());

        $this->info("Created: {$result['created']}");
        if (!empty($result['errors'])) {
            foreach ($result['errors'] as $err) {
                $this->error($err);
            }
        }

        $this->info('Finished generating monthly bills.');
        return 0;
    }
}