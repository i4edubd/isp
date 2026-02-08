<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomerPayment\CustomersPaymentProcessController;
use App\Models\customer_payment;
use App\Models\payment_gateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class AamarpayController extends Controller
{
    /**
     * Payment URL
     *
     * @var string
     */
    protected $payment_url = '';


    /**
     * Validation URL
     *
     * @var string
     */
    protected $validation_url = '';


    /**
     * Re Check URL
     *
     * @var string
     */
    protected $recheck_url = '';


    public function __construct()

    {
        if (config('local.is_sandbox_pgw')) {
            $this->payment_url = 'https://sandbox.aamarpay.com/request.php';
            $this->validation_url = '';
            $this->recheck_url = '';
        } else {
            $this->payment_url = 'https://secure.aamarpay.com/request.php';
            $this->validation_url = '';
            $this->recheck_url = '';
        }
    }

    /**
     * Initiate Customer Payment through Aamar Pay Payment Gateway
     *
     * @param \App\Models\customer_payment $customer_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function initiateCustomerPayment(customer_payment $customer_payment)
    {

        $payment_gateway = payment_gateway::findOrFail($customer_payment->payment_gateway_id);

        $email = config('mail.mailers.smtp.username');
        if (strlen($email)) {
            $cus_email = $email;
        } else {
            $cus_email = 'root@mydomain.com';
        }

        //use multi_card_name to control the gateway list at SSLCOMMERZ gateway selection page
        $response =  Http::asForm()->post($this->payment_url, [
            'store_id' => $payment_gateway->username,
            'signature_key' => $payment_gateway->password,
            'tran_id' => $customer_payment->mer_txnid,
            'success_url' => route('aamarpay.customer_payment.success'),
            'fail_url' => route('aamarpay.customer_payment.failed'),
            'cancel_url' => route('aamarpay.customer_payment.canceled'),
            'amount' => $customer_payment->amount_paid,
            'currency' => 'BDT',
            'desc' => 'Internet Topup',
            'cus_name' => $customer_payment->mobile,
            'cus_email' => $cus_email,
            'cus_add1' => 'Bangladesh',
            'cus_add2' => 'Bangladesh',
            'cus_city' => 'Dhaka',
            'cus_state' => 'Dhaka',
            'cus_country' => 'Bangladesh',
            'cus_postcode' => '1200',
            'cus_phone' => '+8801751000000',
        ]);

        $body = stripslashes($response->body());

        $url_forward = 'https://secure.aamarpay.com' . str_replace('"', '', $body);

        return redirect()->away($url_forward);
    }

    /**
     * Process Success Customer Payment with Aamar Pay Payment Gateway
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */

    public function successCustomerPayment(Request $request)
    {
        if ($request->pay_status === 'Successful') {

            $customer_payment = customer_payment::where('mer_txnid', $request->mer_txnid)->firstOrFail();

            $input = $request->all();

            if (config('consumer.debug_payment')) {
                Storage::put('customer_payment/' . $customer_payment->id . '.json', json_encode($input));
            }
            //update Customer Payment
            $customer_payment->pgw_txnid = $request->pg_txnid;
            $customer_payment->bank_txnid = $request->bank_txn;
            $customer_payment->pay_status = 'Successful';
            $customer_payment->store_amount = $request->store_amount;
            $customer_payment->transaction_fee = $customer_payment->amount_paid - $request->store_amount;
            $customer_payment->save();

            CustomersPaymentProcessController::store($customer_payment);

            return redirect()->route('customers.profile')->with('success', 'Package has been activated successfully');
        } else {
            abort(500);
        }
    }

    /**
     * Process Failed Customer Payment with Aamar Pay Payment Gateway
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function failCustomerPayment(Request $request)
    {
        $customer_payment = customer_payment::where('mer_txnid', $request->mer_txnid)->firstOrFail();

        // debug
        $input = $request->all();

        if (config('consumer.debug_payment')) {
            Storage::put('customer_payment/' . $customer_payment->id . '.json', json_encode($input));
        }

        if ($customer_payment->pay_status == 'Pending' && $request->pay_status == 'Failed') {
            $customer_payment->pay_status = 'Failed';
            $customer_payment->store_amount = 0;
            $customer_payment->transaction_fee = 0;
            $customer_payment->save();

            //Show Profile
            return redirect()->route('customers.home')->with('error', 'Payment Failed!');
        } else {
            //Show Profile
            return redirect()->route('customers.home')->with('error', 'Payment Failed!');
        }
    }

    /**
     * Process Canceled Customer Payment with Aamar Pay Payment Gateway
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function cancelCustomerPayment(Request $request)
    {
        $customer_payment = customer_payment::where('mer_txnid', $request->mer_txnid)->firstOrFail();

        // debug
        $input = $request->all();

        if (config('consumer.debug_payment')) {
            Storage::put('customer_payment/' . $customer_payment->id . '.json', json_encode($input));
        }

        if ($customer_payment->pay_status == 'Pending') {
            $customer_payment->pay_status = 'Failed';
            $customer_payment->store_amount = 0;
            $customer_payment->transaction_fee = 0;
            $customer_payment->save();
            //Show Profile
            return redirect()->route('customers.home')->with('error', 'Payment Failed!');
        } else {
            //Show Profile
            return redirect()->route('customers.home')->with('error', 'Payment Failed!');
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
        $customer_payment->delete();
        return 0;
    }
}
