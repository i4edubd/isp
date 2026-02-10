<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomerPayment\CustomersPaymentProcessController;
use App\Models\customer_payment;
use App\Models\payment_gateway;
use Illuminate\Http\Request;

class BdsmartpayController extends Controller
{
    /**
     * Initiate Customer Payment
     *
     * @param \App\Models\customer_payment $customer_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function initiateCustomerPayment(customer_payment $customer_payment)
    {
        //payment_gateway
        $payment_gateway = payment_gateway::findOrFail($customer_payment->payment_gateway_id);

        $email = config('mail.mailers.smtp.username');
        if (strlen($email)) {
            $cus_email = $email;
        } else {
            $cus_email = 'root@mydomain.com';
        }

        $request_data = http_build_query(array(
            'gateway' => 'bdsmartpay',
            'service_name' => $payment_gateway->operator->company,
            'amount' => $customer_payment->amount_paid,
            'order_id' => $customer_payment->id,
            'merchant_id' => $payment_gateway->username,
            'mobile' => '01751000000', /*Fixed*/
            'email' => $cus_email,
            'user_ref' => $customer_payment->customer_id,
            'redirect_url' => route('bdsmartpay.customer_payment.callback') . '?',
        ));

        $secret = $payment_gateway->password;
        $cipher_algo = "aes-128-cbc";
        $ivlen = openssl_cipher_iv_length($cipher_algo);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $data = openssl_encrypt($request_data, $cipher_algo, $secret, 0, $iv);
        $url = 'https://bdsmartpay.com/gateway/pro/' . htmlentities(base64_encode($data));
        return redirect()->away($url);
    }

    /**
     * Process Success Customer Payment
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */

    public function customerPaymentCallback(Request $request)
    {
        $response = $request->all();

        if (count($response) == 0) {
            return 'Transaction Failed!';
        }

        $response_data = array_key_first($response);
        if (substr($response_data, 0, 1) == '?') {
            $response_data = substr($response_data, 1);
        }
        if (substr($response_data, 0, 1) == '/') {
            $response_data = substr($response_data, 1);
        }
        $cipher_algo = "aes-128-cbc";
        $secret = "25c6c7ff35b9979b151f2136cd13b0ee";
        $data = openssl_decrypt(base64_decode($response_data), $cipher_algo, $secret);

        if (!strlen($data)) {
            return 'Transaction Failed!';
        }

        parse_str($data, $array_data);

        if (is_array($array_data) == false) {
            return 'Transaction Failed!';
        }

        if (count($array_data) == 0) {
            return 'Transaction Failed!';
        }

        if (array_key_exists('merchant_order_id', $array_data) == false) {
            return 'Transaction Failed!';
        }

        $customer_payment = customer_payment::findOrFail($array_data['merchant_order_id']);

        /*
            $payment_gateway = payment_gateway::findOrFail($customer_payment->payment_gateway_id);
            $request_data = http_build_query(array(
                'service_name' => $payment_gateway->operator->company,
                'order_id' => $array_data['merchant_order_id'],
                'merchant_id' => $payment_gateway->username,
            ));
            $cipher_algo = "aes-128-cbc";
            $secret = "25c6c7ff35b9979b151f2136cd13b0ee";
            $ivlen = openssl_cipher_iv_length($cipher_algo);
            $iv = openssl_random_pseudo_bytes($ivlen);
            $data = openssl_encrypt($request_data, $cipher_algo, $secret, 0, $iv);
            $response_data = json_decode(trim(file_get_contents('https://bdsmartpay.com/gateway/query_payment/'.base64_encode($data)),'[]'),true);
        */

        if ($array_data['transaction_status'] == 'Successful') {
            $transaction_fee = $customer_payment->amount_paid * (2.25 / 100);
            $customer_payment->pay_status = 'Successful';
            $customer_payment->store_amount = $customer_payment->amount_paid - $transaction_fee;
            $customer_payment->transaction_fee = $transaction_fee;
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
     * Recheck Customer Payment
     *
     * @param \App\Models\customer_payment $customer_payment
     * @return \Illuminate\Http\Response
     */
    public function recheckCustomerPayment(customer_payment $customer_payment)
    {
        $payment_gateway = payment_gateway::findOrFail($customer_payment->payment_gateway_id);

        $request_data = http_build_query(array(
            'service_name' => $payment_gateway->operator->company,
            'order_id' => $customer_payment->id,
            'merchant_id' => $payment_gateway->username,
        ));
        $cipher_algo = "aes-128-cbc";
        $secret = "25c6c7ff35b9979b151f2136cd13b0ee";
        $ivlen = openssl_cipher_iv_length($cipher_algo);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $data = openssl_encrypt($request_data, $cipher_algo, $secret, 0, $iv);
        $response_data = json_decode(trim(file_get_contents('https://bdsmartpay.com/gateway/query_payment/' . base64_encode($data)), '[]'), true);

        if (is_array($response_data) == false) {
            return 0;
        }

        if (array_key_exists('transaction_status', $response_data) == false) {
            return 0;
        }

        if ($response_data['transaction_status'] == 'Successful') {
            $transaction_fee = $customer_payment->amount_paid * (2.25 / 100);
            $customer_payment->pay_status = 'Successful';
            $customer_payment->store_amount = $customer_payment->amount_paid - $transaction_fee;
            $customer_payment->transaction_fee = $transaction_fee;
            $customer_payment->save();
            CustomersPaymentProcessController::store($customer_payment);
            return 1;
        } else {
            $customer_payment->delete();
            return 0;
        }
    }
}
