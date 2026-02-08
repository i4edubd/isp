<?php

namespace App\Console\Commands;

use App\Http\Controllers\BillsVsPaymentsChartController;
use App\Models\operator;
use Illuminate\Console\Command;

class UpdateBillsVsPaymentsChartCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:bills_vs_payments_chart';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update bills vs. payments chart | run only in central node';

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
        if (config('local.host_type') !== 'central') {
            return 0;
        }

        $operators = operator::all();

        foreach ($operators as $operator) {
            BillsVsPaymentsChartController::store($operator);
        }
    }
}
