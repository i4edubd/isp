<?php

namespace App\Console\Commands;

use App\Models\customer_bill;
use App\Models\operator;
use App\Models\package;
use Illuminate\Console\Command;

class UpdateCustomerBillsAmount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_customer_bills_amount';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update customer bills amount';

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

        $gadmins = operator::where('role', 'group_admin')->get();

        while ($gadmin = $gadmins->shift()) {

            $packages = package::where('mgid', $gadmin->id)->get();

            while ($package = $packages->shift()) {

                $master_package = $package->master_package;

                $bills_where = [
                    ['mgid', '=', $gadmin->id],
                    ['package_id', '=', $package->id],
                    ['validity_period', '=', $master_package->validity],
                ];

                customer_bill::where($bills_where)->update([
                    'amount' => $package->price,
                    'operator_amount' => $package->operator_price
                ]);
            }
        }

        return 0;
    }
}
