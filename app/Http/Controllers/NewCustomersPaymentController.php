<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CustomerPayment\CustomersPaymentProcessController;
use App\Http\Controllers\enum\PaymentPurpose;
use App\Models\customer_payment;
use App\Models\Freeradius\customer;
use App\Models\package;
use Carbon\Carbon;

class NewCustomersPaymentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return void
     */
    public static function store(customer $customer)
    {
        $package = package::findOrFail($customer->package_id);
        $master_package = $package->master_package;

        switch ($customer->connection_type) {
            case 'PPPoE':
                $validity_minute = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_started_at, getTimeZone($customer->operator_id), 'en')->diffInMinutes(Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($customer->operator_id), 'en'));
                $amount_paid = round(($package->price / $master_package->total_minute) * $validity_minute);
                $validity = Carbon::now(getTimeZone($customer->operator_id))->diffInDays(Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($customer->operator_id), 'en')) + 1;
                break;
            case 'Hotspot':
                $amount_paid = $package->price;
                $validity = $master_package->validity;
                break;
            case 'StaticIp':
            case 'Other':
                return 0;
                break;
        }

        $mer_txnid = random_int(1000, 9999) . Carbon::now(config('app.timezone'))->timestamp;

        $customer_payment = new customer_payment();
        $customer_payment->mgid = $customer->mgid;
        $customer_payment->gid = $customer->gid;
        $customer_payment->operator_id = $customer->operator_id;
        $customer_payment->customer_id = $customer->id;
        $customer_payment->customer_bill_id = 0;
        $customer_payment->package_id = $customer->package_id;
        $customer_payment->validity_period = $validity;
        $customer_payment->payment_gateway_id = 0;
        $customer_payment->payment_gateway_name = 'Cash';
        $customer_payment->mobile = $customer->mobile;
        $customer_payment->name = $customer->name;
        $customer_payment->username = $customer->username;
        $customer_payment->type = 'Cash';
        $customer_payment->payment_mode = 'postpaid';
        $customer_payment->pay_status = 'Successful';
        $customer_payment->amount_paid = $amount_paid;
        $customer_payment->store_amount = $amount_paid;
        $customer_payment->transaction_fee = 0;
        $customer_payment->mer_txnid = $mer_txnid;
        $customer_payment->date = date(config('app.date_format'));
        $customer_payment->week = date(config('app.week_format'));
        $customer_payment->month = date(config('app.month_format'));
        $customer_payment->year = date(config('app.year_format'));
        $customer_payment->used = 0;
        $customer_payment->require_sms_notice = 0;
        $customer_payment->package_started_at = $customer->package_started_at;
        $customer_payment->package_expired_at = $customer->package_expired_at;
        $customer_payment->purpose = PaymentPurpose::NEW_CUSTOMER->value;
        $customer_payment->save();
        CustomersPaymentProcessController::store($customer_payment);
    }
}
