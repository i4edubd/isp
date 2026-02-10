<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\OperatorsOnlinePaymentController;
use App\Models\operators_online_payment;
use App\Models\payment_gateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class NagadPaymentGatewayForOperatorsOnlinePaymentController extends NagadPaymentGatewayBaseController
{

    /**
     * Initiate Payment through Nagad Payment Gateway
     *
     * @param \App\Models\operators_online_payment $operators_online_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function initiatePayment(operators_online_payment $operators_online_payment)
    {
        $payment_gateway = payment_gateway::findOrFail($operators_online_payment->payment_gateway_id);

        $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" . $payment_gateway->private_key . "\n-----END RSA PRIVATE KEY-----";

        $public_key = "-----BEGIN PUBLIC KEY-----\n" . $payment_gateway->public_key . "\n-----END PUBLIC KEY-----";

        $gateway_public_key = openssl_get_publickey($public_key);

        //constant parameters
        $datetime = date('YmdHis');
        $random = $this->generateRandomString(40);

        //initialize data
        $initialize_data = json_encode([
            'merchantId' => $payment_gateway->username,
            'datetime' => $datetime,
            'orderId' => $operators_online_payment->mer_txnid,
            'challenge' => $random,
        ]);

        //initialize signature
        openssl_sign($initialize_data, $sign, $private_key, OPENSSL_ALGO_SHA256);
        $signature = base64_encode($sign);

        //initialize sensitiveData
        openssl_public_encrypt($initialize_data, $crypted, $gateway_public_key);
        $sensitiveData = base64_encode($crypted);

        //initialize Request
        $response = Http::withHeaders([
            'X-KM-IP-V4' => $this->get_client_ip(),
            'X-KM-Client-Type' => 'MOBILE_WEB',
            'X-KM-Api-Version' => 'v-0.2.0',
            'Content-Type' => 'application/json',
        ])->post($this->nagad_host . "/check-out/initialize/" . $payment_gateway->username . "/" . $operators_online_payment->mer_txnid, [
            'dateTime' => $datetime,
            'sensitiveData' => $sensitiveData,
            'signature' => $signature,
        ]);

        //initialize Response
        $reply = json_decode($response, true);

        if (array_key_exists('sensitiveData', $reply)) {
            openssl_private_decrypt(base64_decode($reply['sensitiveData']), $plain_text, $private_key);
            $plain_response = json_decode($plain_text, true);

            //save paymentReferenceId
            $operators_online_payment->pgw_payment_identifier = $plain_response['paymentReferenceId'];
            $operators_online_payment->save();

            //check-out complete data
            $SensitiveDataOrder = json_encode([
                'merchantId' => $payment_gateway->username,
                'orderId' => $operators_online_payment->mer_txnid,
                'currencyCode' => '050',
                'amount' => $operators_online_payment->amount_paid,
                'challenge' => $plain_response['challenge']
            ]);

            //check-out complete signature
            openssl_sign($SensitiveDataOrder, $sign, $private_key, OPENSSL_ALGO_SHA256);
            $signature = base64_encode($sign);

            //check-out complete sensitiveData
            openssl_public_encrypt($SensitiveDataOrder, $crypted, $gateway_public_key);
            $sensitiveData = base64_encode($crypted);

            //check-out complete Request
            $response = Http::withHeaders([
                'X-KM-IP-V4' => $this->get_client_ip(),
                'X-KM-Client-Type' => 'MOBILE_WEB',
                'X-KM-Api-Version' => 'v-0.2.0',
                'Content-Type' => 'application/json',
            ])->post($this->nagad_host . "/check-out/complete/" . $plain_response['paymentReferenceId'], [
                'sensitiveData' => $sensitiveData,
                'signature' => $signature,
                'merchantCallbackURL' => route('nagad.operators_online_payment.callback'),
            ]);

            //check-out complete Response
            $callBack = json_decode($response, true);

            //Redirect to Nagad Website
            if (is_array($callBack)) {
                if (array_key_exists('callBackUrl', $callBack)) {
                    return redirect()->away($callBack['callBackUrl']);
                }
            } else {
                return $response;
            }
        }
        return $response;
    }

    /**
     * Process Success Customer Payment with Nagad Payment Gateway
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */

    public function paymentCallback(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
        ]);

        $operators_online_payment = operators_online_payment::where('mer_txnid', $request->order_id)->firstOrFail();

        Auth::loginUsingId($operators_online_payment->operator_id);

        $response = Http::retry(3, 100)->withHeaders([
            'X-KM-IP-V4' => $this->get_client_ip(),
            'X-KM-Client-Type' => 'MOBILE_WEB',
            'X-KM-Api-Version' => 'v-0.2.0',
            'Content-Type' => 'application/json',
        ])->get($this->nagad_host . "/verify/payment/" . $operators_online_payment->pgw_payment_identifier);

        $reply = json_decode($response, true);

        if ($reply['status'] === 'Success' && $operators_online_payment->pay_status == 'Pending') {
            // Successful
            $operators_online_payment->pay_status = 'Successful';
            $operators_online_payment->store_amount = $reply['amount'];
            $operators_online_payment->transaction_fee = $operators_online_payment->amount_paid - $reply['amount'];
            $operators_online_payment->save();
            return OperatorsOnlinePaymentController::store($operators_online_payment);
        } else {
            // Failed
            $operators_online_payment->pay_status = 'Failed';
            $operators_online_payment->store_amount = 0;
            $operators_online_payment->transaction_fee = 0;
            $operators_online_payment->save();

            switch ($operators_online_payment->payment_purpose) {
                case 'cash_in':
                    return redirect()->route('accounts.receivable')->with('error', 'Payment Failed');
                    break;
                case 'cash_out':
                    return redirect()->route('accounts.payable')->with('error', 'Payment Failed');
                    break;
            }
        }
    }
}
