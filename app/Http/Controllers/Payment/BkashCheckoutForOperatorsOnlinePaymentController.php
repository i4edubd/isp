<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\OperatorsOnlinePaymentController;
use App\Models\operators_online_payment;
use App\Models\payment_gateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class BkashCheckoutForOperatorsOnlinePaymentController extends BkashCheckoutBaseController
{

    /**
     * Initiate Bkash Checkout Payment
     *
     * @param \App\Models\operators_online_payment $operators_online_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function initiatePayment(operators_online_payment $operators_online_payment)
    {
        if ($operators_online_payment->pay_status !== 'Pending') {
            abort(500, 'Invalid Request!');
        }

        //get token
        $payment_gateway = payment_gateway::findOrFail($operators_online_payment->payment_gateway_id);
        $this->grantToken($payment_gateway);

        return view('admins.components.bkash-operators_online_payment', [
            'operators_online_payment' => $operators_online_payment,
            'bkash_script_url' => $this->bkash_script_url,
        ]);
    }

    /**
     * Create Bkash Checkout Payment
     *
     * @param \App\Models\operators_online_payment $operators_online_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function createPayment(operators_online_payment $operators_online_payment)
    {
        $payment_gateway = payment_gateway::find($operators_online_payment->payment_gateway_id);

        $credentials_file = $this->getCredentialsFileName($payment_gateway);

        $credentials = json_decode(file_get_contents($credentials_file), true);

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
            'content-type' => 'application/json',
        ])->post($this->create_payment_url, [
            'amount' => $operators_online_payment->amount_paid,
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => $operators_online_payment->mer_txnid,
        ]);

        $data = json_decode($response, true);
        if (array_key_exists('paymentID', $data)) {
            $operators_online_payment->pgw_payment_identifier = $data['paymentID'];
            $operators_online_payment->save();
        }

        echo $response;
    }

    /**
     * Execute Bkash Checkout Payment
     *
     * @param \App\Models\operators_online_payment $operators_online_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function executePayment(operators_online_payment $operators_online_payment)
    {
        $payment_gateway = payment_gateway::find($operators_online_payment->payment_gateway_id);

        $credentials_file = $this->getCredentialsFileName($payment_gateway);

        $credentials = json_decode(file_get_contents($credentials_file), true);

        $response =  Http::withHeaders([
            'accept' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
        ])->post($this->execute_payment_url . $operators_online_payment->pgw_payment_identifier);

        //update payment
        $data = json_decode($response, true);
        if (array_key_exists('paymentID', $data)) {
            $transaction_fee = $operators_online_payment->amount_paid * ($payment_gateway->service_charge_percentage / 100);
            $operators_online_payment->pgw_txnid = $data['trxID'];
            $operators_online_payment->bank_txnid = $data['trxID'];
            $operators_online_payment->pay_status = 'Successful';
            $operators_online_payment->store_amount = $operators_online_payment->amount_paid - $transaction_fee;
            $operators_online_payment->transaction_fee = $transaction_fee;
            $operators_online_payment->save();
        }

        echo $response;
    }

    /**
     * Query Bkash Checkout Payment
     *
     * @param \App\Models\operators_online_payment $operators_online_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function queryPayment(operators_online_payment $operators_online_payment)
    {
        $payment_gateway = payment_gateway::find($operators_online_payment->payment_gateway_id);

        $credentials_file = $this->getCredentialsFileName($payment_gateway);

        $credentials = json_decode(file_get_contents($credentials_file), true);

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
        ])->get($this->query_payment_url . $operators_online_payment->pgw_payment_identifier);

        return $response;
    }

    /**
     * Search Bkash Checkout Payment
     *
     * @param \App\Models\operators_online_payment $operators_online_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function searchTransaction(operators_online_payment $operators_online_payment)
    {
        $payment_gateway = payment_gateway::find($operators_online_payment->payment_gateway_id);

        $credentials_file = $this->getCredentialsFileName($payment_gateway);

        $credentials = json_decode(file_get_contents($credentials_file), true);

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
        ])->get($this->search_transaction_url . $operators_online_payment->pgw_txnid);

        return $response;
    }

    /**
     * Success Bkash Checkout Payment for SMS
     *
     * @param \App\Models\operators_online_payment $operators_online_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function successPayment(operators_online_payment $operators_online_payment)
    {
        if ($operators_online_payment->pay_status == 'Successful') {
            return OperatorsOnlinePaymentController::store($operators_online_payment);
        } else {
            abort(500, 'Invalid Payment');
        }
    }
}
