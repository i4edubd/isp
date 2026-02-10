<?php

namespace App\Http\Controllers;

use App\Models\customer_payment;
use App\Models\operators_income;
use App\Models\payment_gateway;
use Illuminate\Http\Request;

class PaymentGatewayServiceChargeController extends Controller
{

    /**
     * Process Payment Gateway Service Charge
     *
     * @param  \App\Models\customer_payment $customer_payment
     * @return  \App\Models\customer_payment
     */
    public static function customerPayment(customer_payment $customer_payment)
    {

        if ($customer_payment->payment_gateway_id == 0) {
            return $customer_payment;
        }

        $payment_gateway = payment_gateway::find($customer_payment->payment_gateway_id);

        if (!$payment_gateway) {
            return $customer_payment;
        }

        if ($payment_gateway->provider_name !== 'easypayway') {
            return $customer_payment;
        }

        // New transaction_fee
        $transaction_fee = $customer_payment->amount_paid * ($payment_gateway->service_charge_percentage / 100);

        // gateway_income
        $gateway_income = round($transaction_fee - $customer_payment->transaction_fee);

        // update customer_payment
        $customer_payment->store_amount = $customer_payment->amount_paid - $transaction_fee;
        $customer_payment->transaction_fee = $transaction_fee;
        $customer_payment->save();

        // record income
        if ($gateway_income) {
            $operators_income = new operators_income();
            $operators_income->operator_id = $payment_gateway->operator_id;
            $operators_income->payment_id = $customer_payment->id;
            $operators_income->source_operator_id = $customer_payment->operator_id;
            $operators_income->source = 'customers_payment';
            $operators_income->amount = $gateway_income;
            $operators_income->date = $customer_payment->date;
            $operators_income->week = $customer_payment->week;
            $operators_income->month = $customer_payment->month;
            $operators_income->year = $customer_payment->year;
            $operators_income->save();
        }

        return $customer_payment;
    }
}
