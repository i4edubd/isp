<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\customer_payment;
use App\Models\payment_gateway;
use Illuminate\Http\Request;

class CustomerPaymentGatewayRouteController extends Controller
{
    /**
     * Route to Payment Gateway
     *
     * @param  \App\Models\payment_gateway  $payment_gateway
     * @param  \App\Models\customer_payment  $customer_payment
     * @return \Illuminate\Http\Response
     */
    public static function pgwRoute(payment_gateway $payment_gateway, customer_payment $customer_payment)
    {

        switch ($payment_gateway->provider_name) {
            case 'easypayway':
                return redirect()->route('easypayway.customer_payment.initiate', ['customer_payment' => $customer_payment->id]);
                break;

            case 'sslcommerz':
                return redirect()->route('sslcommerz.customer_payment.initiate', ['customer_payment' => $customer_payment->id]);
                break;

            case 'bkash_checkout':
                return redirect()->route('bkash.customer_payment.initiate', ['customer_payment' => $customer_payment->id]);
                break;

            case 'recharge_card':
                $customer_payment->type = 'RechargeCard';
                $customer_payment->save();
                return redirect()->route('customer_payments.recharge-cards.create', ['customer_payment' => $customer_payment->id]);
                break;

            case 'nagad':
                return redirect()->route('nagad.customer_payment.initiate', ['customer_payment' => $customer_payment->id]);
                break;

            case 'walletmix':
                return redirect()->route('walletmix.customer_payment.initiate', ['customer_payment' => $customer_payment->id]);
                break;

            case 'bdsmartpay':
                return redirect()->route('bdsmartpay.customer_payment.initiate', ['customer_payment' => $customer_payment->id]);
                break;

            case 'aamarpay':
                return redirect()->route('aamarpay.customer_payment.initiate', ['customer_payment' => $customer_payment->id]);
                break;

            case 'send_money':
                return redirect()->route('send_money.customer_payment.create', ['customer_payment' => $customer_payment->id]);
                break;

            case 'bkash_payment':
                return redirect()->route('bkash_payment.customer_payment.create', ['customer_payment' => $customer_payment->id]);
                break;

            case 'bkash_tokenized_checkout':
                return redirect()->route('bkash_tokenized.customer_payment.initiate', ['customer_payment' => $customer_payment->id]);
                break;
        }
    }
}
