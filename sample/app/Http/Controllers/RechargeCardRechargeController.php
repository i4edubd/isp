<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CustomerPayment\CustomersPaymentProcessController;
use App\Http\Controllers\enum\PaymentPurpose;
use App\Models\customer_bill;
use App\Models\customer_payment;
use App\Models\Freeradius\customer;
use App\Models\recharge_card;
use Carbon\Carbon;
use Exception;

class RechargeCardRechargeController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @param  \App\Models\recharge_card $recharge_card
     * @return \Illuminate\Http\Response
     */
    public static function recharge(customer $customer, recharge_card $recharge_card)
    {
        try {
            self::checkGatePass($recharge_card);

            $package = CacheController::getPackage($recharge_card->package_id);
            $master_package = $package->master_package;

            $start_and_stop_date = BillingHelper::getStartAndStopDate($customer, $package);
            $mer_txnid = random_int(1000, 9999) . Carbon::now(config('app.timezone'))->timestamp;

            $customer_payment = new customer_payment();
            $customer_payment->mgid = $customer->mgid;
            $customer_payment->gid = $customer->gid;
            $customer_payment->operator_id = $customer->operator_id;
            $customer_payment->cash_collector_id = 0;
            $customer_payment->customer_id = $customer->id;
            $customer_payment->package_id = $package->id;
            $customer_payment->validity_period = $master_package->validity;
            $customer_payment->previous_package_id = $customer->package_id;
            $customer_payment->payment_gateway_id = 0;
            $customer_payment->payment_gateway_name = 'recharge_card';
            $customer_payment->mobile = $customer->mobile;
            $customer_payment->name = $customer->name;
            $customer_payment->username = $customer->username;
            $customer_payment->type = 'RechargeCard';
            $customer_payment->pay_status = 'Successful';
            $customer_payment->amount_paid = $package->price;
            $customer_payment->store_amount = $package->price;
            $customer_payment->transaction_fee = 0;
            $customer_payment->mer_txnid = $mer_txnid;
            $customer_payment->pgw_txnid = $recharge_card->pin;
            $customer_payment->bank_txnid = $recharge_card->pin;
            $customer_payment->date = date(config('app.date_format'));
            $customer_payment->week = date(config('app.week_format'));
            $customer_payment->month = date(config('app.month_format'));
            $customer_payment->year = date(config('app.year_format'));
            $customer_payment->used = 0;
            $customer_payment->recharge_card_id = $recharge_card->id;
            $customer_payment->package_started_at = $start_and_stop_date->get('start_date');
            $customer_payment->package_expired_at = $start_and_stop_date->get('stop_date');
            $customer_payment->purpose = PaymentPurpose::PACKAGE_PURCHASE->value;
            $customer_payment->save();

            //process payment
            CustomersPaymentProcessController::doStore($customer_payment);

            self::doAccounting($recharge_card, $customer_payment);

            return 1;
        } catch (Exception $e) {
            abort(500, $e);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @param  \App\Models\recharge_card $recharge_card
     * @param  \App\Models\customer_bill $customer_bill
     * @return \Illuminate\Http\Response
     */
    public static function payBill(customer $customer, recharge_card $recharge_card, customer_bill $customer_bill)
    {
        try {
            self::checkGatePass($recharge_card);

            $mer_txnid = random_int(1000, 9999) . Carbon::now(config('app.timezone'))->timestamp;
            $billing_profile = CacheController::getBillingProfile($customer->billing_profile_id);

            $customer_payment = new customer_payment();
            $customer_payment->mgid = $customer->mgid;
            $customer_payment->gid = $customer->gid;
            $customer_payment->operator_id = $customer->operator_id;
            $customer_payment->cash_collector_id = 0;
            $customer_payment->parent_customer_id = $customer->parent_id;
            $customer_payment->customer_id = $customer->id;
            $customer_payment->customer_bill_id = $customer_bill->id;
            $customer_payment->package_id = $customer->package_id;
            $customer_payment->validity_period = $customer_bill->validity_period;
            $customer_payment->previous_package_id = $customer->package_id;
            $customer_payment->payment_gateway_id = 0;
            $customer_payment->payment_gateway_name = 'recharge_card';
            $customer_payment->mobile = $customer->mobile;
            $customer_payment->name = $customer->name;
            $customer_payment->username = $customer->username;
            $customer_payment->type = 'RechargeCard';
            $customer_payment->pay_status = 'Successful';
            $customer_payment->amount_paid = $customer_bill->amount;
            $customer_payment->store_amount = $customer_bill->amount;
            $customer_payment->transaction_fee = 0;
            $customer_payment->mer_txnid = $mer_txnid;
            $customer_payment->pgw_txnid = $recharge_card->pin;
            $customer_payment->bank_txnid = $recharge_card->pin;
            $customer_payment->date = date(config('app.date_format'));
            $customer_payment->week = date(config('app.week_format'));
            $customer_payment->month = date(config('app.month_format'));
            $customer_payment->year = date(config('app.year_format'));
            $customer_payment->used = 0;
            $customer_payment->recharge_card_id = $recharge_card->id;
            $customer_payment->package_started_at = Carbon::now(getTimeZone($customer->operator_id))->isoFormat(config('app.expiry_time_format'));
            $customer_payment->package_expired_at =  Carbon::createFromFormat(config('app.date_format'), $billing_profile->next_payment_date, getTimeZone($customer->operator_id))->isoFormat(config('app.expiry_time_format'));
            $customer_payment->purpose = $customer_bill->purpose;
            $customer_payment->save();

            //process payment
            CustomersPaymentProcessController::doStore($customer_payment);

            self::doAccounting($recharge_card, $customer_payment);

            return 1;
        } catch (Exception $e) {
            abort(500, $e);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Models\recharge_card $recharge_card
     * @param  \App\Models\customer_payment $customer_payment
     * @return \Illuminate\Http\Response
     */
    public static function makePayment(recharge_card $recharge_card, customer_payment $customer_payment)
    {
        //update payment
        $customer_payment->recharge_card_id = $recharge_card->id;
        $customer_payment->pgw_txnid = $recharge_card->pin;
        $customer_payment->bank_txnid = $recharge_card->pin;
        $customer_payment->pay_status = 'Successful';
        $customer_payment->store_amount = $customer_payment->amount_paid;
        $customer_payment->transaction_fee = 0;
        $customer_payment->save();

        //process payment
        CustomersPaymentProcessController::store($customer_payment);

        self::doAccounting($recharge_card, $customer_payment);
    }

    /**
     * check Gate Pass
     *
     * @param  \App\Models\recharge_card $recharge_card
     * @return \Illuminate\Http\Response
     */
    public static function checkGatePass(recharge_card $recharge_card)
    {
        if ($recharge_card->status == 'used') {
            abort(500, 'The refill card has already been used!');
        }

        if ($recharge_card->locked != 1) {
            abort(500, 'The refill card is not locked!');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Models\recharge_card $recharge_card
     * @param  \App\Models\customer_payment $customer_payment
     * @return \Illuminate\Http\Response
     */
    public static function doAccounting(recharge_card $recharge_card, customer_payment $customer_payment)
    {
        // update card
        $recharge_card->customer_id = $customer_payment->customer_id;
        $recharge_card->mobile = $customer_payment->mobile;
        $recharge_card->status = 'used';
        $recharge_card->used_date = date(config('app.date_format'));
        $recharge_card->used_month = date(config('app.month_format'));
        $recharge_card->used_year = date(config('app.year_format'));
        $recharge_card->save();

        // card distributor accounting
        $new_due = $customer_payment->amount_paid - $recharge_card->commission;
        $recharge_card->distributor->amount_due = $recharge_card->distributor->amount_due + $new_due;
        $recharge_card->distributor->save();
    }
}
