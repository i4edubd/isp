<?php

namespace App\Mail;

use App\Models\subscription_payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SoftwareSubscriptionPaymentReceived extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The operator instance.
     *
     * @var \App\Models\subscription_payment
     */
    public $subscription_payment;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(subscription_payment $subscription_payment)
    {
        $this->subscription_payment = $subscription_payment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.subscription.payment-received');
    }
}
