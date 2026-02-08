<?php

namespace App\Jobs;

use App\Http\Controllers\CustomerBillingProfileEditController;
use App\Models\billing_profile;
use App\Models\Freeradius\customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CustomerBillingProfileUpdateJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The customer instance.
     *
     * @var \App\Models\Freeradius\customer
     */
    public $customer;

    /**
     * The billing_profile instance.
     *
     * @var \App\Models\billing_profile
     */
    public $billing_profile;

    /**
     * The payment status.
     *
     * @var int
     */
    public $payment;

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
        return $this->customer->id;
    }

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(customer $customer, billing_profile $billing_profile, int $payment)
    {
        $this->customer = $customer;
        $this->billing_profile = $billing_profile;
        $this->payment = $payment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        CustomerBillingProfileEditController::doUpdate($this->customer, $this->billing_profile, $this->payment);
    }
}
