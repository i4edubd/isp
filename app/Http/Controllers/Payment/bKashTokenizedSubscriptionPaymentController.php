<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\SubscriptionPaymentController;
use App\Models\bkash_checkout_agreement;
use App\Models\operator;
use App\Models\subscription_bill;
use App\Models\subscription_payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class bKashTokenizedSubscriptionPaymentController extends bKashTokenizedAbstractController
{
    /**
     * Initiate Bkash Tokenized Checkout Payment for Subscription Payments
     *
     * @param \App\Models\subscription_payment $subscription_payment
     *
     * @return \Illuminate\Http\Response
     */
    public function initiatePayment(subscription_payment $subscription_payment)
    {
        if ($subscription_payment->pay_status !== 'Pending') {
            abort(500, 'Invalid Request!');
        }

        $payment_gateway = $this->getPaymentGateway($subscription_payment->payment_gateway_id);
        $this->grantToken($payment_gateway);

        // msisdn
        $msisdn = '';

        $bkash_checkout_agreement = bkash_checkout_agreement::where('role', 'operator')
            ->where('operator_id', $subscription_payment->mgid)
            ->where('agreement_status', 'Completed')
            ->first();

        if ($bkash_checkout_agreement) {
            $msisdn = substr($bkash_checkout_agreement->customer_msisdn, 0, 3) . ' ** *** ' . substr($bkash_checkout_agreement->customer_msisdn, -3);
        }

        return view('admins.components.subscription_tokenized_payment', [
            'bkash_checkout_agreement' => $bkash_checkout_agreement,
            'msisdn' => $msisdn,
            'subscription_payment' => $subscription_payment,
        ]);
    }

    /**
     * Create Bkash Tokenized Checkout Agreement for Subscription Payments
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\subscription_payment $subscription_payment
     *
     * @return \Illuminate\Http\Response
     */
    public function createAgreement(Request $request, subscription_payment $subscription_payment)
    {
        $request->validate([
            'bkash_number' => 'required|numeric',
        ]);

        $payment_gateway = $this->getPaymentGateway($subscription_payment->payment_gateway_id);
        $credentials_file = $this->getCredentialsFileName($payment_gateway);
        $credentials = json_decode(file_get_contents($credentials_file), true);

        $response =  Http::timeout(30)->withHeaders([
            'content-type' => 'application/json',
            'accept' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
        ])->post($this->create_agreement_url, [
            'mode' => '0000',
            'payerReference' => $request->bkash_number,
            'callbackURL' => route('bkash_tokenized.subscription_payment.agreement_callback'),
            'amount' => $subscription_payment->amount_paid,
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => $subscription_payment->mer_txnid,
        ]);

        $data = json_decode($response, true);

        // Error
        if (array_key_exists('errorMessage', $data)) {
            return redirect()->route('bkash_tokenized.subscription_payment.initiate', ['subscription_payment' => $subscription_payment->id])->with('error', $data['errorMessage']);
        }

        if (array_key_exists('statusMessage', $data)) {
            if ($data['statusMessage'] !== 'Successful') {
                return redirect()->route('bkash_tokenized.subscription_payment.initiate', ['subscription_payment' => $subscription_payment->id])->with('error', $data['statusMessage']);
            }
        }

        // Delete Previous Agreement
        bkash_checkout_agreement::where('role', 'operator')
            ->where('operator_id', $subscription_payment->mgid)
            ->delete();

        $operator = operator::find($subscription_payment->mgid);
        // New Agreement
        $bkash_checkout_agreement = new bkash_checkout_agreement();
        $bkash_checkout_agreement->mgid = $operator->mgid;
        $bkash_checkout_agreement->operator_id = $operator->id;
        $bkash_checkout_agreement->customer_id = 0;
        $bkash_checkout_agreement->role = 'operator';
        $bkash_checkout_agreement->payment_type = 'subscription';
        $bkash_checkout_agreement->payment_id = $subscription_payment->id;
        $bkash_checkout_agreement->payer_reference = $request->bkash_number;
        $bkash_checkout_agreement->bkash_payment_id = $data['paymentID'];
        $bkash_checkout_agreement->agreement_status = 'initiated';
        $bkash_checkout_agreement->save();

        return redirect()->away($data['bkashURL']);
    }

    /**
     * callbackURL for Bkash Tokenized Checkout Agreement for Subscription Payments
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function callbackAgreement(Request $request)
    {
        $callback = $request->all();

        $bkash_checkout_agreement = bkash_checkout_agreement::where('bkash_payment_id', $callback['paymentID'])->firstOrFail();

        $subscription_payment = subscription_payment::findOrFail($bkash_checkout_agreement->payment_id);

        // Error
        if (array_key_exists('errorMessage', $callback)) {
            return redirect()->route('bkash_tokenized.subscription_payment.initiate', ['subscription_payment' => $subscription_payment->id])->with('error', $callback['errorMessage']);
        }

        switch ($callback['status']) {
            case 'success':
                return $this->executeAgreement($bkash_checkout_agreement, $subscription_payment);
                break;

            case 'cancel':
                return redirect()->route('bkash_tokenized.subscription_payment.initiate', ['subscription_payment' => $subscription_payment->id])->with('error', 'Payment Cancelled');
                break;

            default:
                // failure
                return redirect()->route('bkash_tokenized.subscription_payment.initiate', ['subscription_payment' => $subscription_payment->id])->with('error', 'Payment Failed');
                break;
        }
    }

    /**
     * Execute Bkash Tokenized Checkout Agreement for Subscription Payments
     *
     * @param \App\Models\bkash_checkout_agreement $bkash_checkout_agreement
     * @param \App\Models\subscription_payment $subscription_payment
     *
     * @return \Illuminate\Http\Response
     */
    public function executeAgreement(bkash_checkout_agreement $bkash_checkout_agreement, subscription_payment $subscription_payment)
    {
        $payment_gateway = $this->getPaymentGateway($subscription_payment->payment_gateway_id);
        $credentials_file = $this->getCredentialsFileName($payment_gateway);
        $credentials = json_decode(file_get_contents($credentials_file), true);

        $response =  Http::timeout(30)->withHeaders([
            'content-type' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
        ])->post($this->execute_agreement_url, [
            'paymentID' => $bkash_checkout_agreement->bkash_payment_id,
        ]);

        $data = json_decode($response, true);

        // Error
        if (array_key_exists('errorMessage', $data)) {
            return redirect()->route('bkash_tokenized.subscription_payment.initiate', ['subscription_payment' => $subscription_payment->id])->with('error', $data['errorMessage']);
        }

        if (array_key_exists('statusMessage', $data)) {
            if ($data['statusMessage'] !== 'Successful') {
                return redirect()->route('bkash_tokenized.subscription_payment.initiate', ['subscription_payment' => $subscription_payment->id])->with('error', $data['statusMessage']);
            }
        }

        // update Agreement
        $bkash_checkout_agreement->payer_reference = $data['payerReference'];
        $bkash_checkout_agreement->agreement_id = $data['agreementID'];
        $bkash_checkout_agreement->customer_msisdn = $data['customerMsisdn'];
        $bkash_checkout_agreement->agreement_status = $data['agreementStatus'];
        $bkash_checkout_agreement->save();

        // Call Create Payment
        return redirect()->route('bkash_tokenized.subscription_payment.create_payment', ['subscription_payment' => $subscription_payment->id, 'use_saved_bkash_number' => 1]);
    }

    /**
     * Cancel Bkash Tokenized Agreement for Subscription Payments
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\subscription_payment $subscription_payment
     *
     * @return \Illuminate\Http\Response
     */
    public function cancelAgreement(Request $request, subscription_payment $subscription_payment)
    {
        $bkash_checkout_agreement = bkash_checkout_agreement::where('role', 'operator')
            ->where('operator_id', $subscription_payment->mgid)
            ->where('agreement_status', 'Completed')
            ->firstOrFail();

        $payment_gateway = $this->getPaymentGateway($subscription_payment->payment_gateway_id);
        $credentials_file = $this->getCredentialsFileName($payment_gateway);
        $credentials = json_decode(file_get_contents($credentials_file), true);

        $response =  Http::timeout(30)->withHeaders([
            'content-type' => 'application/json',
            'accept' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
        ])->post($this->cancel_agreement_url, [
            'agreementID' => $bkash_checkout_agreement->agreement_id,
        ]);

        $data = json_decode($response, true);

        // Error
        if (array_key_exists('errorMessage', $data)) {
            return redirect()->route('bkash_tokenized.subscription_payment.initiate', ['subscription_payment' => $subscription_payment->id])->with('error', $data['errorMessage']);
        }

        if ($data['statusMessage'] !== 'Successful') {
            return redirect()->route('bkash_tokenized.subscription_payment.initiate', ['subscription_payment' => $subscription_payment->id])->with('error', $data['statusMessage']);
        }

        // Restart Payment
        $bkash_checkout_agreement->delete();
        return redirect()->route('bkash_tokenized.subscription_payment.initiate', ['subscription_payment' => $subscription_payment->id])->with('success', $data['statusMessage']);
    }

    /**
     * Create Bkash Tokenized Checkout Payment for Subscription Payments
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\subscription_payment $subscription_payment
     *
     * @return \Illuminate\Http\Response
     */
    public function createPayment(Request $request, subscription_payment $subscription_payment)
    {
        //Reset mer_txnid
        $mer_txnid = random_int(1000, 9999) . Carbon::now(config('app.timezone'))->timestamp;
        $subscription_payment->mer_txnid = $mer_txnid;
        $subscription_payment->save();

        # << Come Again After Agreement
        if ($request->filled('save_bkash_number')) {

            $request->validate([
                'bkash_number' => 'required',
            ]);

            $bkash_number = validate_mobile($request->bkash_number);

            if (!$bkash_number) {
                return redirect()->route('bkash_tokenized.subscription_payment.initiate', ['subscription_payment' => $subscription_payment->id])->with('error', 'Invlid bKash Number');
            }

            return redirect()->route('bkash_tokenized.subscription_payment.create_agreement', ['subscription_payment' => $subscription_payment->id, 'bkash_number' => $bkash_number]);
        }
        # Come Again After Agreement >>

        # << payment_body
        if ($request->filled('use_saved_bkash_number')) {
            // Pay with Agreement
            $bkash_checkout_agreement = bkash_checkout_agreement::where('role', 'operator')
                ->where('operator_id', $subscription_payment->mgid)
                ->where('agreement_status', 'Completed')
                ->firstOrFail();
            $payment_body = [
                'mode' => '0001',
                'payerReference' => $bkash_checkout_agreement->payer_reference,
                'callbackURL' => route('bkash_tokenized.subscription_payment.payment_callback'),
                'agreementID' => $bkash_checkout_agreement->agreement_id,
                'amount' => $subscription_payment->amount_paid,
                'currency' => 'BDT',
                'intent' => 'sale',
                'merchantInvoiceNumber' => $subscription_payment->mer_txnid,
            ];
        } else {
            // Pay Without Agreement
            if ($request->filled('bkash_number')) {
                $bkash_number = validate_mobile($request->bkash_number);
                if (!$bkash_number) {
                    $bkash_number = $subscription_payment->id;
                }
            } else {
                $bkash_number = $subscription_payment->id;
            }
            $payment_body = [
                'mode' => '0011',
                'payerReference' => $bkash_number,
                'callbackURL' => route('bkash_tokenized.subscription_payment.payment_callback'),
                'amount' => $subscription_payment->amount_paid,
                'currency' => 'BDT',
                'intent' => 'sale',
                'merchantInvoiceNumber' => $subscription_payment->mer_txnid,
            ];
        }
        # payment_body >>

        $payment_gateway = $this->getPaymentGateway($subscription_payment->payment_gateway_id);
        $credentials_file = $this->getCredentialsFileName($payment_gateway);
        $credentials = json_decode(file_get_contents($credentials_file), true);

        $response =  Http::timeout(30)->withHeaders([
            'content-type' => 'application/json',
            'accept' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
        ])->post($this->create_payment_url, $payment_body);

        $data = json_decode($response, true);

        // Error
        if (array_key_exists('errorMessage', $data)) {
            return redirect()->route('bkash_tokenized.subscription_payment.initiate', ['subscription_payment' => $subscription_payment->id])->with('error', $data['errorMessage']);
        }

        if (array_key_exists('statusMessage', $data)) {
            if ($data['statusMessage'] !== 'Successful') {
                return redirect()->route('bkash_tokenized.subscription_payment.initiate', ['subscription_payment' => $subscription_payment->id])->with('error', $data['statusMessage']);
            }
        }

        $subscription_payment->pgw_payment_identifier = $data['paymentID'];
        $subscription_payment->save();

        return redirect()->away($data['bkashURL']);
    }

    /**
     * callbackURL for Bkash Tokenized Checkout Payment for Subscription Payments
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function callbackPayment(Request $request)
    {
        $callback = $request->all();

        $subscription_payment = subscription_payment::where('pgw_payment_identifier', $callback['paymentID'])->firstOrFail();

        // Error
        if (array_key_exists('errorMessage', $callback)) {
            return redirect()->route('bkash_tokenized.subscription_payment.initiate', ['subscription_payment' => $subscription_payment->id])->with('error', $callback['errorMessage']);
        }

        switch ($callback['status']) {
            case 'success':
                return $this->executePayment($subscription_payment);
                break;

            case 'cancel':
                return redirect()->route('bkash_tokenized.subscription_payment.initiate', ['subscription_payment' => $subscription_payment->id])->with('error', 'Payment Cancelled');
                break;

            default:
                // failure
                return redirect()->route('bkash_tokenized.subscription_payment.initiate', ['subscription_payment' => $subscription_payment->id])->with('error', 'Payment Failed');
                break;
        }
    }

    /**
     * Execute Bkash Tokenized Checkout Payment for Subscription Payments
     *
     * @param \App\Models\subscription_payment $subscription_payment
     *
     * @return \Illuminate\Http\Response
     */
    public function executePayment(subscription_payment $subscription_payment)
    {
        $payment_gateway = $this->getPaymentGateway($subscription_payment->payment_gateway_id);
        $credentials_file = $this->getCredentialsFileName($payment_gateway);
        $credentials = json_decode(file_get_contents($credentials_file), true);

        $response =  Http::timeout(30)->withHeaders([
            'accept' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
        ])->post($this->execute_payment_url, [
            'paymentID' => $subscription_payment->pgw_payment_identifier,
        ]);

        if (config('consumer.debug_payment')) {
            Storage::put('bkash_tokenized_subscription_payment/executePayment' . $subscription_payment->id  . '.json', $response);
        }

        //If No Response From execute Query
        if ($response->failed()) {

            $response = $this->queryPayment($subscription_payment);

            $data = json_decode($response, true);

            if (array_key_exists('transactionStatus', $data)) {
                if ($data['transactionStatus'] == 'Completed') {
                    $transaction_fee = $subscription_payment->amount_paid * ($payment_gateway->service_charge_percentage / 100);
                    $subscription_payment->pgw_txnid = $data['trxID'];
                    $subscription_payment->bank_txnid = $data['trxID'];
                    $subscription_payment->pay_status = 'Successful';
                    $subscription_payment->store_amount = $subscription_payment->amount_paid - $transaction_fee;
                    $subscription_payment->transaction_fee = $transaction_fee;
                    $subscription_payment->save();
                    SubscriptionPaymentController::recordIncomes($subscription_payment);
                    $subscription_bill = subscription_bill::find($subscription_payment->subscription_bill_id);
                    if ($subscription_bill) {
                        $subscription_bill->delete();
                    }
                    return redirect()->route('subscription_payments.index')->with('success', 'Payment Successful');
                } else {
                    return redirect()->route('subscription_payments.index')->with('error', $data['transactionStatus']);
                }
            } else {
                return redirect()->route('subscription_payments.index')->with('error', 'Query Payment Failed');
            }
        }

        $data = json_decode($response, true);

        // Error
        if (array_key_exists('errorMessage', $data)) {
            return redirect()->route('bkash_tokenized.subscription_payment.initiate', ['subscription_payment' => $subscription_payment->id])->with('error', $data['errorMessage']);
        }

        if (array_key_exists('statusMessage', $data)) {
            if ($data['statusMessage'] !== 'Successful') {
                return redirect()->route('bkash_tokenized.subscription_payment.initiate', ['subscription_payment' => $subscription_payment->id])->with('error', 'Payment Failed : ' . $data['statusMessage']);
            }
        }

        $transaction_fee = $subscription_payment->amount_paid * ($payment_gateway->service_charge_percentage / 100);
        $subscription_payment->pgw_txnid = $data['trxID'];
        $subscription_payment->bank_txnid = $data['trxID'];
        $subscription_payment->pay_status = 'Successful';
        $subscription_payment->store_amount = $subscription_payment->amount_paid - $transaction_fee;
        $subscription_payment->transaction_fee = $transaction_fee;
        $subscription_payment->save();
        SubscriptionPaymentController::recordIncomes($subscription_payment);
        $subscription_bill = subscription_bill::find($subscription_payment->subscription_bill_id);
        if ($subscription_bill) {
            $subscription_bill->delete();
        }
        return redirect()->route('subscription_payments.index')->with('success', 'Payment Successful');
    }

    /**
     * Query Bkash Tokenized Checkout Payment for Subscription Payments
     *
     * @param \App\Models\subscription_payment $subscription_payment
     *
     * @return \Illuminate\Http\Response
     */
    public function queryPayment(subscription_payment $subscription_payment)
    {
        $payment_gateway = $this->getPaymentGateway($subscription_payment->payment_gateway_id);
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
            'paymentID' => $subscription_payment->pgw_payment_identifier,
        ];

        $response = Http::timeout(30)->withHeaders($header)->post($this->query_payment_url, $body);

        return $response;
    }

    /**
     * Search Bkash Tokenized Checkout Payment for Subscription Payments
     *
     * @param \App\Models\subscription_payment $subscription_payment
     *
     * @return \Illuminate\Http\Response
     */
    public function searchTransaction(subscription_payment $subscription_payment)
    {
        $payment_gateway = $this->getPaymentGateway($subscription_payment->payment_gateway_id);
        $this->grantToken($payment_gateway);
        $credentials_file = $this->getCredentialsFileName($payment_gateway);
        $credentials = json_decode(file_get_contents($credentials_file), true);

        $response = Http::timeout(30)->withHeaders([
            'accept' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
        ])->post($this->search_transaction_url, [
            'trxID' => $subscription_payment->pgw_txnid,
        ]);

        return $response;
    }

    public function refundTransaction()
    {
    }

    public function refundStatus()
    {
    }

    /**
     * Recheck Subscription Payment
     *
     * @param \App\Models\subscription_payment $subscription_payment
     *
     * @return \Illuminate\Http\Response
     */
    public function recheckPayment(subscription_payment $subscription_payment)
    {
        //Payment Initiate was Failed
        if (strlen($subscription_payment->pgw_payment_identifier) == 0) {
            $subscription_payment->delete();
            return 0;
        }

        $response = $this->queryPayment($subscription_payment);

        $reply = json_decode($response, true);

        if (array_key_exists('transactionStatus', $reply)) {
            if ($reply['transactionStatus'] === 'Completed') {
                //update Subscription Payment
                $subscription_payment->pgw_txnid = $reply['trxID'];
                $subscription_payment->bank_txnid = $reply['trxID'];
                $subscription_payment->pay_status = 'Successful';
                $subscription_payment->store_amount = $reply['amount'];
                $subscription_payment->transaction_fee = 0;
                $subscription_payment->save();
                SubscriptionPaymentController::recordIncomes($subscription_payment);
                $subscription_bill = subscription_bill::find($subscription_payment->subscription_bill_id);
                if ($subscription_bill) {
                    $subscription_bill->delete();
                }
                return 1;
            } else {
                $subscription_payment->delete();
                return 0;
            }
        }
    }
}
