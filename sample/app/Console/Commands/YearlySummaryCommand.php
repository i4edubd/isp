<?php

namespace App\Console\Commands;

use App\Http\Controllers\YearlySummaryDeleteController;
use Illuminate\Console\Command;

class YearlySummaryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yearly_summary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Do All Yearly Summary Job';

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
        $this->call('yearly_card_distributor_payment_record');
        $this->call('yearly_cash_ins_record');
        $this->call('yearly_cash_outs_record');
        $this->call('yearly_expenses_record');
        $this->call('yearly_operators_incomes_record');
        YearlySummaryDeleteController::purge();
        return 0;
    }
}
