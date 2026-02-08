<?php

namespace App\Console\Commands;

use App\Http\Controllers\CacheController;
use App\Models\Freeradius\customer;
use App\Models\package;
use Illuminate\Console\Command;

class FixPackageNotFoundCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix_not_found:package';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix Package Not Found Problem';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $customers = customer::all();

        $bar = $this->output->createProgressBar(count($customers));
        $bar->start();

        foreach ($customers as $customer) {

            package::where('id', $customer->package_id)->firstOr(function () use ($customer) {
                $info = 'Package Not Found';
                $info .= ' Server: ' . config('app.name');
                $info .= ' Operator: ' . $customer->operator_id;
                $info .= ' Customer: ' . $customer->id;
                $info .= ' package_id: ' . $customer->package_id;
                $this->info($info);

                $operator = CacheController::getOperator($customer->operator_id);
                if (!$operator) {
                    $customer->delete();
                    return 1;
                }
                $packages = $operator->packages;
                $connection_type = $customer->connection_type;
                $packages = $packages->filter(function ($package) use ($connection_type) {
                    return $package->master_package->connection_type == $connection_type && $package->name !== 'Trial';
                });
                $package = $packages->first();
                if ($package) {
                    $customer->package_id = $package->id;
                    $customer->save();
                    $this->warn('Problem Fixed');
                }
            });

            $bar->advance();
        }

        $bar->finish();

        return 0;
    }
}
