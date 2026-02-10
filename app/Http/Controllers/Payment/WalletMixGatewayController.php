<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomerPayment\CustomersPaymentProcessController;
use App\Models\customer_payment;
use App\Models\payment_gateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class WalletMixGatewayController extends Controller
{

    /**
     * Server Details URL
     *
     * @var string
     */
    protected $server_details_url  = '?';


    /**
     * Payment Process URL
     *
     * @var string
     */
    protected $payment_process_url  = '?';


    /**
     * Check Payment URL
     *
     * @var string
     */
    protected $check_payment_url  = '?';


    public function __construct()

    {
        if (config('local.is_sandbox_pgw')) {
            $this->server_details_url = 'https://sandbox.walletmix.com/check-server';
            $this->payment_process_url = 'https://sandbox.walletmix.com/bank-payment-process/';
            $this->check_payment_url = 'https://sandbox.walletmix.com/check-payment';
        } else {
            $this->server_details_url = 'https://epay.walletmix.com/check-server';
            $this->payment_process_url = 'https://epay.walletmix.com/bank-payment-process/';
            $this->check_payment_url = 'https://epay.walletmix.com/check-payment';
        }
    }


    /**
     * Initiate Customer Payment through Nagad Payment Gateway
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

        $server_details = Http::get($this->server_details_url);

        // debug
        // Storage::put('walletmix/server_details' . $customer_payment->id . '.json', $server_details);

        $server_details = json_decode($server_details, true);

        if (array_key_exists('url', $server_details) == false) {
            abort(500);
        }

        $data = [
            'wmx_id' => $payment_gateway->msisdn,
            'merchant_order_id' => $customer_payment->id,
            'merchant_ref_id' => $customer_payment->mer_txnid,
            'app_name' => config('app.name'),
            'cart_info' => "Internet Bill",
            'customer_name' => $customer_payment->username,
            'customer_email' => $cus_email,
            'customer_add' => 'Dhaka,Bangladesh',
            'customer_phone' => '01751000000',
            'product_desc' => 'Internet Bill',
            'amount' => $customer_payment->amount_paid,
            'currency' => 'BDT',
            'options' => base64_encode('s=' . $_SERVER['HTTP_HOST'] . ',i=' . $_SERVER['SERVER_ADDR']),
            'callback_url' => route('walletmix.customer_payment.callback'),
            'access_app_key' => $payment_gateway->credentials_path,
            'authorization' => 'Basic ' . base64_encode($payment_gateway->username . ':' . $payment_gateway->password),
        ];

        // debug
        // Storage::put('walletmix/data' . $customer_payment->id . '.json', json_encode($data));

        $payment_request = Http::asForm()->post($server_details['url'], $data);

        // debug
        // Storage::put('walletmix/payment_request' . $customer_payment->id . '.json', $payment_request);

        $payment_response = json_decode($payment_request, true);

        if ($payment_response['statusCode'] == '1000') {

            $customer_payment->pgw_payment_identifier = $payment_response['token'];
            $customer_payment->save();

            return redirect()->away($this->payment_process_url . $payment_response['token']);
        } else {
            abort(500);
        }
    }


    /**
     * Process Success Customer Payment with Nagad Payment Gateway
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */

    public function customerPaymentCallback(Request $request)
    {

        $request->validate([
            'merchant_txn_data' => 'required',
        ]);

        $callback = $request->all();

        // debug
        // Storage::put('walletmix/callback' . '.json', json_encode($callback));

        $response = json_decode($callback['merchant_txn_data'], true);

        $customer_payment = customer_payment::findOrFail($response['merchant_order_id']);

        $payment_gateway = payment_gateway::findOrFail($customer_payment->payment_gateway_id);

        $check_request = Http::asForm()->post($this->check_payment_url, [
            'wmx_id' => $payment_gateway->msisdn,
            'access_app_key' => $payment_gateway->credentials_path,
            'authorization' => 'Basic ' . base64_encode($payment_gateway->username . ':' . $payment_gateway->password),
            'token' => $response['token'],
        ]);

        // debug
        // Storage::put('walletmix/check_request' . $customer_payment->id . '.json', $check_request);

        $check_response = json_decode($check_request, true);

        if ($check_response['txn_status'] == '1000') {
            $customer_payment->pay_status = 'Successful';
            $customer_payment->store_amount = $customer_payment->amount_paid - $check_response['wmx_charge_bdt'];
            $customer_payment->transaction_fee = $check_response['wmx_charge_bdt'];
            $customer_payment->bank_txnid = $check_response['txn_details'];
            $customer_payment->card_number = $check_response['payment_card'];
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
        //Payment Initiate was Failed
        if (strlen($customer_payment->pgw_payment_identifier) == 0) {
            $customer_payment->delete();
            return 0;
        }

        $payment_gateway = payment_gateway::findOrFail($customer_payment->payment_gateway_id);

        $response = Http::asForm()->post($this->check_payment_url, [
            'wmx_id' => $payment_gateway->msisdn,
            'access_app_key' => $payment_gateway->credentials_path,
            'authorization' => 'Basic ' . base64_encode($payment_gateway->username . ':' . $payment_gateway->password),
            'token' => $customer_payment->pgw_payment_identifier,
        ]);

        $reply = json_decode($response, true);

        if (config('consumer.debug_payment')) {
            Storage::put('walletmix/recheck' . $customer_payment->id . '.json', json_encode($reply));
        }

        if (array_key_exists('txn_status', $reply) == false) {
            $customer_payment->delete();
            return 0;
        }

        if ($reply['txn_status'] === '1000' && $customer_payment->pay_status !== 'Successful') {

            //update Customer Payment
            $customer_payment->pay_status = 'Successful';
            $customer_payment->store_amount = $customer_payment->amount_paid - $reply['wmx_charge_bdt'];
            $customer_payment->transaction_fee = $reply['wmx_charge_bdt'];
            $customer_payment->save();

            CustomersPaymentProcessController::store($customer_payment);

            return 1;
        } else {
            $customer_payment->delete();
            return 0;
        }
    }
}
