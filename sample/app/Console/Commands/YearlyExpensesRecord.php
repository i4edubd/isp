<?php

namespace App\Console\Commands;

use App\Models\expense;
use App\Models\expense_category;
use App\Models\operator;
use App\Models\yearly_expense;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class YearlyExpensesRecord extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yearly_expenses_record';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Yearly Expenses Record';

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

            $categories = expense_category::where('operator_id', $operator->id)->get();

            while ($category = $categories->shift()) {

                $where = [
                    ['operator_id', '=', $operator->id],
                    ['expense_category_id', '=', $category->id],
                ];

                $expenses_amount = expense::where($where)->sum('amount');

                yearly_expense::updateOrCreate(
                    [
                        'operator_id' => $operator->id,
                        'expense_category' => $category->category_name,
                        'month' => $month,
                        'year' => $year
                    ],
                    ['amount' => $expenses_amount]
                );
            }
        }

        return 0;
    }
}
