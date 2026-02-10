<?php

namespace App\Jobs;

use App\Http\Controllers\PPPCustomerDisconnectController;
use App\Models\Freeradius\customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DisconnectPPPCustomer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * The Customer instance.
     *
     * @var \App\Models\Freeradius\customer
     */
    protected $customer;

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\Freeradius\customer $customer
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
        PPPCustomerDisconnectController::handle($this->customer);
    }
}
