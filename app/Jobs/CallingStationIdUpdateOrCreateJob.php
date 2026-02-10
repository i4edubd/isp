<?php

namespace App\Jobs;

use App\Http\Controllers\Customer\RadCallingStationIdController;
use App\Models\Freeradius\customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CallingStationIdUpdateOrCreateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The customer instance.
     *
     * @var \App\Models\Freeradius\customer
     */
    public $customer;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(customer $customer)
    {
        $this->customer = $customer;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $customer = $this->customer;
        RadCallingStationIdController::doUpdateOrCreate($customer);
    }
}
