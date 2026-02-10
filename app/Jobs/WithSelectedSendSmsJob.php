<?php

namespace App\Jobs;

use App\Http\Controllers\CacheController;
use App\Http\Controllers\Sms\SmsGatewayController;
use App\Models\Freeradius\customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WithSelectedSendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The message
     *
     * @var string
     */
    protected $message;

    /**
     * The customer instance.
     *
     * @var \App\Models\Freeradius\customer
     */
    protected $customer;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(customer $customer, string $message)
    {
        $this->message = $message;
        $this->customer = $customer;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $message = $this->message;
        $customer = $this->customer;

        $sms_gateway = new SmsGatewayController();
        $sms_operator = CacheController::getOperator($customer->operator_id);
        $mobile = validate_mobile($customer->mobile, getCountryCode($customer->operator_id));
        if ($mobile) {
            try {
                $sms_gateway->sendSms($sms_operator, $mobile, $message, $customer->id);
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
    }
}
