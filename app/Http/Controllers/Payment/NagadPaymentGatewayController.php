<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomerPayment\CustomersPaymentProcessController;
use App\Http\Controllers\Sms\SmsBalanceHistoryController;
use App\Http\Controllers\SubscriptionPaymentController;
use App\Models\payment_gateway;
use App\Models\customer_payment;
use App\Models\sms_payment;
use App\Models\sms_bill;
use App\Models\subscription_payment;
use App\Models\subscription_bill;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NagadPaymentGatewayController extends Controller
{

    /**
     * Nagad Host
     *
     * @var string
     */
    protected $nagad_host = '';


    public function __construct()

    {
        if (config('local.is_sandbox_pgw')) {
            $this->nagad_host = 'http://sandbox.mynagad.com:10080/remote-payment-gateway-1.0/api/dfs';
        } else {
            $this->nagad_host = 'https://api.mynagad.com/api/dfs';
        }
    }


    /**
     * Generate Random string
     *
     * @param int $length
     * @return string
     */
    public static function generateRandomString($length = 40)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Get clinet ip
     *
     * @return string
     */
    public static function get_client_ip()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }



    /**
     * Initiate Customer Payment through Nagad Payment Gateway
     *
     * @param \App\Models\customer_payment $customer_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function initiateCustomerPayment(customer_payment $customer_payment)
    {

        //payment_gateway
        $payment_gateway = payment_gateway::findOrFail($customer_payment->payment_gateway_id);

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
            'orderId' => $customer_payment->mer_txnid,
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
        ])->post($this->nagad_host . "/check-out/initialize/" . $payment_gateway->username . "/" . $customer_payment->mer_txnid, [
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
            $customer_payment->pgw_payment_identifier = $plain_response['paymentReferenceId'];
            $customer_payment->save();

            //check-out complete data
            $SensitiveDataOrder = json_encode([
                'merchantId' => $payment_gateway->username,
                'orderId' => $customer_payment->mer_txnid,
                'currencyCode' => '050',
                'amount' => $customer_payment->amount_paid,
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
                'merchantCallbackURL' => route('nagad.customer_payment.callback'),
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

    public function customerPaymentCallback(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
        ]);

        $customer_payment = customer_payment::where('mer_txnid', $request->order_id)->firstOrFail();

        $response = Http::retry(3, 100)->withHeaders([
            'X-KM-IP-V4' => $this->get_client_ip(),
            'X-KM-Client-Type' => 'MOBILE_WEB',
            'X-KM-Api-Version' => 'v-0.2.0',
            'Content-Type' => 'application/json',
        ])->get($this->nagad_host . "/verify/payment/" . $customer_payment->pgw_payment_identifier);

        $reply = json_decode($response, true);

        if ($reply['status'] === 'Success' && $customer_payment->pay_status == 'Pending') {

            //update Customer Payment
            $customer_payment->pay_status = 'Successful';
            $customer_payment->store_amount = $reply['amount'];
            $customer_payment->transaction_fee = $customer_payment->amount_paid - $reply['amount'];
            $customer_payment->save();

            CustomersPaymentProcessController::store($customer_payment);

            //Show Profile
            return redirect()->route('customers.profile')->with('success', 'Package has been activated successfully');
        } else {
            //update Customer Payment
            $customer_payment->pay_status = 'Failed';
            $customer_payment->store_amount = 0;
            $customer_payment->transaction_fee = 0;
            $customer_payment->save();
            return redirect()->route('customers.profile')->with('error', 'Transaction Failed!');
        }
    }




    /**
     * Initiate Customer Payment through Nagad Payment Gateway
     *
     * @param \App\Models\sms_payment $sms_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function initiateSmsPayment(sms_payment $sms_payment)
    {

        //payment_gateway
        $payment_gateway = payment_gateway::findOrFail($sms_payment->payment_gateway_id);

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
            'orderId' => $sms_payment->mer_txnid,
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
        ])->post($this->nagad_host . "/check-out/initialize/" . $payment_gateway->username . "/" . $sms_payment->mer_txnid, [
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
            $sms_payment->pgw_payment_identifier = $plain_response['paymentReferenceId'];
            $sms_payment->save();

            //check-out complete data
            $SensitiveDataOrder = json_encode([
                'merchantId' => $payment_gateway->username,
                'orderId' => $sms_payment->mer_txnid,
                'currencyCode' => '050',
                'amount' => $sms_payment->amount_paid,
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
                'merchantCallbackURL' => route('nagad.sms_payment.callback'),
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

    public function smsPaymentCallback(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
        ]);

        $sms_payment = sms_payment::where('mer_txnid', $request->order_id)->firstOrFail();

        Auth::loginUsingId($sms_payment->operator_id);

        $response = Http::retry(3, 100)->withHeaders([
            'X-KM-IP-V4' => $this->get_client_ip(),
            'X-KM-Client-Type' => 'MOBILE_WEB',
            'X-KM-Api-Version' => 'v-0.2.0',
            'Content-Type' => 'application/json',
        ])->get($this->nagad_host . "/verify/payment/" . $sms_payment->pgw_payment_identifier);

        $reply = json_decode($response, true);

        if ($reply['status'] === 'Success' && $sms_payment->pay_status == 'Pending') {

            //update sms_payment
            $sms_payment->pay_status = 'Successful';
            $sms_payment->store_amount = $reply['amount'];
            $sms_payment->transaction_fee = $sms_payment->amount_paid - $reply['amount'];
            $sms_payment->save();
            //Add balance || Delete SMS Bill
            if ($sms_payment->pay_for == 'balance') {
                SmsBalanceHistoryController::store($sms_payment);
            } else {
                $sms_bill = sms_bill::find($sms_payment->sms_bill_id);
                $sms_bill->delete();
            }

            //Show SMS Payments
            return redirect()->route('sms_payments.index')->with('success', 'Payment Successful');
        } else {
            //update Customer Payment
            $sms_payment->pay_status = 'Failed';
            $sms_payment->store_amount = 0;
            $sms_payment->transaction_fee = 0;
            $sms_payment->save();
            return redirect()->route('sms_bills.index')->with('error', 'Payment Failed!');
        }
    }


    /**
     * Initiate Subscription Payment through Nagad Payment Gateway
     *
     * @param \App\Models\subscription_payment $subscription_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function initiateSubscriptionPayment(subscription_payment $subscription_payment)
    {

        //payment_gateway
        $payment_gateway = payment_gateway::findOrFail($subscription_payment->payment_gateway_id);

        $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" . $payment_gateway->private_key . "\n-----END RSA PRIVATE KEY-----";

        $public_key = "-----BEGIN PUBLIC KEY-----\n" . $payment_gateway->public_key  . "\n-----END PUBLIC KEY-----";

        $gateway_public_key = openssl_get_publickey($public_key);

        //constant parameters
        $datetime = date('YmdHis');
        $random = $this->generateRandomString(40);

        //initialize data
        $initialize_data = json_encode([
            'merchantId' => $payment_gateway->username,
            'datetime' => $datetime,
            'orderId' => $subscription_payment->mer_txnid,
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
        ])->post($this->nagad_host . "/check-out/initialize/" . $payment_gateway->username . "/" . $subscription_payment->mer_txnid, [
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
            $subscription_payment->pgw_payment_identifier = $plain_response['paymentReferenceId'];
            $subscription_payment->save();

            //check-out complete data
            $SensitiveDataOrder = json_encode([
                'merchantId' => $payment_gateway->username,
                'orderId' => $subscription_payment->mer_txnid,
                'currencyCode' => '050',
                'amount' => $subscription_payment->amount_paid,
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
                'merchantCallbackURL' => route('nagad.subscription_payment.callback'),
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

    public function subscriptionPaymentCallback(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
        ]);

        $subscription_payment = subscription_payment::where('mer_txnid', $request->order_id)->firstOrFail();

        Auth::loginUsingId($subscription_payment->mgid);

        $response = Http::retry(3, 100)->withHeaders([
            'X-KM-IP-V4' => $this->get_client_ip(),
            'X-KM-Client-Type' => 'MOBILE_WEB',
            'X-KM-Api-Version' => 'v-0.2.0',
            'Content-Type' => 'application/json',
        ])->get($this->nagad_host . "/verify/payment/" . $subscription_payment->pgw_payment_identifier);

        $reply = json_decode($response, true);

        if ($reply['status'] === 'Success' && $subscription_payment->pay_status == 'Pending') {

            //update subscription_payment
            $subscription_payment->pay_status = 'Successful';
            $subscription_payment->store_amount = $reply['amount'];
            $subscription_payment->transaction_fee = $subscription_payment->amount_paid - $reply['amount'];
            $subscription_payment->save();

            //Delete  Bill
            $subscription_bill = subscription_bill::find($subscription_payment->subscription_bill_id);
            $subscription_bill->delete();

            //record incomes
            SubscriptionPaymentController::recordIncomes($subscription_payment);

            //Show Payments
            return redirect()->route('subscription_payments.index')->with('success', 'Payment Successful');
        } else {
            //update  Payment
            $subscription_payment->pay_status = 'Failed';
            $subscription_payment->store_amount = 0;
            $subscription_payment->transaction_fee = 0;
            $subscription_payment->save();
            return redirect()->route('subscription_bills.index')->with('error', 'Payment Failed!');
        }
    }




    /**
     * Recheck Customer Payment
     *
     * @param \App\Models\customer_payment $customer_payment
     * @return \Illuminate\Http\Response
     */
    public function recheckCustomerPayment(customer_payment $customer_payment)
    {
        //Payment Initiate was Failed
        if (strlen($customer_payment->pgw_payment_identifier) == 0) {
            $customer_payment->delete();
            return 0;
        }

        try {
            $response = Http::retry(3, 100)->withHeaders([
                'X-KM-IP-V4' => $this->get_client_ip(),
                'X-KM-Client-Type' => 'MOBILE_WEB',
                'X-KM-Api-Version' => 'v-0.2.0',
                'Content-Type' => 'application/json',
            ])->get($this->nagad_host . "/verify/payment/" . $customer_payment->pgw_payment_identifier);
        } catch (\Throwable $th) {
            $customer_payment->delete();
            return 0;
        }

        $reply = json_decode($response, true);

        if ($reply['status'] === 'Success' && $customer_payment->pay_status !== 'Successful') {

            //update Customer Payment
            $customer_payment->pay_status = 'Successful';
            $customer_payment->store_amount = $reply['amount'];
            $customer_payment->transaction_fee = $customer_payment->amount_paid - $reply['amount'];
            $customer_payment->save();

            CustomersPaymentProcessController::store($customer_payment);

            return 1;
        } else {
            $customer_payment->delete();
            return 0;
        }
    }




    /**
     * Recheck SMS Payment
     *
     * @param \App\Models\sms_payment $sms_payment
     * @return \Illuminate\Http\Response
     */
    public function recheckSmsPayment(sms_payment $sms_payment)
    {
        //Payment Initiate was Failed
        if (strlen($sms_payment->pgw_payment_identifier) == 0) {
            $sms_payment->delete();
            return 0;
        }

        $response = Http::retry(3, 100)->withHeaders([
            'X-KM-IP-V4' => $this->get_client_ip(),
            'X-KM-Client-Type' => 'MOBILE_WEB',
            'X-KM-Api-Version' => 'v-0.2.0',
            'Content-Type' => 'application/json',
        ])->get($this->nagad_host . "/verify/payment/" . $sms_payment->pgw_payment_identifier);

        $reply = json_decode($response, true);

        if ($reply['status'] === 'Success' && $sms_payment->pay_status !== 'Successful') {

            //update sms_payment
            $sms_payment->pay_status = 'Successful';
            $sms_payment->store_amount = $reply['amount'];
            $sms_payment->transaction_fee = $sms_payment->amount_paid - $reply['amount'];
            $sms_payment->save();

            //Add balance || Delete SMS Bill
            if ($sms_payment->pay_for == 'balance') {
                SmsBalanceHistoryController::store($sms_payment);
            } else {
                $sms_bill = sms_bill::find($sms_payment->sms_bill_id);
                $sms_bill->delete();
            }
            return 1;
        } else {
            $sms_payment->delete();
            return 0;
        }
    }



    /**
     * Recheck Subscription Payment
     *
     * @param \App\Models\subscription_payment $subscription_payment
     * @return \Illuminate\Http\Response
     */
    public function recheckSubscriptionPayment(subscription_payment $subscription_payment)
    {

        //Payment Initiate was Failed
        if (strlen($subscription_payment->pgw_payment_identifier) == 0) {
            $subscription_payment->delete();
            return 0;
        }


        $response = Http::retry(3, 100)->withHeaders([
            'X-KM-IP-V4' => $this->get_client_ip(),
            'X-KM-Client-Type' => 'MOBILE_WEB',
            'X-KM-Api-Version' => 'v-0.2.0',
            'Content-Type' => 'application/json',
        ])->get($this->nagad_host . "/verify/payment/" . $subscription_payment->pgw_payment_identifier);

        $reply = json_decode($response, true);

        if ($reply['status'] === 'Success' && $subscription_payment->pay_status !== 'Successful') {

            //update subscription_payment
            $subscription_payment->pay_status = 'Successful';
            $subscription_payment->store_amount = $reply['amount'];
            $subscription_payment->transaction_fee = $subscription_payment->amount_paid - $reply['amount'];
            $subscription_payment->save();
            //Delete  Bill
            $subscription_bill = subscription_bill::find($subscription_payment->subscription_bill_id);
            $subscription_bill->delete();
            return 1;
        } else {
            $subscription_payment->delete();
            return 0;
        }
    }
}
