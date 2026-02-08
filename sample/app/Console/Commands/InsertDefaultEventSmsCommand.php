<?php

namespace App\Console\Commands;

use App\Models\event_sms;
use Illuminate\Console\Command;

class InsertDefaultEventSmsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insert:default_event_sms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert Default Event SMS';

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
        if (config('local.host_type') !== 'central') {
            return 0;
        }

        $event_smses = event_sms::where('operator_id', 0)->get();

        // clean previous event sms
        foreach ($event_smses as $event_sms) {
            $event_sms->delete();
        }

        // DueDateNotificationController : 150
        event_sms::create([
            'operator_id' => 0,
            'event' => 'CONFIRMATION_SMS',
            'readable_event' => 'Confirmation SMS',
            'variables' => '[PAYMENT_DATE],[CUSTOMERS_COUNT]',
            'default_sms' => '[PAYMENT_DATE] তারিখে পেমেন্ট করার জন্য [CUSTOMERS_COUNT] জন কাস্টমারকে নোটিশ পাঠানো হয়েছে ।',
            'status' => 'enabled',
        ]);

        // CustomerMobileVerificationController : 51
        // CustomersMacAddressReplaceController : 83
        // TempCustomerMobileVerificationController : 51
        event_sms::create([
            'operator_id' => 0,
            'event' => 'OTP',
            'readable_event' => 'OTP',
            'variables' => '[OTP]',
            'default_sms' => 'Your OTP code is: [OTP]',
            'status' => 'enabled',
        ]);

        // CustomerIdRecoveryController : 51
        event_sms::create([
            'operator_id' => 0,
            'event' => 'CUSTOMER_ID',
            'readable_event' => 'Recover Customer ID',
            'variables' => '[COMPANY_NAME],[CUSTOMER_ID]',
            'default_sms' => '[COMPANY_NAME] এ আপনার ব্যাবহারিত কাস্টমার আইডিঃ [CUSTOMER_ID]',
            'status' => 'enabled',
        ]);

        // SmsMessagesForCustomerController : 25
        event_sms::create([
            'operator_id' => 0,
            'event' => 'NO_BALANCE_NOTICE',
            'readable_event' => 'ব্যালেন্স শেষ হয়ে গেলে নোটিশ',
            'variables' => '',
            'default_sms' => 'ইন্টারনেট ব্যালেন্স শেষ। পছন্দের প্যাকেজ কিনতে ভিজিট করুনঃ ' . route('root'),
            'status' => 'enabled',
        ]);

        // SmsMessagesForCustomerController : 47
        event_sms::create([
            'operator_id' => 0,
            'event' => 'WELCOME_MESSAGE_FOR_HOTSPOT',
            'readable_event' => 'Welcome Message for Hotspot Users',
            'variables' => '[COMPANY_NAME]',
            'default_sms' => '[COMPANY_NAME] এর সাথে যুক্ত হওয়ার জন্য ধন্যবাদ। আপনার পছন্দের প্যাকেজ কিনতে ভিজিট করুনঃ ' . route('root'),
            'status' => 'enabled',
        ]);

        // SmsMessagesForCustomerController : 67
        event_sms::create([
            'operator_id' => 0,
            'event' => 'WELCOME_MESSAGE_FOR_PPP',
            'readable_event' => 'Welcome Message for PPPoE Users',
            'variables' => '[CUSTOMER_ID],[USERNAME],[PASSWORD],[HELPLINE]',
            'default_sms' => 'স্বাগতম,আপনার PPP আইডি:[USERNAME] ,পাস: [PASSWORD] এবং কাস্টমার আইডিঃ [CUSTOMER_ID]। আরও জানতে ভিজিট  করুনঃ ' . route('root'),
            'status' => 'enabled',
        ]);

        // AccountBalanceAddController : 57
        // SubOperatorAccountBalanceAddController: 56
        event_sms::create([
            'operator_id' => 0,
            'event' => 'BALANCE_ADDED_TO_OPERATOR_ACCOUNT',
            'readable_event' => 'Balance added to operator account',
            'variables' => '[AMOUNT]',
            'default_sms' => ' একাউন্টে [AMOUNT] টাকা টাকা জমা করা হয়েছে ।',
            'status' => 'enabled',
        ]);

        // CustomersPaymentProcessController : 127
        // CardDistributorPaymentsController: 128
        event_sms::create([
            'operator_id' => 0,
            'event' => 'PAYMENT_CONFIRMATION_MESSAGE',
            'readable_event' => 'Payment Confirmation Message',
            'variables' => '[AMOUNT],[HELPLINE]',
            'default_sms' => 'আপনার ইন্টারনেট বিলের [AMOUNT] টাকা সফলভাবে পরিশোধিত হয়েছে। প্রয়োজনে কল করুনঃ [HELPLINE]',
            'status' => 'enabled',
        ]);

        // BkashPaymentController : 121
        // SendMoneyController : 113
        event_sms::create([
            'operator_id' => 0,
            'event' => 'SEND_MONEY_NOTIFICATION',
            'readable_event' => 'Send Money Notification',
            'variables' => '[MOBILE],[AMOUNT],[CUSTOMER_ID]',
            'default_sms' => '[MOBILE] থেকে [AMOUNT] টাকা পাঠিয়েছে । কাস্টমার আইডিঃ [CUSTOMER_ID] ।',
            'status' => 'enabled',
        ]);
    }
}
