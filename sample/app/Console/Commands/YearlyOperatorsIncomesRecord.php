<?php

namespace App\Console\Commands;

use App\Models\operator;
use App\Models\operators_income;
use App\Models\yearly_operators_income;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class YearlyOperatorsIncomesRecord extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yearly_operators_incomes_record';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Yearly Operators Incomes Record';

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
        $yesterday = CarbonImmutable::now()->subDay();

        $month = $yesterday->format(config('app.month_format'));

        $year = $yesterday->format(config('app.year_format'));

        $operators = operator::all();

        while ($operator = $operators->shift()) {

            $where = [
                ['operator_id', '=', $operator->id],
                ['month', '=', $month],
                ['year', '=', $year]
            ];

            $amount = operators_income::where($where)->sum('amount');

            yearly_operators_income::updateOrCreate(
                ['operator_id' => $operator->id, 'month' => $month, 'year' => $year],
                ['amount' => $amount]
            );
        }

        return 0;
    }
}
