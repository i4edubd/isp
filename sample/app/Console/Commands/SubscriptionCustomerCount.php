<?php

namespace App\Console\Commands;

use App\Http\Controllers\CustomerCountController;
use Illuminate\Console\Command;

class SubscriptionCustomerCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:countCustomer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Count Customer For Subscription Bill';

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
        CustomerCountController::store();
        return 0;
    }
}
