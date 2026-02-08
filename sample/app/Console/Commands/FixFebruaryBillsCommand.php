<?php

namespace App\Console\Commands;

use App\Http\Controllers\Customer\CustomerBillGenerateController;
use App\Models\customer_bill;
use App\Models\Freeradius\customer;
use App\Models\operator;
use Illuminate\Console\Command;

class FixFebruaryBillsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:FebruaryBills';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Temp commands to fix bills of february';

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

        $madmins = operator::where('role', 'group_admin')->get();

        foreach ($madmins as $madmin) {
            $where = [
                ['mgid', '=', $madmin->id],
                ['year', '=', '2022'],
                ['month', '=', 'February'],
                ['validity_period', '=', 27],
            ];

            $bills = customer_bill::where($where)->get();

            foreach ($bills as $bill) {

                $model = new customer();
                $model->setConnection($madmin->node_connection);
                $customer = $model->findOrFail($bill->customer_id);
                CustomerBillGenerateController::generateBill($customer);
                $bill->delete();
            }
        }
    }
}
