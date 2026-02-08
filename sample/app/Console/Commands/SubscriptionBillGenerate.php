<?php

namespace App\Console\Commands;

use App\Http\Controllers\SubscriptionBillController;
use Illuminate\Console\Command;

class SubscriptionBillGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:generateBill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Subscription Bill';

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
        SubscriptionBillController::generateBill();
        return 0;
    }
}
