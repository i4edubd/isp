<?php

namespace App\Console\Commands;

use App\Http\Controllers\CacheController;
use App\Models\Freeradius\customer;
use App\Models\operator;
use Illuminate\Console\Command;

class ConstraintViolationCheckForConnectionType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'constraint_violation_check:connection_type';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'constraint violation check for connection_type';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $operators = operator::where('role', 'group_admin')->get();
        $bar = $this->output->createProgressBar(count($operators));
        $bar->start();

        foreach ($operators as $operator) {
            $model = new customer();
            $model->setConnection($operator->node_connection);
            $customers = $model->where('mgid', $operator->id)->select('id', 'mgid', 'gid', 'operator_id', 'connection_type', 'billing_type', 'package_id', 'package_name')->get();
            foreach ($customers as $customer) {
                $package = CacheController::getPackage($customer->package_id);
                if ($customer->connection_type != $package->master_package->connection_type) {
                    $this->newLine();
                    $this->info('node: ' . $operator->radius_db_connection .  ' admin id: ' . $operator->id . ' operator id: ' . $customer->operator_id . ' customer id: ' . $customer->id . ' package id: ' . $package->id . ' master package id: ' . $package->master_package->id);
                }
            }
            $bar->advance();
        }

        $bar->finish();

        return Command::SUCCESS;
    }
}
