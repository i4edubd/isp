<?php

namespace App\Jobs;

use App\Http\Controllers\Customer\PPPoECustomersFramedIPAddressController;
use App\Http\Controllers\Customer\PPPoECustomersMikrotikGroupController;
use App\Http\Controllers\PPPCustomerDisconnectController;
use App\Models\Freeradius\customer;
use App\Models\master_package;
use App\Models\operator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReAllocateIPv4ForPackage implements ShouldQueue
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
        $madmin = operator::find($master_package->mgid);
        $packages = $master_package->packages;
        foreach ($packages as $package) {
            $model = new customer();
            $model->setConnection($madmin->node_connection);
            $customers = $model->where('package_id', $package->id)->get();
            foreach ($customers as $customer) {
                PPPoECustomersFramedIPAddressController::updateOrCreate($customer);
                PPPoECustomersMikrotikGroupController::updateOrCreate($customer);
                PPPCustomerDisconnectController::disconnect($customer);
            }
        }
    }
}
