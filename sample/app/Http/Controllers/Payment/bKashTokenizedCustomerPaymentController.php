<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\CacheController;
use App\Http\Controllers\CustomerPayment\CustomersPaymentProcessController;
use App\Models\bkash_checkout_agreement;
use App\Models\customer_payment;
use App\Models\package;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class bKashTokenizedCustomerPaymentController extends bKashTokenizedAbstractController
{

    /**
     * Initiate Bkash Tokenized Checkout Payment for Customer
     *
     * @param \App\Models\customer_payment $customer_payment
     * @return \Illuminate\Http\Response
     */
    public function initiatePayment(customer_payment $customer_payment)
    {
        if ($customer_payment->pay_status !== 'Pending') {
            abort(500, 'Invalid Request!');
        }

        // operator
        $operator = CacheController::getOperator($customer_payment->operator_id);

        //package
        $package = package::find($customer_payment->package_id);

        //get token
        $payment_gateway = $this->getPaymentGateway($customer_payment->payment_gateway_id);
        $this->grantToken($payment_gateway);

        //payment_token
        $payment_token = $this->token();
        $customer_payment->payment_token = $payment_token;
        $customer_payment->save();

        // msisdn
        $msisdn = '';

        $bkash_checkout_agreement = bkash_checkout_agreement::where('mgid', $customer_payment->mgid)
            ->where('customer_id', $customer_payment->customer_id)
            ->where('agreement_status', 'Completed')
            ->first();

        if ($bkash_checkout_agreement) {
            $msisdn = substr($bkash_checkout_agreement->customer_msisdn, 0, 3) . ' ** *** ' . substr($bkash_checkout_agreement->customer_msisdn, -3);
        }

        return view('customers.bkash_tokenized_payment', [
            'operator' => $operator,
            'package' => $package,
            'customer_payment' => $customer_payment,
            'bkash_checkout_agreement' => $bkash_checkout_agreement,
            'msisdn' => $msisdn,
        ]);
    }

    /**
     * Create Bkash Tokenized Checkout Agreement for Customer
     *
     * @param  \Illuminate\Http\Request  $request
     * @param \App\Models\customer_payment $customer_payment
     * @return \Illuminate\Http\Response
     */
    public function createAgreement(Request $request, customer_payment $customer_payment)
    {
        $request->validate([
            'bkash_number' => 'required|numeric',
        ]);

        # <<Payment Token Validation
        $request->validate([
            'token' => 'required|string',
        ]);

        if ($request->token !== $customer_payment->payment_token) {
            abort(500, 'Invalid Token');
        }
        # Payment Token Validation>>

        $all_customer = $request->user('customer');
        $customer = CacheController::getCustomerUseAllCustomer($all_customer);

        $payment_gateway = $this->getPaymentGateway($customer_payment->payment_gateway_id);

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
            'callbackURL' => route('bkash_tokenized.customer_payment.agreement_callback'),
            'amount' => $customer_payment->amount_paid,
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => $customer_payment->mer_txnid,
        ]);

        if (config('consumer.debug_payment')) {
            Storage::put('bkash_tokenized_debug/createAgreement.json', $response);
        }

        $data = json_decode($response, true);

        // Error
        if (array_key_exists('errorMessage', $data)) {
            return redirect()->route('customers.home')->with('error', $data['errorMessage']);
        }

        if (array_key_exists('statusMessage', $data)) {
            if ($data['statusMessage'] !== 'Successful') {
                return redirect()->route('bkash_tokenized.customer_payment.initiate', ['customer_payment' => $customer_payment->id])->with('error', $data['statusMessage']);
            }
        }

        // Delete Previous Agreement
        bkash_checkout_agreement::where('mgid', $customer->mgid)
            ->where('customer_id', $customer->id)
            ->delete();

        // New Agreement
        $bkash_checkout_agreement = new bkash_checkout_agreement();
        $bkash_checkout_agreement->mgid = $customer->mgid;
        $bkash_checkout_agreement->operator_id = $customer->operator_id;
        $bkash_checkout_agreement->customer_id = $customer->id;
        $bkash_checkout_agreement->role = 'customer';
        $bkash_checkout_agreement->payment_type = 'internet';
        $bkash_checkout_agreement->payment_id = $customer_payment->id;
        $bkash_checkout_agreement->payer_reference = $request->bkash_number;
        $bkash_checkout_agreement->bkash_payment_id = $data['paymentID'];
        $bkash_checkout_agreement->agreement_status = 'initiated';
        $bkash_checkout_agreement->save();

        return redirect()->away($data['bkashURL']);
    }

    /**
     * callbackURL for Bkash Tokenized Checkout Agreement for Customer
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function callbackAgreement(Request $request)
    {
        $callback = $request->all();

        if (config('consumer.debug_payment')) {
            Storage::put('bkash_tokenized_debug/callbackAgreement.json', json_encode($callback));
        }

        $bkash_checkout_agreement = bkash_checkout_agreement::where('bkash_payment_id', $callback['paymentID'])->firstOrFail();

        $customer_payment = customer_payment::findOrFail($bkash_checkout_agreement->payment_id);

        // Error
        if (array_key_exists('errorMessage', $callback)) {
            return redirect()->route('customers.home')->with('error', $callback['errorMessage']);
        }

        switch ($callback['status']) {
            case 'success':
                return $this->executeAgreement($bkash_checkout_agreement, $customer_payment);
                break;

            case 'cancel':
                return redirect()->route('bkash_tokenized.customer_payment.initiate', ['customer_payment' => $customer_payment->id])->with('error', 'Payment Cancelled');
                break;

            default:
                // failure
                return redirect()->route('bkash_tokenized.customer_payment.initiate', ['customer_payment' => $customer_payment->id])->with('error', 'Payment Failed');
                break;
        }
    }

    /**
     * Execute Bkash Tokenized Checkout Agreement for Customer
     *
     * @param \App\Models\bkash_checkout_agreement $bkash_checkout_agreement
     * @param \App\Models\customer_payment $customer_payment
     * @return \Illuminate\Http\Response
     */
    public function executeAgreement(bkash_checkout_agreement $bkash_checkout_agreement, customer_payment $customer_payment)
    {
        $payment_gateway = $this->getPaymentGateway($customer_payment->payment_gateway_id);

        $credentials_file = $this->getCredentialsFileName($payment_gateway);

        $credentials = json_decode(file_get_contents($credentials_file), true);

        $response =  Http::timeout(30)->withHeaders([
            'content-type' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
        ])->post($this->execute_agreement_url, [
            'paymentID' => $bkash_checkout_agreement->bkash_payment_id,
        ]);

        if (config('consumer.debug_payment')) {
            Storage::put('bkash_tokenized_debug/executeAgreement.json', $response);
        }

        $data = json_decode($response, true);

        // Error
        if (array_key_exists('errorMessage', $data)) {
            return redirect()->route('customers.home')->with('error', $data['errorMessage']);
        }

        if (array_key_exists('statusMessage', $data)) {
            if ($data['statusMessage'] !== 'Successful') {
                return redirect()->route('bkash_tokenized.customer_payment.initiate', ['customer_payment' => $customer_payment->id])->with('error', $data['statusMessage']);
            }
        }

        // update Agreement
        $bkash_checkout_agreement->payer_reference = $data['payerReference'];
        $bkash_checkout_agreement->agreement_id = $data['agreementID'];
        $bkash_checkout_agreement->customer_msisdn = $data['customerMsisdn'];
        $bkash_checkout_agreement->agreement_status = $data['agreementStatus'];
        $bkash_checkout_agreement->save();

        // Call Create Payment
        return redirect()->route('bkash_tokenized.customer_payment.create_payment', ['customer_payment' => $customer_payment->id, 'token' => $customer_payment->payment_token, 'use_saved_bkash_number' => 1]);
    }

    /**
     * Cancel Bkash Tokenized Agreement for Customer
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\customer_payment $customer_payment
     * @return \Illuminate\Http\Response
     */
    public function cancelAgreement(Request $request, customer_payment $customer_payment)
    {
        $bkash_checkout_agreement = bkash_checkout_agreement::where('mgid', $customer_payment->mgid)
            ->where('customer_id', $customer_payment->customer_id)
            ->where('agreement_status', 'Completed')
            ->firstOrFail();

        $payment_gateway = $this->getPaymentGateway($customer_payment->payment_gateway_id);

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

        if (config('consumer.debug_payment')) {
            Storage::put('bkash_tokenized_debug/cancelAgreement.json', $response);
        }

        $data = json_decode($response, true);

        // Error
        if (array_key_exists('errorMessage', $data)) {
            return redirect()->route('customers.home')->with('error', $data['errorMessage']);
        }

        if ($data['statusMessage'] !== 'Successful') {
            return redirect()->route('bkash_tokenized.customer_payment.initiate', ['customer_payment' => $customer_payment->id])->with('error', $data['statusMessage']);
        }

        // Restart Payment
        $bkash_checkout_agreement->delete();
        return redirect()->route('bkash_tokenized.customer_payment.initiate', ['customer_payment' => $customer_payment->id])->with('success', $data['statusMessage']);
    }

    /**
     * Create Bkash Tokenized Checkout Payment for Customer
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\customer_payment $customer_payment
     *
     * @return \Illuminate\Http\Response
     */
    public function createPayment(Request $request, customer_payment $customer_payment)
    {
        # <<Payment Token Validation
        $request->validate([
            'token' => 'required|string',
        ]);

        if ($request->token !== $customer_payment->payment_token) {
            abort(500, 'Invalid Token');
        }
        # Payment Token Validation>>

        # << Come Again After Agreement
        if ($request->filled('save_bkash_number')) {

            $request->validate([
                'bkash_number' => 'required',
            ]);

            $bkash_number = validate_mobile($request->bkash_number);

            if (!$bkash_number) {
                return redirect()->route('bkash_tokenized.customer_payment.initiate', ['customer_payment' => $customer_payment->id])->with('error', 'Invlid bKash Number');
            }

            return redirect()->route('bkash_tokenized.customer_payment.create_agreement', ['customer_payment' => $customer_payment->id, 'bkash_number' => $bkash_number, 'token' => $customer_payment->payment_token]);
        }
        # Come Again After Agreement >>

        # << payment_body
        if ($request->filled('use_saved_bkash_number')) {
            // Pay with Agreement
            $bkash_checkout_agreement = bkash_checkout_agreement::where('mgid', $customer_payment->mgid)
                ->where('customer_id', $customer_payment->customer_id)
                ->where('agreement_status', 'Completed')
                ->firstOrFail();
            $payment_body = [
                'mode' => '0001',
                'payerReference' => $bkash_checkout_agreement->payer_reference,
                'callbackURL' => route('bkash_tokenized.customer_payment.payment_callback'),
                'agreementID' => $bkash_checkout_agreement->agreement_id,
                'amount' => $customer_payment->amount_paid,
                'currency' => 'BDT',
                'intent' => 'sale',
                'merchantInvoiceNumber' => $customer_payment->mer_txnid,
            ];
        } else {
            // Pay Without Agreement
            if ($request->filled('bkash_number')) {
                $bkash_number = validate_mobile($request->bkash_number);
                if (!$bkash_number) {
                    $bkash_number = $customer_payment->mobile;
                }
            } else {
                $bkash_number = $customer_payment->mobile;
            }
            $payment_body = [
                'mode' => '0011',
                'payerReference' => $bkash_number,
                'callbackURL' => route('bkash_tokenized.customer_payment.payment_callback'),
                'amount' => $customer_payment->amount_paid,
                'currency' => 'BDT',
                'intent' => 'sale',
                'merchantInvoiceNumber' => $customer_payment->mer_txnid,
            ];
        }
        # payment_body >>

        //Reset mer_txnid
        $mer_txnid = random_int(1000, 9999) . Carbon::now(config('app.timezone'))->timestamp;
        $customer_payment->mer_txnid = $mer_txnid;
        $customer_payment->save();

        $payment_gateway = $this->getPaymentGateway($customer_payment->payment_gateway_id);

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
            return redirect()->route('customers.home')->with('error', $data['errorMessage']);
        }

        if (array_key_exists('statusMessage', $data)) {
            if ($data['statusMessage'] !== 'Successful') {
                return redirect()->route('customers.home')->with('error', $data['statusMessage']);
            }
        }

        $customer_payment->pgw_payment_identifier = $data['paymentID'];
        $customer_payment->save();

        return redirect()->away($data['bkashURL']);
    }

    /**
     * callbackURL for Bkash Tokenized Checkout Payment for Customer
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function callbackPayment(Request $request)
    {
        $callback = $request->all();

        if (config('consumer.debug_payment')) {
            Storage::put('bkash_tokenized_debug/callbackPayment.json', json_encode($callback));
        }

        $customer_payment = customer_payment::where('pgw_payment_identifier', $callback['paymentID'])->firstOrFail();

        // Error
        if (array_key_exists('errorMessage', $callback)) {
            return redirect()->route('bkash_tokenized.customer_payment.initiate', ['customer_payment' => $customer_payment->id])->with('error', $callback['errorMessage']);
        }

        switch ($callback['status']) {
            case 'success':
                return $this->executePayment($customer_payment);
                break;

            case 'cancel':
                return redirect()->route('bkash_tokenized.customer_payment.initiate', ['customer_payment' => $customer_payment->id])->with('error', 'Payment Cancelled');
                break;

            default:
                // failure
                return redirect()->route('bkash_tokenized.customer_payment.initiate', ['customer_payment' => $customer_payment->id])->with('error', 'Payment Failed');
                break;
        }
    }

    /**
     * Execute Bkash Tokenized Checkout Payment for Customer
     *
     * @param \App\Models\customer_payment $customer_payment
     *
     * @return \Illuminate\Http\Response
     */
    public function executePayment(customer_payment $customer_payment)
    {
        $payment_gateway = $this->getPaymentGateway($customer_payment->payment_gateway_id);

        $credentials_file = $this->getCredentialsFileName($payment_gateway);

        $credentials = json_decode(file_get_contents($credentials_file), true);

        $response =  Http::timeout(30)->withHeaders([
            'accept' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
        ])->post($this->execute_payment_url, [
            'paymentID' => $customer_payment->pgw_payment_identifier,
        ]);

        if (config('consumer.debug_payment')) {
            Storage::put('bkash_tokenized_debug/executePayment.json', $response);
        }

        //If No Response From execute Query
        if ($response->failed()) {

            $response = $this->queryPayment($customer_payment);

            $data = json_decode($response, true);

            if (array_key_exists('transactionStatus', $data)) {
                if ($data['transactionStatus'] == 'Completed') {
                    $transaction_fee = $customer_payment->amount_paid * ($payment_gateway->service_charge_percentage / 100);
                    $customer_payment->pgw_txnid = $data['trxID'];
                    $customer_payment->bank_txnid = $data['trxID'];
                    $customer_payment->pay_status = 'Successful';
                    $customer_payment->store_amount = $customer_payment->amount_paid - $transaction_fee;
                    $customer_payment->transaction_fee = $transaction_fee;
                    $customer_payment->save();
                    CustomersPaymentProcessController::store($customer_payment);
                    return redirect()->route('customers.profile')->with('success', 'Payment Successful');
                } else {
                    return redirect()->route('customers.profile')->with('error', $data['transactionStatus']);
                }
            } else {
                return redirect()->route('customers.profile')->with('error', 'Query Payment Failed');
            }
        }

        $data = json_decode($response, true);

        // Error
        if (array_key_exists('errorMessage', $data)) {
            return redirect()->route('customers.home')->with('error', $data['errorMessage']);
        }

        if (array_key_exists('statusMessage', $data)) {
            if ($data['statusMessage'] !== 'Successful') {
                return redirect()->route('customers.home')->with('error', 'Payment Failed : ' . $data['statusMessage']);
            }
        }

        $transaction_fee = $customer_payment->amount_paid * ($payment_gateway->service_charge_percentage / 100);
        $customer_payment->pgw_txnid = $data['trxID'];
        $customer_payment->bank_txnid = $data['trxID'];
        $customer_payment->pay_status = 'Successful';
        $customer_payment->store_amount = $customer_payment->amount_paid - $transaction_fee;
        $customer_payment->transaction_fee = $transaction_fee;
        $customer_payment->save();
        CustomersPaymentProcessController::store($customer_payment);
        return redirect()->route('customers.profile')->with('success', 'Payment Successful');
    }

    /**
     * Query Bkash Tokenized Checkout Payment for Customer
     *
     * @param \App\Models\customer_payment $customer_payment
     *
     * @return \Illuminate\Http\Response
     */
    public function queryPayment(customer_payment $customer_payment)
    {
        $payment_gateway = $this->getPaymentGateway($customer_payment->payment_gateway_id);

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
            'paymentID' => $customer_payment->pgw_payment_identifier,
        ];

        $response = Http::timeout(30)->withHeaders($header)->post($this->query_payment_url, $body);

        if (config('consumer.debug_payment')) {
            Storage::put('bkash_tokenized_debug/queryPayment.json', $response);
        }

        return $response;
    }

    /**
     * Search Bkash Tokenized Checkout Payment for Customer
     *
     * @param \App\Models\customer_payment $customer_payment
     *
     * @return \Illuminate\Http\Response
     */
    public function searchTransaction(customer_payment $customer_payment)
    {
        $payment_gateway = $this->getPaymentGateway($customer_payment->payment_gateway_id);

        $this->grantToken($payment_gateway);

        $credentials_file = $this->getCredentialsFileName($payment_gateway);

        $credentials = json_decode(file_get_contents($credentials_file), true);

        $response = Http::timeout(30)->withHeaders([
            'accept' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
        ])->post($this->search_transaction_url, [
            'trxID' => $customer_payment->pgw_txnid,
        ]);

        if (config('consumer.debug_payment')) {
            Storage::put('bkash_tokenized_debug/searchTransaction.json', $response);
        }

        return $response;
    }

    public function refundTransaction()
    {
    }

    public function refundStatus()
    {
    }

    /**
     * Recheck Customer Payment
     *
     * @param \App\Models\customer_payment $customer_payment
     *
     * @return \Illuminate\Http\Response
     */
    public function recheckPayment(customer_payment $customer_payment)
    {
        //Payment Initiate was Failed
        if (strlen($customer_payment->pgw_payment_identifier) == 0) {
            $customer_payment->delete();
            return 0;
        }

        $response = $this->queryPayment($customer_payment);

        if (config('consumer.debug_payment')) {
            Storage::put('bkash_tokenized_debug/recheckPayment.json', $response);
        }

        $reply = json_decode($response, true);

        if (array_key_exists('transactionStatus', $reply)) {
            if ($reply['transactionStatus'] === 'Completed') {
                //update Customer Payment
                $customer_payment->pgw_txnid = $reply['trxID'];
                $customer_payment->bank_txnid = $reply['trxID'];
                $customer_payment->pay_status = 'Successful';
                $customer_payment->store_amount = $reply['amount'];
                $customer_payment->transaction_fee = 0;
                $customer_payment->save();
                CustomersPaymentProcessController::store($customer_payment);
                return 1;
            } else {
                $customer_payment->delete();
                return 0;
            }
        }
    }
}
