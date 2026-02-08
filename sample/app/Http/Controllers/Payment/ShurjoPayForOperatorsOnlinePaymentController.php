<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\OperatorsOnlinePaymentController;
use App\Models\operator;
use App\Models\operators_online_payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ShurjoPayForOperatorsOnlinePaymentController extends ShurjoPayAbstractController
{
    /**
     * Create ShurjoPay Payment
     *
     * @param \App\Models\operators_online_payment $operators_online_payment
     *
     * @return \Illuminate\Http\Response
     */
    public function createPayment(operators_online_payment $operators_online_payment)
    {
        $payment_gateway = $this->getPaymentGateway($operators_online_payment->payment_gateway_id);

        $this->getToken($payment_gateway);

        $storage_tokens = Storage::path($this->getTokenStoragePath($payment_gateway));

        if (file_exists($storage_tokens)) {
            $tokens = json_decode(file_get_contents($storage_tokens), true);
        } else {
            abort(500, 'Token Not Found');
        }

        $operator = operator::find($operators_online_payment->operator_id);

        $mobile = validate_mobile($operator->mobile);

        if (!$mobile) {
            $mobile = '01751000000';
        }

        $request_body = [
            'token' => $tokens['token'],
            'store_id' => $tokens['store_id'],
            'prefix' => $payment_gateway->msisdn,
            'currency' => 'BDT',
            'return_url' => route('ShurjoPay.operators_online_payment.callback'),
            'cancel_url' => route('ShurjoPay.operators_online_payment.callback'),
            'amount' => $operators_online_payment->amount_paid,
            'order_id' => $payment_gateway->msisdn . $operators_online_payment->id,
            'customer_name' => $operators_online_payment->payment_purpose,
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
            $operators_online_payment->pgw_payment_identifier = $response_body['sp_order_id'];
            $operators_online_payment->save();
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
        $operators_online_payment = operators_online_payment::where('pgw_payment_identifier', $order_id)->firstOrFail();

        $response = $this->verifyPayment($operators_online_payment);
        if (is_bool($response)) {
            if ($operators_online_payment->payment_purpose == 'cash_in') {
                return redirect()->route('accounts.receivable')->with('error', 'Payment Failed');
            }
            if ($operators_online_payment->payment_purpose == 'cash_out') {
                return redirect()->route('accounts.payable')->with('error', 'Payment Failed');
            }
        } else {
            return $response;
        }
    }

    /**
     * Verify ShurjoPay Payment for Customer
     *
     * @param \App\Models\operators_online_payment $operators_online_payment
     *
     * @return \Illuminate\Http\Response
     */
    public function verifyPayment(operators_online_payment $operators_online_payment)
    {
        if ($operators_online_payment->pay_status === 'Successful') {
            return false;
        }

        if (!strlen($operators_online_payment->pgw_payment_identifier)) {
            $operators_online_payment->delete();
            return false;
        }

        $payment_gateway = $this->getPaymentGateway($operators_online_payment->payment_gateway_id);
        $this->getToken($payment_gateway);
        $storage_tokens = Storage::path($this->getTokenStoragePath($payment_gateway));
        if (file_exists($storage_tokens)) {
            $tokens = json_decode(file_get_contents($storage_tokens), true);
        } else {
            return false;
        }

        $response = Http::acceptJson()
            ->withHeaders(['content-type' => 'application/json'])
            ->withToken($tokens['token'])
            ->post($this->verification_url, [
                'token' => $tokens['token'],
                'order_id' => $operators_online_payment->pgw_payment_identifier,
            ]);

        if (config('consumer.debug_payment')) {
            Storage::put('ShurjoPay_debug/verifyPayment.json', $response);
        }

        $response_body = json_decode($response, true);

        if (is_array($response_body) == false) {
            return false;
        }

        $response_body = array_shift($response_body);

        if (array_key_exists('sp_code', $response_body)) {
            // success
            if ($response_body['sp_code'] == 1000) {
                if (array_key_exists('bank_trx_id', $response_body)) {
                    $operators_online_payment->bank_txnid = $response_body['bank_trx_id'];
                }
                $operators_online_payment->pay_status = 'Successful';
                $operators_online_payment->store_amount = $response_body['payable_amount'];
                $operators_online_payment->transaction_fee = $operators_online_payment->amount_paid - $operators_online_payment['store_amount'];
                $operators_online_payment->save();
                return OperatorsOnlinePaymentController::store($operators_online_payment);
            }
        }

        // Failed
        $operators_online_payment->delete();
        return false;
    }
}
