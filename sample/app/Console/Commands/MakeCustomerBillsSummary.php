<?php

namespace App\Console\Commands;

use App\Http\Controllers\CustomerBillsSummaryController;
use App\Models\operator;
use Illuminate\Console\Command;

class MakeCustomerBillsSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:customer_bills_summary {operator_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make customer bills summary for the given operator';

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
        $operator_id = $this->argument('operator_id');

        $operator = operator::find($operator_id);

        if ($operator) {

            return  CustomerBillsSummaryController::store($operator);
        }

        return 0;
    }
}
