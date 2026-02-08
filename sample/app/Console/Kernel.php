<?php

namespace App\Console;

use App\Models\authentication_log;
use App\Models\customer_change_log;
use App\Models\deleted_customer;
use App\Models\failed_login;
use App\Models\operators_online_payment;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Radsqlrelay
        $schedule->command('rad:sql_relay_v2p')->everyThreeMinutes();

        // RRD Graph
        $schedule->command('update:rrd')->everyFiveMinutes();

        // Remove Stale Sessions
        $schedule->command('delete:rad_stale_sessions')->everyFiveMinutes();

        // PullRadAccts
        $schedule->command('pull:radaccts')->everyFiveMinutes();

        // move radaccts
        $schedule->command('move:radaccts')->everyFifteenMinutes();

        // restart freeradius service
        $schedule->command('restart:freeradius')->everyTwoHours();

        //backup
        if (collect(config('backup.backup.destination.disks'))->first(function ($value, $key) {
            return $value == 'ftp' || $value == 'local';
        })) {
            $schedule->command('backup:clean')->daily()->at('04:00');
            $schedule->command('backup:run')->daily()->at('04:30');
        }

        //prune model
        $schedule->command('model:prune', [
            '--model' => [
                deleted_customer::class,
                customer_change_log::class,
                operators_online_payment::class,
                authentication_log::class,
                failed_login::class,
            ],
        ])->daily();

        // Clear Laravel Logs
        $schedule->command('laravel_logs:clear')->weekly()->tuesdays()->at('23:00');

        // check API Status
        $schedule->command('check_routers_api')->daily();

        // Only for central
        if (config('local.host_type') === 'central') {

            // monitor international attributes
            $schedule->command('monitor:international_attributes')->daily();

            // count_top_users
            $schedule->command('count_top_users')->daily();

            // dashboard
            $schedule->command('update:bills_vs_payments_chart')->hourly();

            //sms
            $schedule->command('sms:generateBill')->everySixHours();
            $schedule->command('check:low_sms_balance')->dailyAt('10:37');
            $schedule->command('recheck:SmsPayment')->everyTwoHours();

            // Due Notice
            $schedule->command('notify:due_dates')->dailyAt('11:15');
            $schedule->command('notify:expiration')->dailyAt('10:15');

            //subscription
            $schedule->command('subscription:countCustomer')->daily();
            $schedule->command('subscription:generateBill')->monthlyOn(2, '00:15');
            $schedule->command('suspend:subscription')->monthlyOn(20, '10:00');

            //backup
            $schedule->command('backup:customers')->daily();

            //customer
            $schedule->command('customer:monthlyBill')->monthly();

            //advance payment
            $schedule->command('auto:debit')->monthlyOn(1, '10:00');

            //Suspend customers
            $schedule->command('auto:suspend_customers')->daily();

            //recheck payment
            $schedule->command('recheck:customer_payment')->everyThirtyMinutes();

            //Data Cleaning
            $schedule->command('data:clean')->monthlyOn(5, '00:15');

            //Marketing Email
            $schedule->command('marketing:gasro')->dailyAt('11:00');

            //Activate FUP
            $schedule->command('activate:fup')->daily();

            // Activity Log
            $schedule->command('activity-log:purge')->daily();

            // yearly summary
            $schedule->command('yearly_summary')->dailyAt('04:00');
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
