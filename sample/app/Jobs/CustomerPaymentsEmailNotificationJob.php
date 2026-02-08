<?php

namespace App\Jobs;

use App\Mail\CustomerPaymentReceived;
use App\Models\customer_payment;
use App\Models\operator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class CustomerPaymentsEmailNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The customer_payment instance.
     *
     * @var \App\Models\customer_payment
     */
    protected $customer_payment;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(customer_payment $customer_payment)
    {
        $this->customer_payment = $customer_payment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $customer_payment = $this->customer_payment;

        $operator = operator::find($customer_payment->operator_id);

        if ($operator->require_customer_payment_email) {

            Mail::to($operator->accounts_email)->send(new CustomerPaymentReceived($customer_payment));
        }
    }
}
