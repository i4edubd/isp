<?php

namespace App\Mail;

use App\Models\operator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GreetingFromIspBillingSolution extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The operator instance.
     *
     * @var \App\Models\operator
     */
    public $operator;


    /**
     * The demo link.
     *
     * @var string
     */
    public $demo_link;


    /**
     * The confirmation link.
     *
     * @var string
     */
    public $confirmation_link;


    /**
     * The delete link.
     *
     * @var string
     */
    public $delete_link;


    /**
     * The warning.
     *
     * @var string
     */
    public $warning;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(operator $operator)
    {
        $sp_key = bin2hex(random_bytes(5));
        $sd_key = bin2hex(random_bytes(5));
        $operator->sp_key = $sp_key;
        $operator->sd_key = $sd_key;
        $operator->mrk_email_count = $operator->mrk_email_count + 1;
        $operator->save();

        if ($operator->mrk_email_count >= 2) {
            $this->warning = "If you failed to respond to this email, your account at ispbills.com will be deleted.";
        } else {
            $this->warning = "";
        }
        $this->operator = $operator;
        $this->demo_link = route('demo.index');
        $this->confirmation_link = route('operators.self-provisioning.create', ['operator' => $operator->id, 'sp_key' => $sp_key]);
        $this->delete_link = route('operators.self-deletion.create', ['operator' => $operator->id, 'sd_key' => $sd_key]);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('mail.mailers.sales.from.address'), config('mail.mailers.sales.from.name'))
            ->markdown('emails.sales.greeting');
    }
}
