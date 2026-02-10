<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\CacheController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomerPayment\CustomersPaymentProcessController;
use App\Http\Controllers\Sms\SmsBalanceHistoryController;
use App\Http\Controllers\SubscriptionPaymentController;
use App\Models\customer_payment;
use App\Models\package;
use App\Models\payment_gateway;
use App\Models\sms_bill;
use App\Models\sms_payment;
use App\Models\subscription_bill;
use App\Models\subscription_payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class BkashCheckoutController extends Controller
{

    /**
     * Grant Token Path
     *
     * @var string
     */
    public $grant_token_path  = '/checkout/token/grant';

    /**
     * Refresh Token Path
     *
     * @var string
     */
    public $refresh_token_path = '/checkout/token/refresh';

    /**
     * Create Payment Path
     *
     * @var string
     */
    public $create_payment_path = '/checkout/payment/create';

    /**
     * Execute Payment Path
     *
     * @var string
     */
    public $execute_payment_path = '/checkout/payment/execute/';

    /**
     * Query Payment Path
     *
     * @var string
     */
    public $query_payment_path = '/checkout/payment/query/';

    /**
     * Search Payment Path
     *
     * @var string
     */
    public $search_transaction_path = '/checkout/payment/search/';

    /**
     * Bkash Script URL
     *
     * @var string
     */
    public $bkash_script_url = '';

    /**
     * Base URL
     *
     * @var string
     */
    public $base_URL = '';

    /**
     * Grant Token URL
     *
     * @var string
     */
    public $grant_token_url = '';

    /**
     * Refresh Token URL
     *
     * @var string
     */
    public $refresh_token_url = '';

    /**
     * Create Payment URL
     *
     * @var string
     */

    public $create_payment_url = '';

    /**
     * Execute Payment URL
     *
     * @var string
     */
    public $execute_payment_url = '';

    /**
     * Query Payment URL
     *
     * @var string
     */
    public $query_payment_url = '';

    /**
     * Search Payment URL
     *
     * @var string
     */
    public $search_transaction_url = '';

    /**
     * Log Response
     *
     * @var bool
     */
    public $log_response = false;

    public function __construct()

    {
        if (config('local.is_sandbox_pgw')) {
            $this->base_URL = 'https://checkout.sandbox.bka.sh/v1.2.0-beta';
            $this->bkash_script_url = 'https://scripts.sandbox.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout-sandbox.js';
            $this->log_response = true;
        } else {
            $this->base_URL = 'https://checkout.pay.bka.sh/v1.2.0-beta';
            $this->bkash_script_url = 'https://scripts.pay.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout.js';
            $this->log_response = false;
        }

        $this->grant_token_url = $this->base_URL . $this->grant_token_path;

        $this->refresh_token_url = $this->base_URL . $this->refresh_token_path;

        $this->create_payment_url = $this->base_URL . $this->create_payment_path;

        $this->execute_payment_url =  $this->base_URL . $this->execute_payment_path;

        $this->query_payment_url =  $this->base_URL . $this->query_payment_path;

        $this->search_transaction_url = $this->base_URL . $this->search_transaction_path;
    }

    /**
     * Generate Bkash Payment Token
     *
     * @return string
     */
    public function token()
    {
        $bytes = random_bytes(4);
        return bin2hex($bytes);
    }

    /**
     * Get Credentials File Name
     *
     * @param \App\Models\customer_payment $customer_payment
     * @return string
     */
    public function getCredentialsFileName(payment_gateway $payment_gateway)
    {
        $credentials_path = storage_path('payment-gateways/');
        if (file_exists($credentials_path) == false) {
            mkdir($credentials_path);
        }
        $file_name = $payment_gateway->id . $payment_gateway->provider_name . $payment_gateway->operator_id . '.json';
        $credentials_file = $credentials_path . $file_name;
        return $credentials_file;
    }

    /**
     * Get Token for Bkash Checkout
     *
     * @param \App\Models\customer_payment $customer_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function  grantToken(payment_gateway $payment_gateway)
    {
        // function variable
        $credentials = [];

        $credentials_file = $this->getCredentialsFileName($payment_gateway);

        if (file_exists($credentials_file)) {
            $credentials = json_decode(file_get_contents($credentials_file), true);
            if (array_key_exists('expire_at', $credentials)) {
                $now = Carbon::now(config('app.timezone'));
                $expire_at = Carbon::create($credentials['expire_at']);
                if ($expire_at->greaterThan($now)) {
                    return 0;
                }
            }
        }

        $response = Http::timeout(30)->withHeaders([
            'accept' => 'application/json',
            'username' => $payment_gateway->username,
            'password' => $payment_gateway->password,
            'content-type' => 'application/json',
        ])->post($this->grant_token_url, [
            'app_key' => $payment_gateway->app_key,
            'app_secret' => $payment_gateway->app_secret,
        ]);

        if (config('consumer.debug_payment')) {
            Storage::put('bkash_debug/grantToken.json', $response);
        }

        $tokens = json_decode($response, true);

        if (array_key_exists('id_token', $tokens) == false) {
            abort(500, json_encode($tokens));
        }

        $idtoken = $tokens['id_token'];

        $credentials['token'] = $idtoken;

        $now = Carbon::now(config('app.timezone'));
        $expire_at = $now->addSeconds(1800);
        $credentials['expire_at'] = $expire_at;

        $credentials['username'] = $payment_gateway->username;
        $credentials['password'] = $payment_gateway->password;
        $credentials['app_key'] = $payment_gateway->app_key;
        $credentials['app_secret'] = $payment_gateway->app_secret;

        $newcredentials = json_encode($credentials);

        file_put_contents($credentials_file, $newcredentials);

        return 1;
    }

    /**
     * Initiate Bkash Checkout Payment for Customer
     *
     * @param \App\Models\customer_payment $customer_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function initiateCustomerPayment(Request $request, customer_payment $customer_payment)
    {
        if ($customer_payment->pay_status !== 'Pending') {
            abort(500, 'Invalid Request!');
        }

        //operator
        $all_customer = $request->user('customer');
        $operator = CacheController::getOperator($all_customer->operator_id);

        //get token
        $payment_gateway = payment_gateway::findOrFail($customer_payment->payment_gateway_id);
        $this->grantToken($payment_gateway);

        //package
        $package = package::find($customer_payment->package_id);

        //payment_token
        $payment_token = $this->token();
        $customer_payment->payment_token = $payment_token;
        $customer_payment->save();

        return view('customers.checkout-customers-payment', [
            'operator' => $operator,
            'customer_payment' => $customer_payment,
            'bkash_script_url' => $this->bkash_script_url,
            'package' => $package,
            'payment_token' => $payment_token,
        ]);
    }

    /**
     * Check Bkash Checkout Payment for Customer
     *
     * @param \App\Models\customer_payment $customer_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function createCustomerPayment(Request $request, customer_payment $customer_payment)
    {
        # <<Payment Token Validation
        $request->validate([
            'token' => 'required|string',
        ]);

        if ($request->token !== $customer_payment->payment_token) {
            abort(500, 'Invalid Token');
        }
        # Payment Token Validation>>

        //Reset mer_txnid
        $mer_txnid = random_int(1000, 9999) . Carbon::now(config('app.timezone'))->timestamp;
        $customer_payment->mer_txnid = $mer_txnid;
        $customer_payment->save();

        $payment_gateway = payment_gateway::find($customer_payment->payment_gateway_id);

        $credentials_file = $this->getCredentialsFileName($payment_gateway);

        $credentials = json_decode(file_get_contents($credentials_file), true);

        $response =  Http::timeout(30)->withHeaders([
            'accept' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
            'content-type' => 'application/json',
        ])->post($this->create_payment_url, [
            'amount' => $customer_payment->amount_paid,
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => $customer_payment->mer_txnid,
        ]);

        if (config('consumer.debug_payment')) {
            Storage::put('bkash_debug/createPayment.json', $response);
        }

        $data = json_decode($response, true);

        if (array_key_exists('paymentID', $data)) {
            $customer_payment->pgw_payment_identifier = $data['paymentID'];
            $customer_payment->save();
        }

        //return response
        return $response;
    }

    /**
     * Execute Bkash Checkout Payment for Customer
     *
     * @param \App\Models\customer_payment $customer_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function executeCustomerPayment(Request $request, customer_payment $customer_payment)
    {
        # <<Payment Token Validation
        $request->validate([
            'token' => 'required|string',
        ]);

        if ($request->token !== $customer_payment->payment_token) {
            abort(500, 'Invalid Token');
        }
        # Payment Token Validation>>

        $payment_gateway = payment_gateway::find($customer_payment->payment_gateway_id);

        $credentials_file = $this->getCredentialsFileName($payment_gateway);

        $credentials = json_decode(file_get_contents($credentials_file), true);

        $response =  Http::timeout(30)->withHeaders([
            'accept' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
        ])->post($this->execute_payment_url . $customer_payment->pgw_payment_identifier);

        if (config('consumer.debug_payment')) {
            Storage::put('bkash_debug/executePayment.json', $response);
        }

        //If No Response From execute Query
        if ($response->failed()) {

            $response = $this->queryCustomerPayment($customer_payment);

            $data = json_decode($response, true);

            if ($data['transactionStatus'] == 'Completed') {
                $transaction_fee = $customer_payment->amount_paid * ($payment_gateway->service_charge_percentage / 100);
                $customer_payment->pgw_txnid = $data['trxID'];
                $customer_payment->bank_txnid = $data['trxID'];
                $customer_payment->pay_status = 'Successful';
                $customer_payment->store_amount = $customer_payment->amount_paid - $transaction_fee;
                $customer_payment->transaction_fee = $transaction_fee;
                $customer_payment->save();
            }
        }

        //If Success
        $data = json_decode($response, true);

        if (array_key_exists('paymentID', $data)) {
            $transaction_fee = $customer_payment->amount_paid * ($payment_gateway->service_charge_percentage / 100);
            $customer_payment->pgw_txnid = $data['trxID'];
            $customer_payment->bank_txnid = $data['trxID'];
            $customer_payment->pay_status = 'Successful';
            $customer_payment->store_amount = $customer_payment->amount_paid - $transaction_fee;
            $customer_payment->transaction_fee = $transaction_fee;
            $customer_payment->save();
        }

        // return response
        return $response;
    }

    /**
     * Success Bkash Checkout Payment for Customer
     *
     * @param \App\Models\customer_payment $customer_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function successCustomerPayment(customer_payment $customer_payment)
    {
        if ($customer_payment->pay_status == 'Successful' && $customer_payment->used == 0) {

            CustomersPaymentProcessController::store($customer_payment);

            //Show Profile
            return redirect()->route('customers.profile')->with('success', 'Package has been activated successfully');
        } else {
            abort(500, 'Invalid Payment');
        }
    }

    /**
     * Query Bkash Checkout Payment for Customer
     *
     * @param \App\Models\customer_payment $customer_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function queryCustomerPayment(customer_payment $customer_payment)
    {
        $payment_gateway = payment_gateway::find($customer_payment->payment_gateway_id);

        $credentials_file = $this->getCredentialsFileName($payment_gateway);

        $credentials = json_decode(file_get_contents($credentials_file), true);

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
        ])->get($this->query_payment_url . $customer_payment->pgw_payment_identifier);

        return $response;
    }

    /**
     * Search Bkash Checkout Payment for Customer
     *
     * @param \App\Models\customer_payment $customer_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function searchCustomerTransaction(customer_payment $customer_payment)
    {
        $payment_gateway = payment_gateway::find($customer_payment->payment_gateway_id);

        $credentials_file = $this->getCredentialsFileName($payment_gateway);

        $credentials = json_decode(file_get_contents($credentials_file), true);

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
        ])->get($this->search_transaction_url . $customer_payment->pgw_txnid);

        return $response;
    }

    /**
     * Initiate Bkash Checkout Payment for SMS
     *
     * @param \App\Models\sms_payment $sms_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function initiateSmsPayment(sms_payment $sms_payment)
    {
        if ($sms_payment->pay_status !== 'Pending') {
            abort(500, 'Invalid Request!');
        }

        //get token
        $payment_gateway = payment_gateway::findOrFail($sms_payment->payment_gateway_id);
        $this->grantToken($payment_gateway);

        return view('admins.components.bkash-sms-payment', [
            'sms_payment' => $sms_payment,
            'bkash_script_url' => $this->bkash_script_url,
        ]);
    }

    /**
     * Check Bkash Checkout Payment for SMS
     *
     * @param \App\Models\sms_payment $sms_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function createSmsPayment(sms_payment $sms_payment)
    {
        $payment_gateway = payment_gateway::find($sms_payment->payment_gateway_id);

        $credentials_file = $this->getCredentialsFileName($payment_gateway);

        $credentials = json_decode(file_get_contents($credentials_file), true);

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
            'content-type' => 'application/json',
        ])->post($this->create_payment_url, [
            'amount' => $sms_payment->amount_paid,
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => $sms_payment->mer_txnid,
        ]);

        $data = json_decode($response, true);
        if (array_key_exists('paymentID', $data)) {
            $sms_payment->pgw_payment_identifier = $data['paymentID'];
            $sms_payment->save();
        }

        echo $response;
    }

    /**
     * Execute Bkash Checkout Payment for SMS
     *
     * @param \App\Models\sms_payment $sms_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function executeSmsPayment(sms_payment $sms_payment)
    {
        $payment_gateway = payment_gateway::find($sms_payment->payment_gateway_id);

        $credentials_file = $this->getCredentialsFileName($payment_gateway);

        $credentials = json_decode(file_get_contents($credentials_file), true);

        $response =  Http::withHeaders([
            'accept' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
        ])->post($this->execute_payment_url . $sms_payment->pgw_payment_identifier);

        //update payment
        $data = json_decode($response, true);
        if (array_key_exists('paymentID', $data)) {
            $transaction_fee = $sms_payment->amount_paid * ($payment_gateway->service_charge_percentage / 100);
            $sms_payment->pgw_txnid = $data['trxID'];
            $sms_payment->bank_txnid = $data['trxID'];
            $sms_payment->pay_status = 'Successful';
            $sms_payment->store_amount = $sms_payment->amount_paid - $transaction_fee;
            $sms_payment->transaction_fee = $transaction_fee;
            $sms_payment->save();
        }

        echo $response;
    }

    /**
     * Query Bkash Checkout Payment for SMS Payment
     *
     * @param \App\Models\sms_payment $sms_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function querySmsPayment(sms_payment $sms_payment)
    {
        $payment_gateway = payment_gateway::find($sms_payment->payment_gateway_id);

        $credentials_file = $this->getCredentialsFileName($payment_gateway);

        $credentials = json_decode(file_get_contents($credentials_file), true);

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
        ])->get($this->query_payment_url . $sms_payment->pgw_payment_identifier);

        return $response;
    }

    /**
     * Search Bkash Checkout Payment for SMS
     *
     * @param \App\Models\sms_payment $sms_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function searchSmsTransaction(sms_payment $sms_payment)
    {
        $payment_gateway = payment_gateway::find($sms_payment->payment_gateway_id);

        $credentials_file = $this->getCredentialsFileName($payment_gateway);

        $credentials = json_decode(file_get_contents($credentials_file), true);

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
        ])->get($this->search_transaction_url . $sms_payment->pgw_txnid);

        return $response;
    }

    /**
     * Success Bkash Checkout Payment for SMS
     *
     * @param \App\Models\sms_payment $sms_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function successSmsPayment(sms_payment $sms_payment)
    {
        if ($sms_payment->pay_status == 'Successful') {

            SmsBalanceHistoryController::store($sms_payment);

            $sms_bill = sms_bill::find($sms_payment->sms_bill_id);

            if ($sms_bill) {
                $sms_bill->delete();
            }

            //Show SMS Payments
            return redirect()->route('sms_payments.index')->with('success', 'Payment Successful');
        } else {
            abort(500, 'Invalid Payment');
        }
    }

    /**
     * Initiate Bkash Checkout Payment for Subscription
     *
     * @param \App\Models\subscription_payment $subscription_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function initiateSubscriptionPayment(subscription_payment $subscription_payment)
    {
        if ($subscription_payment->pay_status !== 'Pending') {
            abort(500, 'Invalid Request!');
        }

        //get token
        $payment_gateway = payment_gateway::findOrFail($subscription_payment->payment_gateway_id);

        $this->grantToken($payment_gateway);

        return view('admins.components.bkash-subscription-payment', [
            'subscription_payment' => $subscription_payment,
            'bkash_script_url' => $this->bkash_script_url,
        ]);
    }

    /**
     * Check Bkash Checkout Payment for subscription_payment
     *
     * @param \App\Models\subscription_payment $subscription_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function createSubscriptionPayment(subscription_payment $subscription_payment)
    {
        $payment_gateway = payment_gateway::find($subscription_payment->payment_gateway_id);

        $credentials_file = $this->getCredentialsFileName($payment_gateway);

        $credentials = json_decode(file_get_contents($credentials_file), true);

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
            'content-type' => 'application/json',
        ])->post($this->create_payment_url, [
            'amount' => $subscription_payment->amount_paid,
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => $subscription_payment->mer_txnid,
        ]);

        $data = json_decode($response, true);

        if (array_key_exists('paymentID', $data)) {
            $subscription_payment->pgw_payment_identifier = $data['paymentID'];
            $subscription_payment->save();
        }

        echo $response;
    }

    /**
     * Execute Bkash Checkout Payment for subscription_payment
     *
     * @param \App\Models\subscription_payment $subscription_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function executeSubscriptionPayment(subscription_payment $subscription_payment)
    {
        $payment_gateway = payment_gateway::find($subscription_payment->payment_gateway_id);

        $credentials_file = $this->getCredentialsFileName($payment_gateway);

        $credentials = json_decode(file_get_contents($credentials_file), true);

        $response =  Http::withHeaders([
            'accept' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
        ])->post($this->execute_payment_url . $subscription_payment->pgw_payment_identifier);

        //update payment
        $data = json_decode($response, true);
        if (array_key_exists('paymentID', $data)) {
            $transaction_fee = $subscription_payment->amount_paid * ($payment_gateway->service_charge_percentage / 100);
            $subscription_payment->pgw_txnid = $data['trxID'];
            $subscription_payment->bank_txnid = $data['trxID'];
            $subscription_payment->pay_status = 'Successful';
            $subscription_payment->store_amount = $subscription_payment->amount_paid - $transaction_fee;
            $subscription_payment->transaction_fee = $transaction_fee;
            $subscription_payment->save();
        }

        echo $response;
    }

    /**
     * Query Bkash Checkout Payment for subscription_payment
     *
     * @param \App\Models\subscription_payment $subscription_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function querySubscriptionPayment(subscription_payment $subscription_payment)
    {
        $payment_gateway = payment_gateway::find($subscription_payment->payment_gateway_id);

        $credentials_file = $this->getCredentialsFileName($payment_gateway);

        $credentials = json_decode(file_get_contents($credentials_file), true);

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
        ])->get($this->query_payment_url . $subscription_payment->pgw_payment_identifier);

        return $response;
    }

    /**
     * Search Bkash Checkout Payment for subscription_payment
     *
     * @param \App\Models\subscription_payment $subscription_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function searchSubscriptionTransaction(subscription_payment $subscription_payment)
    {
        $payment_gateway = payment_gateway::find($subscription_payment->payment_gateway_id);

        $credentials_file = $this->getCredentialsFileName($payment_gateway);

        $credentials = json_decode(file_get_contents($credentials_file), true);

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
        ])->get($this->search_transaction_url . $subscription_payment->pgw_txnid);

        return $response;
    }

    /**
     * Success Bkash Checkout Payment for subscription_payment
     *
     * @param \App\Models\subscription_payment $subscription_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function successSubscriptionPayment(subscription_payment $subscription_payment)
    {
        if ($subscription_payment->pay_status == 'Successful') {

            //Delete subscription_bill Bill
            $subscription_bill = subscription_bill::find($subscription_payment->subscription_bill_id);
            $subscription_bill->delete();

            //record incomes
            SubscriptionPaymentController::recordIncomes($subscription_payment);

            //Show subscription Payments
            return redirect()->route('subscription_payments.index')->with('success', 'Payment Successfull');
        } else {
            abort(500, 'Invalid Payment');
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

        $payment_gateway = payment_gateway::findOrFail($customer_payment->payment_gateway_id);

        $this->grantToken($payment_gateway);

        $credentials_file = $this->getCredentialsFileName($payment_gateway);

        $credentials = json_decode(file_get_contents($credentials_file), true);

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
        ])->get($this->query_payment_url . $customer_payment->pgw_payment_identifier);

        $reply = json_decode($response, true);

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

        $payment_gateway = payment_gateway::findOrFail($sms_payment->payment_gateway_id);

        $this->grantToken($payment_gateway);

        $credentials_file = $this->getCredentialsFileName($payment_gateway);

        $credentials = json_decode(file_get_contents($credentials_file), true);

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
        ])->get($this->query_payment_url . $sms_payment->pgw_payment_identifier);

        $reply = json_decode($response, true);

        if ($reply['transactionStatus'] === 'Completed') {

            //update Customer Payment
            $sms_payment->pgw_txnid = $reply['trxID'];
            $sms_payment->bank_txnid = $reply['trxID'];
            $sms_payment->pay_status = 'Successful';
            $sms_payment->store_amount = $reply['amount'];
            $sms_payment->transaction_fee = 0;
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

        $payment_gateway = payment_gateway::findOrFail($subscription_payment->payment_gateway_id);

        $this->grantToken($payment_gateway);

        $credentials_file = $this->getCredentialsFileName($payment_gateway);

        $credentials = json_decode(file_get_contents($credentials_file), true);

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'authorization' => $credentials['token'],
            'x-app-key' => $credentials['app_key'],
        ])->get($this->query_payment_url . $subscription_payment->pgw_payment_identifier);

        $reply = json_decode($response, true);

        if ($reply['transactionStatus'] === 'Completed') {

            //update Customer Payment
            $subscription_payment->pgw_txnid = $reply['trxID'];
            $subscription_payment->bank_txnid = $reply['trxID'];
            $subscription_payment->pay_status = 'Successful';
            $subscription_payment->store_amount = $reply['amount'];
            $subscription_payment->transaction_fee = 0;
            $subscription_payment->save();

            //Delete subscription_bill Bill
            $subscription_bill = subscription_bill::find($subscription_payment->subscription_bill_id);
            $subscription_bill->delete();

            //record incomes
            SubscriptionPaymentController::recordIncomes($subscription_payment);
            return 1;
        } else {
            $subscription_payment->delete();
            return 0;
        }
    }
}
