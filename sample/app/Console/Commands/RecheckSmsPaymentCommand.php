<?php

namespace App\Console\Commands;

use App\Http\Controllers\Sms\SmsPaymentController;
use App\Models\sms_payment;
use Illuminate\Console\Command;

class RecheckSmsPaymentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recheck:SmsPayment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recheck Sms Payment';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $sms_payments = sms_payment::where('pay_status', '!=', 'Successful')->get();

        foreach ($sms_payments as $sms_payment) {
            SmsPaymentController::recheckPayment($sms_payment);
        }

        return 0;
    }
}
