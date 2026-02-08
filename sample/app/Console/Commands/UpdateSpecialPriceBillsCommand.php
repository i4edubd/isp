<?php

namespace App\Console\Commands;

use App\Models\custom_price;
use App\Models\customer_bill;
use Illuminate\Console\Command;

class UpdateSpecialPriceBillsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:specialPriceBills';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update special price bills';

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

        $custom_prices = custom_price::all();

        foreach ($custom_prices as $custom_price) {

            $update = customer_bill::where('operator_id', $custom_price->operator_id)
                ->where('customer_id', $custom_price->customer_id)
                ->where('package_id', $custom_price->package_id)
                ->where('validity_period', 30)
                ->update(['amount' => $custom_price->price]);

            if ($update) {
                $this->info($update);
            }
        }

        return 0;
    }
}
