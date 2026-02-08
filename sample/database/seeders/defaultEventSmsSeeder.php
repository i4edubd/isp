<?php

namespace Database\Seeders;

use App\Models\event_sms;
use Illuminate\Database\Seeder;

class defaultEventSmsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        if (config('local.host_type') !== 'central') {
            return 0;
        }

        event_sms::updateOrCreate(
            [
                'operator_id' => 0,
                'event' => 'CONFIRMATION_SMS'
            ],
            [
                'readable_event' => 'Due Notifier Confirmation SMS',
                'variables' => '[PAYMENT_DATE],[CUSTOMERS_COUNT]',
                'default_sms' => 'Dear Sir, [CUSTOMERS_COUNT] customers has been notified for payments on [PAYMENT_DATE]',
                'default_sms_bn' => 'স্যার, [PAYMENT_DATE] তারিখে পেমেন্ট করার জন্য [CUSTOMERS_COUNT] জন কাস্টমারকে নোটিশ পাঠানো হয়েছে ।',
                'status' => 'enabled'
            ]
        );

        event_sms::updateOrCreate(
            [
                'operator_id' => 0,
                'event' => 'OTP'
            ],
            [
                'readable_event' => 'OTP',
                'variables' => '[OTP]',
                'default_sms' => 'The code for use at ' . route('root') . ' is: [OTP]',
                'default_sms_bn' => route('root') . '  এ ব্যবহার করার জন্য কোডঃ [OTP]',
                'status' => 'enabled'
            ]
        );

        event_sms::updateOrCreate(
            [
                'operator_id' => 0,
                'event' => 'CUSTOMER_ID'
            ],
            [
                'readable_event' => 'Recover Customer ID',
                'variables' =>  '[CUSTOMER_ID]',
                'default_sms' => 'Your Customer ID at ' . route('root') . ' is: [CUSTOMER_ID]',
                'default_sms_bn' => route('root') . ' এ আপনার কাস্টমার আইডিঃ [CUSTOMER_ID]',
                'status' => 'enabled'
            ]
        );

        event_sms::updateOrCreate(
            [
                'operator_id' => 0,
                'event' => 'NO_BALANCE_NOTICE'
            ],
            [
                'readable_event' => 'When No Internet Balance',
                'variables' =>  '',
                'default_sms' => 'Your internet balance is 0. Purchase your favourite internet package at: ' . route('root'),
                'default_sms_bn' => 'স্যার, আপনার ইন্টারনেট ব্যালেন্স শেষ। পছন্দের প্যাকেজ কিনতে ভিজিট করুনঃ ' . route('root'),
                'status' => 'enabled'
            ]
        );

        event_sms::updateOrCreate(
            [
                'operator_id' => 0,
                'event' => 'WELCOME_MESSAGE_FOR_HOTSPOT'
            ],
            [
                'readable_event' => 'Welcome Message for Hotspot Users',
                'variables' =>  '[COMPANY_NAME]',
                'default_sms' => 'Thank you for registration with [COMPANY_NAME] ! Purchase your favourite internet package at: ' . route('root'),
                'default_sms_bn' => '[COMPANY_NAME] এর সাথে যুক্ত হওয়ার জন্য ধন্যবাদ। আপনার পছন্দের প্যাকেজ কিনতে ভিজিট করুনঃ ' . route('root'),
                'status' => 'enabled'
            ]
        );

        event_sms::updateOrCreate(
            [
                'operator_id' => 0,
                'event' => 'WELCOME_MESSAGE_FOR_PPP'
            ],
            [
                'readable_event' => 'Welcome Message for PPPoE Users',
                'variables' => '[COMPANY_NAME],[CUSTOMER_ID],[USERNAME],[PASSWORD],[HELPLINE]',
                'default_sms' => 'Thank you for joining with us! Your Customer ID : [CUSTOMER_ID] , username : [USERNAME] , password : [PASSWORD] , HelpLine : [HELPLINE] , For more details, please visit: ' . route('root'),
                'default_sms_bn' => '[COMPANY_NAME] এর সাথে যুক্ত হওয়ার জন্য ধন্যবাদ। আপনার কাস্টমার আইডিঃ [CUSTOMER_ID]  ইউসার নেমঃ [USERNAME] , পাসওয়ার্ডঃ [PASSWORD] , হেল্প লাইনঃ [HELPLINE] । আরও জানতে ভিজিট  করুনঃ ' . route('root'),
                'status' => 'enabled'
            ]
        );

        event_sms::updateOrCreate(
            [
                'operator_id' => 0,
                'event' => 'BALANCE_ADDED_TO_OPERATOR_ACCOUNT'
            ],
            [
                'readable_event' => 'Balance added to operator account',
                'variables' =>  '[AMOUNT],[CURRENCY]',
                'default_sms' => 'Dear Sir, [AMOUNT] [CURRENCY] has been deposited to your account at ' .  route('root'),
                'default_sms_bn' => 'স্যার, ' . route('root') . ' এ আপনার একাউন্টে [AMOUNT] [CURRENCY] জমা করা হয়েছে ।',
                'status' => 'enabled'
            ]
        );

        event_sms::updateOrCreate(
            [
                'operator_id' => 0,
                'event' => 'PAYMENT_CONFIRMATION_MESSAGE'
            ],
            [
                'readable_event' => 'Payment Confirmation Message',
                'variables' =>  '[AMOUNT],[CURRENCY],[COMPANY_NAME],[HELPLINE]',
                'default_sms' => 'Dear Sir, [AMOUNT] [CURRENCY] payment has been received at ' . route('root') . ' . For any query please call : [HELPLINE]',
                'default_sms_bn' => 'স্যার, ' . route('root') . ' এ [AMOUNT] [CURRENCY] পেমেন্ট রিসিভ করা হয়েছে - [COMPANY_NAME] । প্রয়োজনে কল করুনঃ [HELPLINE]',
                'status' => 'enabled'
            ]
        );

        event_sms::updateOrCreate(
            [
                'operator_id' => 0,
                'event' => 'SEND_MONEY_NOTIFICATION'
            ],
            [
                'readable_event' => 'Send Money Notification',
                'variables' =>  '[MOBILE],[AMOUNT],[CUSTOMER_ID]',
                'default_sms' => 'Dear Sir, Customer of ID: [CUSTOMER_ID] has sent money to [MOBILE] , amount: [AMOUNT] . Please Check.',
                'default_sms_bn' => 'স্যার, [MOBILE] থেকে [AMOUNT] টাকা পাঠিয়েছে । কাস্টমার আইডিঃ [CUSTOMER_ID] । চেক করুন ।',
                'status' => 'enabled'
            ]
        );

        event_sms::updateOrCreate(
            [
                'operator_id' => 0,
                'event' => 'EXPIRATION_NOTIFICATION'
            ],
            [
                'readable_event' => 'Expiration Notification',
                'variables' =>  '[EXPIRATION_DATE],[COMPANY_NAME],[HELPLINE]',
                'default_sms' => 'Dear Sir, Your account will expire on [EXPIRATION_DATE] . Please visit ' . route('root') . ' to recharge your account. -  [COMPANY_NAME] . For any query please call : [HELPLINE]',
                'default_sms_bn' => 'স্যার, আপনার অ্যাকাউন্টের মেয়াদ [EXPIRATION_DATE] তারিখে শেষ হবে । অনলাইনে রিচার্জ করতে ভিজিট করুনঃ ' . route('root') . ' - [COMPANY_NAME] । প্রয়োজনে কল করুনঃ [HELPLINE]',
                'status' => 'disabled'
            ]
        );

        event_sms::updateOrCreate(
            [
                'operator_id' => 0,
                'event' => 'CARD_RECHARGE_SUCCESSFUL_MESSAGE'
            ],
            [
                'readable_event' => 'Card recharge successful message',
                'variables' =>  '[AMOUNT],[CURRENCY],[COMPANY_NAME],[HELPLINE]',
                'default_sms' => 'Dear Sir, Your recharge of amount [AMOUNT] [CURRENCY] at ' . route('root') . ' was successful. For any query please call : [HELPLINE]',
                'default_sms_bn' => 'স্যার, ' . route('root') . ' এ আপনার [AMOUNT] টাকা রিচার্জ সফল হয়েছে। প্রয়োজনে কল করুনঃ [HELPLINE]',
                'status' => 'disabled'
            ]
        );

        event_sms::updateOrCreate(
            [
                'operator_id' => 0,
                'event' => 'DUE_NOTICE'
            ],
            [
                'readable_event' => 'Due Notice',
                'variables' =>  '[AMOUNT],[CURRENCY],[PAYMENT_DATE],[PAYMENT_LINK],[COMPANY_NAME],[HELPLINE]',
                'default_sms' => 'Dear customer, Please pay the due amount [AMOUNT] [CURRENCY] by [PAYMENT_DATE] at [PAYMENT_LINK] - [COMPANY_NAME]. For any query please call : [HELPLINE]',
                'default_sms_bn' => 'স্যার, আপনার বকেয়া ইন্টারনেট বিল [AMOUNT] টাকা [PAYMENT_DATE] তারিখের মধ্যে পরিশোধ করতে ভিজিট করুনঃ [PAYMENT_LINK] - [COMPANY_NAME] । প্রয়োজনে কল করুনঃ [HELPLINE]',
                'status' => 'enabled'
            ]
        );
    }
}
