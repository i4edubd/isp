<?php

namespace App\Http\Controllers;

use App\Models\country;
use App\Models\customer_bill;
use App\Models\due_date_reminder;
use App\Models\event_sms;
use App\Models\Freeradius\customer;
use App\Models\operator;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SmsGenerator extends Controller
{

    /**
     * Get SMS String
     *
     * @param  \App\Models\operator  $operator
     * @param  string  $payment_date
     * @param  string  $customers_count
     * @return string
     */
    public static function confirmationSms(operator $operator, string $payment_date, string $customers_count)
    {

        $event_sms = event_sms::where('event', 'CONFIRMATION_SMS')
            ->where('operator_id', $operator->id)
            ->firstOr(function () {
                return event_sms::where('event', 'CONFIRMATION_SMS')
                    ->where('operator_id', 0)
                    ->first();
            });

        if ($event_sms->status == 'disabled') {
            return "";
        }

        $sms = self::getSMS($operator, $event_sms);
        $sms =  Str::replace('[PAYMENT_DATE]', $payment_date, $sms);
        $sms =  Str::replace('[CUSTOMERS_COUNT]', $customers_count, $sms);

        return $sms;
    }

    /**
     * Get SMS String
     *
     * @param  \App\Models\operator  $operator
     * @param  string  $otp
     * @return string
     */
    public static function OTP(operator $operator, string $otp)
    {
        $event_sms = event_sms::where('event', 'OTP')
            ->where('operator_id', $operator->id)
            ->firstOr(function () {
                return event_sms::where('event', 'OTP')
                    ->where('operator_id', 0)
                    ->first();
            });

        if ($event_sms->status == 'disabled') {
            return "";
        }

        $sms = self::getSMS($operator, $event_sms);
        $sms =  Str::replace('[OTP]', $otp, $sms);

        // Until android webview supports WebOTP API
        return $sms;

        $url = config('app.url');
        $url = Str::afterLast($url, '//');
        $url = Str::before($url, '/');

        $sms = $sms . "\n" . '@' . $url . ' ' . '#' . $otp;
        return $sms;
    }

    /**
     * Get SMS String
     *
     * @param  \App\Models\operator  $operator
     * @param  string  $customerId
     * @return string
     */
    public static function customerId(operator $operator, string $customerId)
    {
        $event_sms = event_sms::where('event', 'CUSTOMER_ID')
            ->where('operator_id', $operator->id)
            ->firstOr(function () {
                return event_sms::where('event', 'CUSTOMER_ID')
                    ->where('operator_id', 0)
                    ->first();
            });

        if ($event_sms->status == 'disabled') {
            return "";
        }

        $sms = self::getSMS($operator, $event_sms);
        $sms =  Str::replace('[CUSTOMER_ID]', $customerId, $sms);
        return $sms;
    }

    /**
     * Get SMS String
     *
     * @param  \App\Models\operator  $operator
     * @return string
     */
    public static function noBalanceNotice(operator $operator)
    {
        $event_sms = event_sms::where('event', 'NO_BALANCE_NOTICE')
            ->where('operator_id', $operator->id)
            ->firstOr(function () {
                return event_sms::where('event', 'NO_BALANCE_NOTICE')
                    ->where('operator_id', 0)
                    ->first();
            });

        if ($event_sms->status == 'disabled') {
            return "";
        }

        return self::getSMS($operator, $event_sms);
    }

    /**
     * Get SMS String
     *
     * @param  \App\Models\operator  $operator
     * @return string
     */
    public static function welcomeMsgHotspot(operator $operator)
    {
        $event_sms = event_sms::where('event', 'WELCOME_MESSAGE_FOR_HOTSPOT')
            ->where('operator_id', $operator->id)
            ->firstOr(function () {
                return event_sms::where('event', 'WELCOME_MESSAGE_FOR_HOTSPOT')
                    ->where('operator_id', 0)
                    ->first();
            });

        if ($event_sms->status == 'disabled') {
            return "";
        }

        $sms = self::getSMS($operator, $event_sms);

        $company = strlen($operator->company_in_native_lang) ? $operator->company_in_native_lang : $operator->company;

        $sms =  Str::replace('[COMPANY_NAME]', $company, $sms);

        return $sms;
    }

    /**
     * Get SMS String
     *
     * @param  \App\Models\operator  $operator
     * @param \App\Models\Freeradius\customer $customer
     * @return string
     */
    public static function welcomeMsgPpp(operator $operator, customer $customer)
    {
        $event_sms = event_sms::where('event', 'WELCOME_MESSAGE_FOR_PPP')
            ->where('operator_id', $operator->id)
            ->firstOr(function () {
                return event_sms::where('event', 'WELCOME_MESSAGE_FOR_PPP')
                    ->where('operator_id', 0)
                    ->first();
            });

        if ($event_sms->status == 'disabled') {
            return "";
        }

        $company = strlen($operator->company_in_native_lang) ? $operator->company_in_native_lang : $operator->company;

        $sms = self::getSMS($operator, $event_sms);
        $sms =  Str::replace('[COMPANY_NAME]', $company, $sms);
        $sms =  Str::replace('[CUSTOMER_ID]', $customer->id, $sms);
        $sms =  Str::replace('[USERNAME]', $customer->username, $sms);
        $sms =  Str::replace('[PASSWORD]', $customer->password, $sms);
        $sms =  Str::replace('[HELPLINE]', $operator->helpline, $sms);
        return $sms;
    }

    /**
     * Get SMS String
     *
     * @param  \App\Models\operator  $operator
     * @param string $amount
     * @return string
     */
    public static function balanceAddedMsg(operator $operator, string $amount)
    {
        $event_sms = event_sms::where('event', 'BALANCE_ADDED_TO_OPERATOR_ACCOUNT')
            ->where('operator_id', $operator->id)
            ->firstOr(function () {
                return event_sms::where('event', 'BALANCE_ADDED_TO_OPERATOR_ACCOUNT')
                    ->where('operator_id', 0)
                    ->first();
            });

        if ($event_sms->status == 'disabled') {
            return "";
        }

        $country =  country::find($operator->country_id);
        if ($country) {
            $currency_code = $country->currency_code;
        } else {
            $currency_code = config('consumer.currency');
        }

        $sms = self::getSMS($operator, $event_sms);
        $sms =  Str::replace('[AMOUNT]', $amount, $sms);
        $sms =  Str::replace('[CURRENCY]', $currency_code, $sms);
        return $sms;
    }

    /**
     * Get SMS String
     *
     * @param  \App\Models\operator  $operator
     * @param string $amount
     * @return string
     */
    public static function paymentConfirmationMsg(operator $operator, string $amount)
    {
        $event_sms = event_sms::where('event', 'PAYMENT_CONFIRMATION_MESSAGE')
            ->where('operator_id', $operator->id)
            ->firstOr(function () {
                return event_sms::where('event', 'PAYMENT_CONFIRMATION_MESSAGE')
                    ->where('operator_id', 0)
                    ->first();
            });

        if ($event_sms->status == 'disabled') {
            return "";
        }

        $country =  country::find($operator->country_id);
        if ($country) {
            $currency_code = $country->currency_code;
        } else {
            $currency_code = config('consumer.currency');
        }
        $company = strlen($operator->company_in_native_lang) ? $operator->company_in_native_lang : $operator->company;

        $sms = self::getSMS($operator, $event_sms);
        $sms =  Str::replace('[AMOUNT]', $amount, $sms);
        $sms =  Str::replace('[CURRENCY]', $currency_code, $sms);
        $sms =  Str::replace('[COMPANY_NAME]', $company, $sms);
        $sms =  Str::replace('[HELPLINE]', $operator->helpline, $sms);
        return $sms;
    }

    /**
     * Get SMS String
     *
     * @param  \App\Models\operator  $operator
     * @param string $amount
     * @param string $mobile
     * @param string $customer_id
     * @return string
     */
    public static function sendMoneyNotification(operator $operator, string $amount, string $mobile, string $customer_id)
    {
        $event_sms = event_sms::where('event', 'SEND_MONEY_NOTIFICATION')
            ->where('operator_id', $operator->id)
            ->firstOr(function () {
                return event_sms::where('event', 'SEND_MONEY_NOTIFICATION')
                    ->where('operator_id', 0)
                    ->first();
            });

        if ($event_sms->status == 'disabled') {
            return "";
        }

        $sms = self::getSMS($operator, $event_sms);
        $sms =  Str::replace('[MOBILE]', $mobile, $sms);
        $sms =  Str::replace('[AMOUNT]', $amount, $sms);
        $sms =  Str::replace('[CUSTOMER_ID]', $customer_id, $sms);
        return $sms;
    }

    /**
     * Get SMS String
     *
     * @param  \App\Models\operator  $operator
     * @param \App\Models\Freeradius\customer $customer
     * @return string
     */
    public static function expirationNotificationMsg(operator $operator, customer $customer)
    {
        $event_sms = event_sms::where('event', 'EXPIRATION_NOTIFICATION')
            ->where('operator_id', $operator->id)
            ->firstOr(function () {
                return event_sms::where('event', 'EXPIRATION_NOTIFICATION')
                    ->where('operator_id', 0)
                    ->first();
            });

        if (!$event_sms) {
            return "";
        }

        if ($event_sms->status == 'disabled') {
            return "";
        }

        $company = strlen($operator->company_in_native_lang) ? $operator->company_in_native_lang : $operator->company;

        $sms = self::getSMS($operator, $event_sms);
        $sms =  Str::replace('[COMPANY_NAME]', $company, $sms);
        $sms =  Str::replace('[EXPIRATION_DATE]', $customer->package_expired_at, $sms);
        $sms =  Str::replace('[HELPLINE]', $operator->helpline, $sms);
        return $sms;
    }

    /**
     * Get SMS String
     *
     * @param  \App\Models\operator  $operator
     * @param string $amount
     * @return string
     */
    public static function cardRechargeSuccessfulMsg(operator $operator, string $amount)
    {
        $event_sms = event_sms::where('event', 'CARD_RECHARGE_SUCCESSFUL_MESSAGE')
            ->where('operator_id', $operator->id)
            ->firstOr(function () {
                return event_sms::where('event', 'CARD_RECHARGE_SUCCESSFUL_MESSAGE')
                    ->where('operator_id', 0)
                    ->first();
            });

        if (!$event_sms) {
            return "";
        }

        if ($event_sms->status == 'disabled') {
            return "";
        }

        $currency_code = getCurrency($operator->id);
        $company = strlen($operator->company_in_native_lang) ? $operator->company_in_native_lang : $operator->company;

        $sms = self::getSMS($operator, $event_sms);
        $sms =  Str::replace('[AMOUNT]', $amount, $sms);
        $sms =  Str::replace('[CURRENCY]', $currency_code, $sms);
        $sms =  Str::replace('[COMPANY_NAME]', $company, $sms);
        $sms =  Str::replace('[HELPLINE]', $operator->helpline, $sms);
        return $sms;
    }

    /**
     * Get SMS String
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\Freeradius\customer $customer
     * @return string
     */
    public static function dueNoticeMsg(operator $operator, customer $customer)
    {
        $event_sms = event_sms::where('event', 'DUE_NOTICE')
            ->where('operator_id', $operator->id)
            ->firstOr(function () {
                return event_sms::where('event', 'DUE_NOTICE')
                    ->where('operator_id', 0)
                    ->first();
            });

        if (!$event_sms) {
            return "";
        }

        if ($event_sms->status == 'disabled') {
            return "";
        }

        if ($customer->payment_status !== 'billed') {
            return "";
        }

        $customer_bills = customer_bill::where('operator_id', $customer->operator_id)->where('customer_id', $customer->id)->get();
        if (!$customer_bills) {
            return "";
        }

        $amount = $customer_bills->sum('amount');
        if (!$amount) {
            return "";
        }

        $last_bill = $customer_bills->last();
        if (!$last_bill) {
            return "";
        }

        $currency_code = getCurrency($operator->id);
        $payment_date = $last_bill->due_date;
        $group_admin = CacheController::getOperator($operator->mgid);
        $web_root = is_null($group_admin->web_root) ? route('root') : $group_admin->web_root;
        $payment_link = $web_root . "?cid=" . $last_bill->customer_id . "&bid=" . $last_bill->id;
        $company = strlen($operator->company_in_native_lang) ? $operator->company_in_native_lang : $operator->company;

        $sms = self::getSMS($operator, $event_sms);
        $sms =  Str::replace('[AMOUNT]', $amount, $sms);
        $sms =  Str::replace('[CURRENCY]', $currency_code, $sms);
        $sms =  Str::replace('[PAYMENT_DATE]', $payment_date, $sms);
        $sms =  Str::replace('[PAYMENT_LINK]', $payment_link, $sms);
        $sms =  Str::replace('[COMPANY_NAME]', $company, $sms);
        $sms =  Str::replace('[HELPLINE]', $operator->helpline, $sms);
        return $sms;
    }

    /**
     * Get SMS String
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\Freeradius\customer $customer
     * @return string
     */
    public static function dueReminderMsg(operator $operator, due_date_reminder $due_date_reminder, customer $customer)
    {

        if ($customer->payment_status !== 'billed') {
            return "";
        }

        $customer_bills = customer_bill::where('operator_id', $customer->operator_id)->where('customer_id', $customer->id)->get();
        if (!$customer_bills) {
            return "";
        }

        $amount = $customer_bills->sum('amount');
        if (!$amount) {
            return "";
        }

        $last_bill = $customer_bills->last();
        if (!$last_bill) {
            return "";
        }

        $currency_code = getCurrency($operator->id);
        $payment_date = $last_bill->due_date;
        $group_admin = CacheController::getOperator($operator->mgid);
        $web_root = is_null($group_admin->web_root) ? route('root') : $group_admin->web_root;
        $payment_link = $web_root . "?cid=" . $last_bill->customer_id . "&bid=" . $last_bill->id;
        $company = strlen($operator->company_in_native_lang) ? $operator->company_in_native_lang : $operator->company;

        $sms =  $due_date_reminder->message;
        $sms =  Str::replace('[AMOUNT]', $amount, $sms);
        $sms =  Str::replace('[CURRENCY]', $currency_code, $sms);
        $sms =  Str::replace('[PAYMENT_DATE]', $payment_date, $sms);
        $sms =  Str::replace('[payment_date]', $payment_date, $sms);
        $sms =  Str::replace('[PAYMENT_LINK]', $payment_link, $sms);
        $sms =  Str::replace('[COMPANY_NAME]', $company, $sms);
        $sms =  Str::replace('[HELPLINE]', $operator->helpline, $sms);
        return $sms;
    }

    /**
     * Get SMS String
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\event_sms $event_sms
     * @return string
     */
    public static function getSMS(operator $operator, event_sms $event_sms)
    {
        $lang_code = strlen($operator->lang_code) ? $operator->lang_code : config('consumer.lang_code');

        $attribute = 'default_sms_' . $lang_code;

        if (strlen($event_sms->operator_sms)) {
            return $event_sms->operator_sms;
        } else {
            return ($event_sms->getAttributeValue($attribute)) ? $event_sms->getAttributeValue($attribute) : $event_sms->default_sms;
        }
    }
}
