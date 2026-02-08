<?php

namespace App\Console\Commands;

use App\Models\card_distributor;
use App\Models\card_distributor_payments;
use App\Models\operator;
use App\Models\yearly_card_distributor_payment;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class YearlyCardDistributorPaymentRecord extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yearly_card_distributor_payment_record';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Yearly Card Distributor Payment Record';

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

            $card_distributors = card_distributor::where('operator_id', $operator->id)->get();

            while ($card_distributor = $card_distributors->shift()) {

                $where = [
                    ['operator_id', '=', $operator->id],
                    ['card_distributor_id', '=', $card_distributor->id],
                    ['month', '=', $month],
                    ['year', '=', $year],
                ];

                $amount_paid = card_distributor_payments::where($where)->sum('amount_paid');

                yearly_card_distributor_payment::updateOrCreate(
                    [
                        'operator_id' => $operator->id,
                        'card_distributor_id' => $card_distributor->id,
                        'month' => $month,
                        'year' => $year
                    ],
                    ['amount_paid' => $amount_paid]
                );
            }
        }

        return 0;
    }
}
