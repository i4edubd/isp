<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Sms\SmsBalanceHistoryController;
use App\Models\bkash_checkout_agreement;
use App\Models\operator;
use App\Models\sms_bill;
use App\Models\sms_payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class bKashTokenizedSmsPaymentController extends bKashTokenizedAbstractController
{
    /**
     * Initiate Bkash Tokenized Checkout Payment for SMS
     *
     * @param \App\Models\sms_payment $sms_payment
     *
     * @return \Illuminate\Http\Response
     */
    public function initiatePayment(sms_payment $sms_payment)
    {
        if ($sms_payment->pay_status !== 'Pending') {
            abort(500, 'Invalid Request!');
        }

        $payment_gateway = $this->getPaymentGateway($sms_payment->payment_gateway_id);

        $this->grantToken($payment_gateway);

        // msisdn
        $msisdn = '';

        $bkash_checkout_agreement = bkash_checkout_agreement::where('role', 'operator')
            ->where('operator_id', $sms_payment->operator_id)
            ->where('agreement_status', 'Completed')
            ->first();

        if ($bkash_checkout_agreement) {
            $msisdn = substr($bkash_checkout_agreement->customer_msisdn, 0, 3) . ' ** *** ' . substr($bkash_checkout_agreement->customer_msisdn, -3);
        }

        return view('admins.components.sms_tokenized_payment', [
            'bkash_checkout_agreement' => $bkash_checkout_agreement,
            'msisdn' => $msisdn,
            'sms_payment' => $sms_payment,
        ]);
    }

    /**
     * Create Bkash Tokenized Checkout Agreement for SMS
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\sms_payment $sms_payment
     *
     * @return \Illuminate\Http\Response
     */
    public function createAgreement(Request $request, sms_payment $sms_payment)
    {
        $request->validate([
            'bkash_number' => 'required|numeric',
        ]);

        $payment_gateway = $this->getPaymentGateway($sms_payment->payment_gateway_id);

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
            'callbackURL' => route('bkash_tokenized.sms_payment.agreement_callback'),
            'amount' => $sms_payment->amount_paid,
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => $sms_payment->mer_txnid,
        ]);

        $data = json_decode($response, true);

        // Error
        if (array_key_exists('errorMessage', $data)) {
            return redirect()->route('bkash_tokenized.sms_payment.initiate', ['sms_payment' => $sms_payment->id])->with('error', $data['errorMessage']);
        }

        if (array_key_exists('statusMessage', $data)) {
            if ($data['statusMessage'] !== 'Successful') {
                return redirect()->route('bkash_tokenized.sms_payment.initiate', ['sms_payment' => $sms_payment->id])->with('error', $data['statusMessage']);
            }
        }

        // Delete Previous Agreement
        bkash_checkout_agreement::where('role', 'operator')
            ->where('operator_id', $sms_payment->operator_id)
            ->delete();

        $operator = operator::find($sms_payment->operator_id);
        // New Agreement
        $bkash_checkout_agreement = new bkash_checkout_agreement();
        $bkash_checkout_agreement->mgid = $operator->mgid;
        $bkash_checkout_agreement->operator_id = $operator->id;
        $bkash_checkout_agreement->customer_id = 0;
        $bkash_checkout_agreement->role = 'operator';
        $bkash_checkout_agreement->payment_type = 'sms';
        $bkash_checkout_agreement->payment_id = $sms_payment->id;
        $bkash_checkout_agreement->payer_reference = $request->bkash_number;
        $bkash_checkout_agreement->bkash_payment_id = $data['paymentID'];
        $bkash_checkout_agreement->agreement_status = 'initiated';
        $bkash_checkout_agreement->save();

        return redirect()->away($data['bkashURL']);
    }

    /**
     * callbackURL for Bkash Tokenized Checkout Agreement for SMS
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function callbackAgreement(Request $request)
    {
        $callback = $request->all();

        $bkash_checkout_agreement = bkash_checkout_agreement::where('bkash_payment_id', $callback['paymentID'])->firstOrFail();

        $sms_payment = sms_payment::findOrFail($bkash_checkout_agreement->payment_id);

        // Error
        if (array_key_exists('errorMessage', $callback)) {
            return redirect()->route('bkash_tokenized.sms_payment.initiate', ['sms_payment' => $sms_payment->id])->with('error', $callback['errorMessage']);
        }

        switch ($callback['status']) {
            case 'success':
                return $this->executeAgreement($bkash_checkout_agreement, $sms_payment);
                break;

            case 'cancel':
                return redirect()->route('bkash_tokenized.sms_payment.initiate', ['sms_payment' => $sms_payment->id])->with('error', 'Payment Cancelled');
                break;

            default:
                // failure
                return redirect()->route('bkash_tokenized.sms_payment.initiate', ['sms_payment' => $sms_payment->id])->with('error', 'Payment Failed');
                break;
        }
    }

    /**
     * Execute Bkash Tokenized Checkout Agreement for SMS
     *
     * @param \App\Models\bkash_checkout_agreement $bkash_checkout_agreement
     * @param \App\Models\sms_payment $sms_payment
     *
     * @return \Illuminate\Http\Response
     */
    public function executeAgreement(bkash_checkout_agreement $bkash_checkout_agreement, sms_payment $sms_payment)
    {
        $payment_gateway = $this->getPaymentGateway($sms_payment->payment_gateway_id);

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
            return redirect()->route('bkash_tokenized.sms_payment.initiate', ['sms_payment' => $sms_payment->id])->with('error', $data['errorMessage']);
        }

        if (array_key_exists('statusMessage', $data)) {
            if ($data['statusMessage'] !== 'Successful') {
                return redirect()->route('bkash_tokenized.sms_payment.initiate', ['sms_payment' => $sms_payment->id])->with('error', $data['statusMessage']);
            }
        }

        // update Agreement
        $bkash_checkout_agreement->payer_reference = $data['payerReference'];
        $bkash_checkout_agreement->agreement_id = $data['agreementID'];
        $bkash_checkout_agreement->customer_msisdn = $data['customerMsisdn'];
        $bkash_checkout_agreement->agreement_status = $data['agreementStatus'];
        $bkash_checkout_agreement->save();

        // Call Create Payment
        return redirect()->route('bkash_tokenized.sms_payment.create_payment', ['sms_payment' => $sms_payment->id, 'use_saved_bkash_number' => 1]);
    }

    /**
     * Cancel Bkash Tokenized Agreement for SMS
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\sms_payment $sms_payment
     *
     * @return \Illuminate\Http\Response
     */
    public function cancelAgreement(Request $request, sms_payment $sms_payment)
    {
        $bkash_checkout_agreement = bkash_checkout_agreement::where('role', 'operator')
            ->where('operator_id', $sms_payment->operator_id)
            ->where('agreement_status', 'Completed')
            ->firstOrFail();

        $payment_gateway = $this->getPaymentGateway($sms_payment->payment_gateway_id);

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
            return redirect()->route('bkash_tokenized.sms_payment.initiate', ['sms_payment' => $sms_payment->id])->with('error', $data['errorMessage']);
        }

        if ($data['statusMessage'] !== 'Successful') {
            return redirect()->route('bkash_tokenized.sms_payment.initiate', ['sms_payment' => $sms_payment->id])->with('error', $data['statusMessage']);
        }

        // Restart Payment
        $bkash_checkout_agreement->delete();
        return redirect()->route('bkash_tokenized.sms_payment.initiate', ['sms_payment' => $sms_payment->id])->with('success', $data['statusMessage']);
    }

    /**
     * Create Bkash Tokenized Checkout Payment for SMS
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\sms_payment $sms_payment
     *
     * @return \Illuminate\Http\Response
     */
    public function createPayment(Request $request, sms_payment $sms_payment)
    {
        //Reset mer_txnid
        $mer_txnid = random_int(1000, 9999) . Carbon::now(config('app.timezone'))->timestamp;
        $sms_payment->mer_txnid = $mer_txnid;
        $sms_payment->save();

        # << Come Again After Agreement
        if ($request->filled('save_bkash_number')) {

            $request->validate([
                'bkash_number' => 'required',
            ]);

            $bkash_number = validate_mobile($request->bkash_number);

            if (!$bkash_number) {
                return redirect()->route('bkash_tokenized.sms_payment.initiate', ['sms_payment' => $sms_payment->id])->with('error', 'Invlid bKash Number');
            }

            return redirect()->route('bkash_tokenized.sms_payment.create_agreement', ['sms_payment' => $sms_payment->id, 'bkash_number' => $bkash_number]);
        }
        # Come Again After Agreement >>

        # << payment_body
        if ($request->filled('use_saved_bkash_number')) {
            // Pay with Agreement
            $bkash_checkout_agreement = bkash_checkout_agreement::where('role', 'operator')
                ->where('operator_id', $sms_payment->operator_id)
                ->where('agreement_status', 'Completed')
                ->firstOrFail();
            $payment_body = [
                'mode' => '0001',
                'payerReference' => $bkash_checkout_agreement->payer_reference,
                'callbackURL' => route('bkash_tokenized.sms_payment.payment_callback'),
                'agreementID' => $bkash_checkout_agreement->agreement_id,
                'amount' => $sms_payment->amount_paid,
                'currency' => 'BDT',
                'intent' => 'sale',
                'merchantInvoiceNumber' => $sms_payment->mer_txnid,
            ];
        } else {
            // Pay Without Agreement
            if ($request->filled('bkash_number')) {
                $bkash_number = validate_mobile($request->bkash_number);
                if (!$bkash_number) {
                    $bkash_number = $sms_payment->id;
                }
            } else {
                $bkash_number = $sms_payment->id;
            }
            $payment_body = [
                'mode' => '0011',
                'payerReference' => $bkash_number,
                'callbackURL' => route('bkash_tokenized.sms_payment.payment_callback'),
                'amount' => $sms_payment->amount_paid,
                'currency' => 'BDT',
                'intent' => 'sale',
                'merchantInvoiceNumber' => $sms_payment->mer_txnid,
            ];
        }
        # payment_body >>

        $payment_gateway = $this->getPaymentGateway($sms_payment->payment_gateway_id);

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
            return redirect()->route('bkash_tokenized.sms_payment.initiate', ['sms_payment' => $sms_payment->id])->with('error', $data['errorMessage']);
        }

        if (array_key_exists('statusMessage', $data)) {
            if ($data['statusMessage'] !== 'Successful') {
                return redirect()->route('bkash_tokenized.sms_payment.initiate', ['sms_payment' => $sms_payment->id])->with('error', $data['statusMessage']);
            }
        }

        $sms_payment->pgw_payment_identifier = $data['paymentID'];
        $sms_payment->save();

        return redirect()->away($data['bkashURL']);
    }

    /**
     * callbackURL for Bkash Tokenized Checkout Payment for SMS
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function callbackPayment(Request $request)
    {
        $callback = $request->all();

        $sms_payment = sms_payment::where('pgw_payment_identifier', $callback['paymentID'])->firstOrFail();

        // Error
        if (array_key_exists('errorMessage', $callback)) {
            return redirect()->route('bkash_tokenized.sms_payment.initiate', ['sms_payment' => $sms_payment->id])->with('error', $callback['errorMessage']);
        }

        switch ($callback['status']) {
            case 'success':
                return $this->executePayment($sms_payment);
                break;

            case 'cancel':
                return redirect()->route('bkash_tokenized.sms_payment.initiate', ['sms_payment' => $sms_payment->id])->with('error', 'Payment Cancelled');
                break;

            default:
                // failure
                return redirect()->route('bkash_tokenized.sms_payment.initiate', ['sms_payment' => $sms_payment->id])->with('error', 'Payment Failed');
                break;
        }
    }

    /**
     * Execute Bkash Tokenized Checkout Payment for SMS
     *
     * @param \App\Models\sms_payment $sms_payment
     *
     * @return \Illuminate\Http\Response
     */
    public function executePayment(sms_payment $sms_payment)
    {
        $payment_gateway = $this->getPaymentGateway($sms_payment->payment_gateway_id);

        $credentials_file = $this->getCredentialsFileName($payment_gateway);

        $credentials = json_decode(file_get_contents($credentials_file), true);

        $response =  Http::timeout(30)->withHeaders([
            'accept' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
        ])->post($this->execute_payment_url, [
            'paymentID' => $sms_payment->pgw_payment_identifier,
        ]);

        if (config('consumer.debug_payment')) {
            Storage::put('bkash_tokenized_sms/executePayment' . $sms_payment->id  . '.json', $response);
        }

        //If No Response From execute Query
        if ($response->failed()) {

            $response = $this->queryPayment($sms_payment);

            $data = json_decode($response, true);

            if (array_key_exists('transactionStatus', $data)) {
                if ($data['transactionStatus'] == 'Completed') {
                    $transaction_fee = $sms_payment->amount_paid * ($payment_gateway->service_charge_percentage / 100);
                    $sms_payment->pgw_txnid = $data['trxID'];
                    $sms_payment->bank_txnid = $data['trxID'];
                    $sms_payment->pay_status = 'Successful';
                    $sms_payment->store_amount = $sms_payment->amount_paid - $transaction_fee;
                    $sms_payment->transaction_fee = $transaction_fee;
                    $sms_payment->save();
                    SmsBalanceHistoryController::store($sms_payment);
                    $sms_bill = sms_bill::find($sms_payment->sms_bill_id);
                    if ($sms_bill) {
                        $sms_bill->delete();
                    }
                    return redirect()->route('sms_payments.index')->with('success', 'Payment Successful');
                } else {
                    return redirect()->route('sms_payments.index')->with('error', $data['transactionStatus']);
                }
            } else {
                return redirect()->route('sms_payments.index')->with('error', 'Query Payment Failed');
            }
        }

        $data = json_decode($response, true);

        // Error
        if (array_key_exists('errorMessage', $data)) {
            return redirect()->route('bkash_tokenized.sms_payment.initiate', ['sms_payment' => $sms_payment->id])->with('error', $data['errorMessage']);
        }

        if (array_key_exists('statusMessage', $data)) {
            if ($data['statusMessage'] !== 'Successful') {
                return redirect()->route('bkash_tokenized.sms_payment.initiate', ['sms_payment' => $sms_payment->id])->with('error', 'Payment Failed : ' . $data['statusMessage']);
            }
        }

        $transaction_fee = $sms_payment->amount_paid * ($payment_gateway->service_charge_percentage / 100);
        $sms_payment->pgw_txnid = $data['trxID'];
        $sms_payment->bank_txnid = $data['trxID'];
        $sms_payment->pay_status = 'Successful';
        $sms_payment->store_amount = $sms_payment->amount_paid - $transaction_fee;
        $sms_payment->transaction_fee = $transaction_fee;
        $sms_payment->save();
        SmsBalanceHistoryController::store($sms_payment);
        $sms_bill = sms_bill::find($sms_payment->sms_bill_id);
        if ($sms_bill) {
            $sms_bill->delete();
        }
        return redirect()->route('sms_payments.index')->with('success', 'Payment Successful');
    }


    /**
     * Query Bkash Tokenized Checkout Payment for SMS
     *
     * @param \App\Models\sms_payment $sms_payment
     *
     * @return \Illuminate\Http\Response
     */
    public function queryPayment(sms_payment $sms_payment)
    {
        $payment_gateway = $this->getPaymentGateway($sms_payment->payment_gateway_id);

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
            'paymentID' => $sms_payment->pgw_payment_identifier,
        ];

        $response = Http::timeout(30)->withHeaders($header)->post($this->query_payment_url, $body);

        return $response;
    }

    /**
     * Search Bkash Tokenized Checkout Payment for SMS
     *
     * @param \App\Models\sms_payment $sms_payment
     *
     * @return \Illuminate\Http\Response
     */
    public function searchTransaction(sms_payment $sms_payment)
    {
        $payment_gateway = $this->getPaymentGateway($sms_payment->payment_gateway_id);

        $this->grantToken($payment_gateway);

        $credentials_file = $this->getCredentialsFileName($payment_gateway);

        $credentials = json_decode(file_get_contents($credentials_file), true);

        $response = Http::timeout(30)->withHeaders([
            'accept' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
        ])->post($this->search_transaction_url, [
            'trxID' => $sms_payment->pgw_txnid,
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
     * Recheck SMS Payment
     *
     * @param \App\Models\sms_payment $sms_payment
     *
     * @return \Illuminate\Http\Response
     */
    public function recheckPayment(sms_payment $sms_payment)
    {
        //Payment Initiate was Failed
        if (strlen($sms_payment->pgw_payment_identifier) == 0) {
            $sms_payment->delete();
            return 0;
        }

        $response = $this->queryPayment($sms_payment);

        $reply = json_decode($response, true);

        if (array_key_exists('transactionStatus', $reply)) {
            if ($reply['transactionStatus'] === 'Completed') {
                //update SMS Payment
                $sms_payment->pgw_txnid = $reply['trxID'];
                $sms_payment->bank_txnid = $reply['trxID'];
                $sms_payment->pay_status = 'Successful';
                $sms_payment->store_amount = $reply['amount'];
                $sms_payment->transaction_fee = 0;
                $sms_payment->save();
                SmsBalanceHistoryController::store($sms_payment);
                $sms_bill = sms_bill::find($sms_payment->sms_bill_id);
                if ($sms_bill) {
                    $sms_bill->delete();
                }
                return 1;
            } else {
                $sms_payment->delete();
                return 0;
            }
        }
    }
}
