<?php

namespace App\Console\Commands;

use App\Http\Controllers\SubscriptionBillController;
use Illuminate\Console\Command;

class generateOperatorSubscriptionBill extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:generateOperatorBill {gid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate subscription bill for the gid';

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
        return SubscriptionBillController::generateOperatorBill($this->argument('gid'));
        return 0;
    }
}
