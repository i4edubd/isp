<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MonitorAllQueueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:monitor_all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor the size of all queues';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $connections_and_queues = [
            "database" => ['default', 'bulk_customer_bill_paid', 'calling_station_id_attribute', 'customer_payments', 'extend_package_validity', 'hotspot_customers_rad_attributes', 'import_ppp_customers', 'package_replace', 'pppoe_customers_rad_attributes', 're_allocate_ipv4'],
            "redis" => ['default', 'disconnect', 'notify_due_dates'],
        ];

        foreach ($connections_and_queues as $connection => $queues) {
            foreach ($queues as $queue) {
                $this->call('queue:monitor', ['queues' => $connection . ':' . $queue]);
            }
        }

        return 0;
    }
}
