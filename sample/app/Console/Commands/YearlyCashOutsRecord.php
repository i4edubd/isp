<?php

namespace App\Console\Commands;

use App\Models\account;
use App\Models\cash_out;
use App\Models\yearly_cash_out;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class YearlyCashOutsRecord extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yearly_cash_outs_record';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Yearly Cash Outs Record';

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

        $accounts = account::all();

        while ($account = $accounts->shift()) {

            $where = [
                ['account_id', '=', $account->id],
                ['month', '=', $month],
                ['year', '=', $year]
            ];

            $amount = cash_out::where($where)->sum('amount');

            yearly_cash_out::updateOrCreate(
                ['account_id' => $account->id, 'month' => $month, 'year' => $year],
                ['amount' => $amount]
            );
        }

        return 0;
    }
}
