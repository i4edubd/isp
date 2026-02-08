<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Sms\SmsBalanceHistoryController;
use App\Models\operator;
use App\Models\sms_bill;
use App\Models\sms_payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ShurjoPaySmsPaymentController extends ShurjoPayAbstractController
{
    /**
     * Create ShurjoPay Payment
     *
     * @param \App\Models\sms_payment $sms_payment
     *
     * @return \Illuminate\Http\Response
     */
    public function createPayment(sms_payment $sms_payment)
    {
        $payment_gateway = $this->getPaymentGateway($sms_payment->payment_gateway_id);

        $operator = operator::find($sms_payment->operator_id);

        $mobile = validate_mobile($operator->mobile);

        if (!$mobile) {
            $mobile = '01751000000';
        }

        $this->getToken($payment_gateway);

        $storage_tokens = Storage::path($this->getTokenStoragePath($payment_gateway));

        if (file_exists($storage_tokens)) {
            $tokens = json_decode(file_get_contents($storage_tokens), true);
        } else {
            abort(500, 'Token Not Found');
        }

        $request_body = [
            'token' => $tokens['token'],
            'store_id' => $tokens['store_id'],
            'prefix' => $payment_gateway->msisdn,
            'currency' => 'BDT',
            'return_url' => route('shurjopay.sms_payment.callback'),
            'cancel_url' => route('shurjopay.sms_payment.callback'),
            'amount' => $sms_payment->amount_paid,
            'order_id' =>  $payment_gateway->msisdn . $sms_payment->id,
            'customer_name' => $operator->company,
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
            $sms_payment->pgw_payment_identifier = $response_body['sp_order_id'];
            $sms_payment->save();
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
        $sms_payment = sms_payment::where('pgw_payment_identifier', $order_id)->firstOrFail();
        $response = $this->verifyPayment($sms_payment);
        if ($response === 1) {
            return redirect()->route('sms_payments.index')->with('success', 'Payment Successful');
        } else {
            return redirect()->route('sms_payments.index')->with('error', 'Payment Failed');
        }
    }

    /**
     * Verify ShurjoPay Payment for Customer
     *
     * @param \App\Models\sms_payment $sms_payment
     *
     * @return \Illuminate\Http\Response
     */
    public function verifyPayment(sms_payment $sms_payment)
    {

        if ($sms_payment->pay_status === 'Successful') {
            return 0;
        }

        if (!strlen($sms_payment->pgw_payment_identifier)) {
            $sms_payment->delete();
            return 0;
        }

        $payment_gateway = $this->getPaymentGateway($sms_payment->payment_gateway_id);
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
                'order_id' => $sms_payment->pgw_payment_identifier,
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
                    $sms_payment->bank_txnid = $response_body['bank_trx_id'];
                }
                $sms_payment->pay_status = 'Successful';
                $sms_payment->store_amount = $response_body['payable_amount'];
                $sms_payment->transaction_fee = $sms_payment->amount_paid - $sms_payment['store_amount'];
                $sms_payment->save();
                if ($sms_payment->pay_for == 'balance') {
                    SmsBalanceHistoryController::store($sms_payment);
                } else {
                    $sms_bill = sms_bill::find($sms_payment->sms_bill_id);
                    $sms_bill->delete();
                }
                return 1;
            }
        }

        $sms_payment->delete();
        return 0;
    }
}
