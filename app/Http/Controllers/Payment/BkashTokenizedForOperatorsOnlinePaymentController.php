<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\OperatorsOnlinePaymentController;
use App\Models\operators_online_payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class BkashTokenizedForOperatorsOnlinePaymentController extends bKashTokenizedAbstractController
{

    /**
     * Initiate Payment
     *
     * @param \App\Models\operators_online_payment $operators_online_payment
     * @return \Illuminate\Http\Response
     */
    public function createPayment(operators_online_payment $operators_online_payment)
    {
        if ($operators_online_payment->pay_status !== 'Pending') {
            abort(500, 'Invalid Request!');
        }

        $payment_gateway = $this->getPaymentGateway($operators_online_payment->payment_gateway_id);
        $this->grantToken($payment_gateway);

        //Reset mer_txnid
        $mer_txnid = random_int(1000, 9999) . Carbon::now(config('app.timezone'))->timestamp;
        $operators_online_payment->mer_txnid = $mer_txnid;
        $operators_online_payment->save();

        // payment_body
        $payment_body = [
            'mode' => '0011',
            'payerReference' => $operators_online_payment->operator_id,
            'callbackURL' => route('bkash_tokenized_checkout.operators_online_payment.callback'),
            'amount' => $operators_online_payment->amount_paid,
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => $operators_online_payment->mer_txnid,
        ];

        $credentials_file = $this->getCredentialsFileName($payment_gateway);
        $credentials = json_decode(file_get_contents($credentials_file), true);

        $response =  Http::timeout(30)->withHeaders([
            'content-type' => 'application/json',
            'accept' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
        ])->post($this->create_payment_url, $payment_body);

        if (config('consumer.debug_payment')) {
            Storage::put('bkash_tokenized_debug/createPaymentBody.json', json_encode($payment_body));
            Storage::put('bkash_tokenized_debug/createPayment.json', $response);
        }

        $data = json_decode($response, true);

        // Error
        if (array_key_exists('errorMessage', $data)) {
            $operators_online_payment->pay_status = 'Failed';
            $operators_online_payment->save();
            return match ($operators_online_payment->payment_purpose) {
                'cash_in' => redirect()->route('accounts.receivable')->with('error', $data['errorMessage']),
                'cash_out' => redirect()->route('accounts.payable')->with('error', $data['errorMessage']),
            };
        }
        if (array_key_exists('statusMessage', $data)) {
            if ($data['statusMessage'] !== 'Successful') {
                $operators_online_payment->pay_status = 'Failed';
                $operators_online_payment->save();
                return match ($operators_online_payment->payment_purpose) {
                    'cash_in' => redirect()->route('accounts.receivable')->with('error', $data['statusMessage']),
                    'cash_out' => redirect()->route('accounts.payable')->with('error', $data['statusMessage']),
                };
            }
        }

        $operators_online_payment->pgw_payment_identifier = $data['paymentID'];
        $operators_online_payment->save();

        return redirect()->away($data['bkashURL']);
    }

    /**
     * Process Payment Callback
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function paymentCallback(Request $request)
    {
        $callback = $request->all();

        if (config('consumer.debug_payment')) {
            Storage::put('bkash_tokenized_debug/callbackPayment.json', json_encode($callback));
        }

        $operators_online_payment = operators_online_payment::where('pgw_payment_identifier', $callback['paymentID'])->firstOrFail();

        // Error
        if (array_key_exists('errorMessage', $callback)) {
            $operators_online_payment->pay_status = 'Failed';
            $operators_online_payment->save();
            return match ($operators_online_payment->payment_purpose) {
                'cash_in' => redirect()->route('accounts.receivable')->with('error', $callback['errorMessage']),
                'cash_out' => redirect()->route('accounts.payable')->with('error', $callback['errorMessage']),
            };
        }

        switch ($callback['status']) {
            case 'success':
                return $this->executePayment($operators_online_payment);
                break;

            case 'cancel':
                $operators_online_payment->pay_status = 'Failed';
                $operators_online_payment->save();
                return match ($operators_online_payment->payment_purpose) {
                    'cash_in' => redirect()->route('accounts.receivable')->with('error', 'Payment Cancelled'),
                    'cash_out' => redirect()->route('accounts.payable')->with('error', 'Payment Cancelled'),
                };
                break;

            default:
                // failure
                $operators_online_payment->pay_status = 'Failed';
                $operators_online_payment->save();
                return match ($operators_online_payment->payment_purpose) {
                    'cash_in' => redirect()->route('accounts.receivable')->with('error', 'Payment Failed'),
                    'cash_out' => redirect()->route('accounts.payable')->with('error', 'Payment Failed'),
                };
                break;
        }
    }

    /**
     * Execute Bkash Tokenized Checkout Payment
     *
     * @param \App\Models\operators_online_payment $operators_online_payment
     * @return \Illuminate\Http\Response
     */
    public function executePayment(operators_online_payment $operators_online_payment)
    {

        $payment_gateway = $this->getPaymentGateway($operators_online_payment->payment_gateway_id);
        $credentials_file = $this->getCredentialsFileName($payment_gateway);
        $credentials = json_decode(file_get_contents($credentials_file), true);

        $response =  Http::timeout(30)->withHeaders([
            'accept' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
        ])->post($this->execute_payment_url, [
            'paymentID' => $operators_online_payment->pgw_payment_identifier,
        ]);

        if (config('consumer.debug_payment')) {
            Storage::put('bkash_tokenized_debug/executePayment.json', $response);
        }

        // << If No Response From execute Query
        if ($response->failed()) {
            $response = $this->queryPayment($operators_online_payment);
            $data = json_decode($response, true);
            if (array_key_exists('transactionStatus', $data)) {
                if ($data['transactionStatus'] == 'Completed') {
                    $transaction_fee = $operators_online_payment->amount_paid * ($payment_gateway->service_charge_percentage / 100);
                    $operators_online_payment->pgw_txnid = $data['trxID'];
                    $operators_online_payment->bank_txnid = $data['trxID'];
                    $operators_online_payment->pay_status = 'Successful';
                    $operators_online_payment->store_amount = $operators_online_payment->amount_paid - $transaction_fee;
                    $operators_online_payment->transaction_fee = $transaction_fee;
                    $operators_online_payment->save();
                    return OperatorsOnlinePaymentController::store($operators_online_payment);
                } else {
                    $operators_online_payment->pay_status = 'Failed';
                    $operators_online_payment->save();
                    return match ($operators_online_payment->payment_purpose) {
                        'cash_in' => redirect()->route('accounts.receivable')->with('error', $data['transactionStatus']),
                        'cash_out' => redirect()->route('accounts.payable')->with('error', $data['transactionStatus']),
                    };
                }
            } else {
                $operators_online_payment->pay_status = 'Failed';
                $operators_online_payment->save();
                return match ($operators_online_payment->payment_purpose) {
                    'cash_in' => redirect()->route('accounts.receivable')->with('error', 'Query Payment Failed'),
                    'cash_out' => redirect()->route('accounts.payable')->with('error', 'Query Payment Failed'),
                };
            }
        }
        // >>

        $data = json_decode($response, true);

        // Error
        if (array_key_exists('errorMessage', $data)) {
            $operators_online_payment->pay_status = 'Failed';
            $operators_online_payment->save();
            return match ($operators_online_payment->payment_purpose) {
                'cash_in' => redirect()->route('accounts.receivable')->with('error', $data['errorMessage']),
                'cash_out' => redirect()->route('accounts.payable')->with('error', $data['errorMessage']),
            };
        }

        if (array_key_exists('statusMessage', $data)) {
            if ($data['statusMessage'] == 'Successful') {
                $transaction_fee = $operators_online_payment->amount_paid * ($payment_gateway->service_charge_percentage / 100);
                $operators_online_payment->pgw_txnid = $data['trxID'];
                $operators_online_payment->bank_txnid = $data['trxID'];
                $operators_online_payment->pay_status = 'Successful';
                $operators_online_payment->store_amount = $operators_online_payment->amount_paid - $transaction_fee;
                $operators_online_payment->transaction_fee = $transaction_fee;
                $operators_online_payment->save();
                return OperatorsOnlinePaymentController::store($operators_online_payment);
            }
        }

        // otherwise Failed
        $operators_online_payment->pay_status = 'Failed';
        $operators_online_payment->save();
        return match ($operators_online_payment->payment_purpose) {
            'cash_in' => redirect()->route('accounts.receivable')->with('error', 'Failed'),
            'cash_out' => redirect()->route('accounts.payable')->with('error', 'Failed'),
        };
    }

    /**
     * Query Bkash Tokenized Checkout Payment
     *
     * @param \App\Models\operators_online_payment $operators_online_payment
     * @return \Illuminate\Http\Response
     */
    public function queryPayment(operators_online_payment $operators_online_payment)
    {
        $payment_gateway = $this->getPaymentGateway($operators_online_payment->payment_gateway_id);
        $this->grantToken($payment_gateway);
        $credentials_file = $this->getCredentialsFileName($payment_gateway);
        $credentials = json_decode(file_get_contents($credentials_file), true);

        $header = [
            'content-type' => 'application/json',
            'accept' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
        ];
        $body = [
            'paymentID' => $operators_online_payment->pgw_payment_identifier,
        ];
        $response = Http::timeout(30)->withHeaders($header)->post($this->query_payment_url, $body);

        if (config('consumer.debug_payment')) {
            Storage::put('bkash_tokenized_debug/queryPayment.json', $response);
        }

        return $response;
    }
}
