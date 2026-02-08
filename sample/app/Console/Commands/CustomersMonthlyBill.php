<?php

namespace App\Console\Commands;

use App\Http\Controllers\Customer\CustomerBillGenerateController;
use Illuminate\Console\Command;

class CustomersMonthlyBill extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer:monthlyBill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Customers Monthly Bill';

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
        CustomerBillGenerateController::monthlyBill();
        return 0;
    }
}
