<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\CustomerPayment\CustomersPaymentProcessController;
use App\Models\customer_payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ShurjoPayCustomerPaymentController extends ShurjoPayAbstractController
{

    /**
     * Create ShurjoPay Payment for Customer
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\customer_payment $customer_payment
     *
     * @return \Illuminate\Http\Response
     */
    public function createPayment(Request $request, customer_payment $customer_payment)
    {
        $payment_gateway = $this->getPaymentGateway($customer_payment->payment_gateway_id);

        $this->getToken($payment_gateway);

        $storage_tokens = Storage::path($this->getTokenStoragePath($payment_gateway));

        if (file_exists($storage_tokens)) {
            $tokens = json_decode(file_get_contents($storage_tokens), true);
        } else {
            abort(500, 'Token Not Found');
        }

        $mobile = validate_mobile($customer_payment->mobile);

        if (!$mobile) {
            $mobile = '01751000000';
        }

        $request_body = [
            'token' => $tokens['token'],
            'store_id' => $tokens['store_id'],
            'prefix' => $payment_gateway->msisdn,
            'currency' => 'BDT',
            'return_url' => route('ShurjoPay.customer_payment.callback'),
            'cancel_url' => route('ShurjoPay.customer_payment.callback'),
            'amount' => $customer_payment->amount_paid,
            'order_id' =>  $payment_gateway->msisdn . $customer_payment->id,
            'customer_name' => $customer_payment->username,
            'customer_phone' => $mobile,
            'customer_address' => 'Bangladesh',
            'customer_city' => 'Dhaka',
            'client_ip' => $this->get_client_ip(),
        ];

        $response = Http::acceptJson()
            ->withHeaders(['content-type' => 'application/json'])
            ->withToken($tokens['token'])
            ->post($this->secret_pay_url, $request_body);

        if (config('consumer.debug_payment')) {
            Storage::put('ShurjoPay_debug/createPaymentRequestBody.json', json_encode($request_body));
            Storage::put('ShurjoPay_debug/createPaymentResponse.json', $response);
        }

        $response_body = json_decode($response, true);

        if (is_array($response_body) == false) {
            abort('500', 'Gateway Error');
        }

        if (array_key_exists('checkout_url', $response_body)) {
            $customer_payment->pgw_payment_identifier = $response_body['sp_order_id'];
            $customer_payment->save();
            return redirect()->away($response_body['checkout_url']);
        } else {
            abort('500', 'Checkout URL Not Found!');
        }
    }

    /**
     * callbackURL for ShurjoPay Payment for Customer
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function successCallback(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
        ]);
        $order_id = $request->order_id;
        $customer_payment = customer_payment::where('pgw_payment_identifier', $order_id)->firstOrFail();
        $response = $this->verifyPayment($customer_payment);
        if ($response === 1) {
            return redirect()->route('customers.profile')->with('success', 'Package has been activated successfully');
        } else {
            return redirect()->route('customers.profile')->with('error', 'Payment Failed');
        }
    }

    /**
     * Verify ShurjoPay Payment for Customer
     *
     * @param \App\Models\customer_payment $customer_payment
     *
     * @return \Illuminate\Http\Response
     */
    public function verifyPayment(customer_payment $customer_payment)
    {

        if ($customer_payment->pay_status === 'Successful') {
            return 0;
        }

        if (!strlen($customer_payment->pgw_payment_identifier)) {
            $customer_payment->delete();
            return 0;
        }

        $payment_gateway = $this->getPaymentGateway($customer_payment->payment_gateway_id);
        $this->getToken($payment_gateway);
        $storage_tokens = Storage::path($this->getTokenStoragePath($payment_gateway));
        if (file_exists($storage_tokens)) {
            $tokens = json_decode(file_get_contents($storage_tokens), true);
        } else {
            return 0;
        }

        $response = Http::acceptJson()
            ->withHeaders(['content-type' => 'application/json'])
            ->withToken($tokens['token'])
            ->post($this->verification_url, [
                'token' => $tokens['token'],
                'order_id' => $customer_payment->pgw_payment_identifier,
            ]);

        if (config('consumer.debug_payment')) {
            Storage::put('ShurjoPay_debug/verifyPayment.json', $response);
        }

        $response_body = json_decode($response, true);

        if (is_array($response_body) == false) {
            return 0;
        }

        $response_body = array_shift($response_body);

        if (array_key_exists('sp_code', $response_body)) {
            if ($response_body['sp_code'] == 1000) {
                if (array_key_exists('bank_trx_id', $response_body)) {
                    $customer_payment->bank_txnid = $response_body['bank_trx_id'];
                }
                $customer_payment->pay_status = 'Successful';
                $customer_payment->store_amount = $response_body['payable_amount'];
                $customer_payment->transaction_fee = $customer_payment->amount_paid - $customer_payment['store_amount'];
                $customer_payment->save();
                CustomersPaymentProcessController::store($customer_payment);
                return 1;
            }
        }

        $customer_payment->delete();
        return 0;
    }
}
