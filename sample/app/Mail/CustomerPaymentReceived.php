<?php

namespace App\Mail;

use App\Models\customer_payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerPaymentReceived extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The customer_payment instance.
     *
     * @var \App\Models\customer_payment
     */
    public $customer_payment;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(customer_payment $customer_payment)
    {
        $this->customer_payment = $customer_payment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.customer_payment_received');
    }
}
