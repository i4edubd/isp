<?php

namespace App\Jobs;

use App\Http\Controllers\Customer\CustomerPackageUpdateController;
use App\Http\Controllers\PPPCustomerDisconnectController;
use App\Models\Freeradius\customer;
use App\Models\operator;
use App\Models\package;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class PackageReplaceJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The package instance
     *
     * @var \App\Models\package
     */
    protected $from_package;

    /**
     * The package instance
     *
     * @var \App\Models\package
     */
    protected $to_package;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public $uniqueFor = 3600;

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->from_package->id;
    }

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(package $from_package, package $to_package)
    {
        $this->from_package = $from_package;
        $this->to_package = $to_package;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $from_package = $this->from_package;
        $to_package = $this->to_package;

        $operator = operator::find($from_package->operator_id);

        $model = new customer();
        $model->setConnection($operator->node_connection);
        $customers = $model->where('package_id', $from_package->id)->get();

        foreach ($customers as $customer) {
            if (Gate::forUser($customer)->allows('usePackage', [$to_package])) {
                CustomerPackageUpdateController::update($customer, $to_package);
                PPPCustomerDisconnectController::handle($customer);
            }
        }

        $from_package->job_processing = 0;
        $from_package->save();

        // update cache
        $from_package_cache_key = 'package_customerCount_' . $from_package->id;
        if (Cache::has($from_package_cache_key)) {
            Cache::forget($from_package_cache_key);
        }

        $to_package_cache_key = 'package_customerCount_' . $to_package->id;
        if (Cache::has($to_package_cache_key)) {
            Cache::forget($to_package_cache_key);
        }
    }
}
