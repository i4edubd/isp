<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LowSmsBalance extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The balance.
     *
     * @var string
     */
    public $balance;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($balance)
    {
        $this->balance = $balance;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.sms.low_balance');
    }
}
