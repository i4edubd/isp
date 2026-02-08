<?php

namespace App\Jobs;

use App\Http\Controllers\Customer\CustomersRadLimitController;
use App\Models\Freeradius\customer;
use App\Models\master_package;
use App\Models\operator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateSpeedController implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The package instance
     *
     * @var \App\Models\master_package
     */
    protected $master_package;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\master_package $master_package
     * @return void
     */
    public function __construct(master_package $master_package)
    {
        $this->master_package = $master_package;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $master_package = $this->master_package;
        $madmin = operator::findOrFail($master_package->mgid);
        $packages = $master_package->packages;

        foreach ($packages as $package) {
            $model = new customer();
            $model->setConnection($madmin->node_connection);
            $where = [
                ['mgid', '=', $master_package->mgid],
                ['connection_type', '=', $master_package->connection_type],
                ['package_id', '=', $package->id],
            ];
            $customers = $model->where($where)->get();

            foreach ($customers as $customer) {
                CustomersRadLimitController::updateOrCreate($customer);
            }
        }
    }
}
