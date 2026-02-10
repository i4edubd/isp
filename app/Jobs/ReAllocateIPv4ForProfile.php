<?php

namespace App\Jobs;

use App\Http\Controllers\Customer\PPPoECustomersFramedIPAddressController;
use App\Http\Controllers\Customer\PPPoECustomersMikrotikGroupController;
use App\Http\Controllers\PPPCustomerDisconnectController;
use App\Models\Freeradius\customer;
use App\Models\operator;
use App\Models\pppoe_profile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReAllocateIPv4ForProfile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The pppoe_profile instance
     *
     * @var \App\Models\pppoe_profile
     */
    protected $pppoe_profile;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(pppoe_profile $pppoe_profile)
    {
        $this->pppoe_profile = $pppoe_profile;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $pppoe_profile = $this->pppoe_profile;

        $madmin = operator::find($pppoe_profile->mgid);

        $mpackages = $pppoe_profile->master_packages;

        foreach ($mpackages as $mpackage) {

            $packages = $mpackage->packages;

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
}
