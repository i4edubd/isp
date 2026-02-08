<?php

namespace App\Console\Commands;

use App\Http\Controllers\Customer\CustomerBillGenerateController;
use Illuminate\Console\Command;

class GenerateCustomerBill extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:CustomerBill {operator_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Customers Bill for the Operator';

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
        return CustomerBillGenerateController::monthlyBillForOperator($this->argument('operator_id'));
        return 0;
    }
}
