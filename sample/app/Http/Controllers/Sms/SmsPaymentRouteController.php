<?php

namespace App\Http\Controllers\Sms;

use App\Http\Controllers\Controller;
use App\Models\payment_gateway;
use App\Models\sms_payment;
use Illuminate\Http\Request;

class SmsPaymentRouteController extends Controller
{

    /**
     * Route to Payment Gateway
     *
     * @param  \App\Models\payment_gateway  $payment_gateway
     * @param  \App\Models\sms_payment  $sms_payment
     * @return \Illuminate\Http\Response
     */
    public static function smsPgwRoute(payment_gateway $payment_gateway, sms_payment $sms_payment)
    {
        switch ($payment_gateway->provider_name) {
            case 'easypayway':
                return redirect()->route('easypayway.sms_payment.initiate', ['sms_payment' => $sms_payment->id]);
                break;
            case 'sslcommerz':
                return redirect()->route('sslcommerz.sms_payment.initiate', ['sms_payment' => $sms_payment->id]);
                break;
            case 'bkash_checkout':
                return redirect()->route('bkash.sms_payment.initiate', ['sms_payment' => $sms_payment->id]);
                break;
            case 'nagad':
                return redirect()->route('nagad.sms_payment.initiate', ['sms_payment' => $sms_payment->id]);
                break;
            case 'bkash_tokenized_checkout':
                return redirect()->route('bkash_tokenized.sms_payment.initiate', ['sms_payment' => $sms_payment->id]);
                break;
            case 'shurjopay':
                return redirect()->route('shurjopay.sms_payment.create', ['sms_payment' => $sms_payment]);
                break;
        }
    }
}
