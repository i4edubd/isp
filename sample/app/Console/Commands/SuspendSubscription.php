<?php

namespace App\Console\Commands;

use App\Http\Controllers\SubscriptionStatusController;
use App\Models\operator;
use App\Models\subscription_bill;
use Illuminate\Console\Command;

class SuspendSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suspend:subscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Suspend Subscription';

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
        $gadmins_where = [
            ['role', '=', 'group_admin'],
            ['subscription_type', '=', 'Paid'],
            ['subscription_status', '=', 'active']
        ];

        $gadmins = operator::where($gadmins_where)->get();

        foreach ($gadmins as $gadmin) {

            $count_bill = subscription_bill::where('mgid', $gadmin->id)->count();

            if ($count_bill) {

                SubscriptionStatusController::suspend($gadmin);
            }
        }

        return 0;
    }
}
