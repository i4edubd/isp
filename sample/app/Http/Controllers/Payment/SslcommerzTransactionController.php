<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomerPayment\CustomersPaymentProcessController;
use App\Http\Controllers\Sms\SmsBalanceHistoryController;
use App\Http\Controllers\SubscriptionPaymentController;
use App\Models\customer_payment;
use App\Models\operator;
use App\Models\payment_gateway;
use App\Models\sms_bill;
use App\Models\sms_payment;
use App\Models\subscription_bill;
use App\Models\subscription_payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SslcommerzTransactionController extends Controller
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
            $this->payment_url = 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php';
            $this->validation_url = 'https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php?';
            $this->recheck_url = 'https://sandbox.sslcommerz.com/validator/api/merchantTransIDvalidationAPI.php?';
        } else {
            $this->payment_url = 'https://securepay.sslcommerz.com/gwprocess/v4/api.php';
            $this->validation_url = 'https://securepay.sslcommerz.com/validator/api/validationserverAPI.php?';
            $this->recheck_url = 'https://securepay.sslcommerz.com/validator/api/merchantTransIDvalidationAPI.php?';
        }
    }

    /**
     * Initiate Customer Payment through SSL Commerz Payment Gateway
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
            'store_passwd' => $payment_gateway->password,
            'total_amount' => $customer_payment->amount_paid,
            'currency' => 'BDT',
            'tran_id' => $customer_payment->mer_txnid,
            'product_category' => 'Internet Topup',
            'success_url' => route('sslcommerz.customer_payment.success'),
            'fail_url' => route('sslcommerz.customer_payment.failed'),
            'cancel_url' => route('sslcommerz.customer_payment.canceled'),
            'emi_option' => 0,
            'cus_name' => $customer_payment->mobile,
            'cus_email' => $cus_email,
            'cus_add1' => 'Bangladesh',
            'cus_city' => 'Bangladesh',
            'cus_postcode' => '1200',
            'cus_country' => 'Bangladesh',
            'cus_phone' => '01751000000',
            'shipping_method' => 'NO',
            'product_name' => 'Internet Package',
            'product_profile' => 'non-physical-goods',
        ]);

        $reply = json_decode($response, true);

        if ($reply['status'] === 'SUCCESS') {
            $customer_payment->pgw_payment_identifier = $reply['sessionkey'];
            $customer_payment->save();
            return redirect()->away($reply['GatewayPageURL']);
        } else {
            abort(500, $reply['failedreason']);
        }
    }

    /**
     * Process Success Customer Payment with SSL Commerz Payment Gateway
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */

    public function successCustomerPayment(Request $request)
    {
        if ($request->status === 'VALID') {

            $customer_payment = customer_payment::where('mer_txnid', $request->tran_id)->firstOrFail();

            $payment_gateway = payment_gateway::findOrFail($customer_payment->payment_gateway_id);

            $response = Http::retry(3, 100)->asForm()->get($this->validation_url, [
                'val_id' => $request->val_id,
                'store_id' => $payment_gateway->username,
                'store_passwd' => $payment_gateway->password,
                'format' => 'json',
            ]);

            // debug
            $input = $request->all();

            if (config('consumer.debug_payment')) {
                Storage::put('customer_payment/' . $customer_payment->id . '.json', json_encode($input));
            }

            $reply = json_decode($response, true);

            if ($reply['status'] === 'VALID' || $reply['status'] === 'VALIDATED') {

                //update Customer Payment
                $customer_payment->pgw_txnid = $reply['val_id'];
                $customer_payment->bank_txnid = $reply['bank_tran_id'];
                $customer_payment->pay_status = 'Successful';
                $customer_payment->store_amount = $reply['store_amount'];
                $customer_payment->transaction_fee = $customer_payment->amount_paid - $reply['store_amount'];
                $customer_payment->save();

                CustomersPaymentProcessController::store($customer_payment);

                return redirect()->route('customers.home')->with('success', 'Package has been activated successfully');
            } else {
                abort(500, 'Payment Validation Failed!');
            }
        } else {
            abort(500);
        }
    }

    /**
     * Process Failed Customer Payment with SSL Commerz Payment Gateway
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function failCustomerPayment(Request $request)
    {
        $customer_payment = customer_payment::where('mer_txnid', $request->tran_id)->firstOrFail();

        // debug
        $input = $request->all();

        if (config('consumer.debug_payment')) {
            Storage::put('customer_payment/' . $customer_payment->id . '.json', json_encode($input));
        }

        if ($customer_payment->pay_status == 'Pending' && $request->status == 'FAILED') {
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
     * Process Canceled Customer Payment with SSL Commerz Payment Gateway
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function cancelCustomerPayment(Request $request)
    {
        $customer_payment = customer_payment::where('mer_txnid', $request->tran_id)->firstOrFail();

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
     * Initiate SMS Payment through SSL Commerz Payment Gateway
     *
     * @param \App\Models\sms_payment $sms_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function initiateSmsPayment(sms_payment $sms_payment)
    {

        $payment_gateway = payment_gateway::findOrFail($sms_payment->payment_gateway_id);

        $operator = operator::find($sms_payment->operator_id);

        $response =  Http::asForm()->post($this->payment_url, [
            'store_id' => $payment_gateway->username,
            'store_passwd' => $payment_gateway->password,
            'total_amount' => $sms_payment->amount_paid,
            'currency' => 'BDT',
            'tran_id' => $sms_payment->mer_txnid,
            'success_url' => route('sslcommerz.sms_payment.success'),
            'fail_url' => route('sslcommerz.sms_payment.failed'),
            'cancel_url' => route('sslcommerz.sms_payment.canceled'),
            'emi_option' => 0,
            'cus_name' => $operator->company,
            'cus_email' => $operator->email,
            'cus_add1' => 'Bangladesh',
            'cus_city' => 'Bangladesh',
            'cus_postcode' => '1200',
            'cus_country' => 'Bangladesh',
            'cus_phone' => '01751000000',
            'shipping_method' => 'NO',
            'product_name' => 'SMS Package',
            'product_category' => 'SMS Topup',
            'product_profile' => 'non-physical-goods',
        ]);

        $reply = json_decode($response, true);

        if ($reply['status'] === 'SUCCESS') {
            $sms_payment->pgw_payment_identifier = $reply['sessionkey'];
            $sms_payment->save();
            return redirect()->away($reply['GatewayPageURL']);
        } else {
            abort(500);
        }
    }



    /**
     * Process Success Customer Payment with SSL Commerz Payment Gateway
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */

    public function successSmsPayment(Request $request)
    {
        if ($request->status === 'VALID') {

            $sms_payment = sms_payment::where('mer_txnid', $request->tran_id)->firstOrFail();

            Auth::loginUsingId($sms_payment->operator_id);

            $payment_gateway = payment_gateway::findOrFail($sms_payment->payment_gateway_id);

            $response = Http::retry(3, 100)->asForm()->get($this->validation_url, [
                'val_id' => $request->val_id,
                'store_id' => $payment_gateway->username,
                'store_passwd' => $payment_gateway->password,
                'format' => 'json',
            ]);

            // debug
            $input = $request->all();

            if (config('consumer.debug_payment')) {
                Storage::put('sms_payment/' . $sms_payment->id . '.json', json_encode($input));
            }

            $reply = json_decode($response, true);

            if ($reply['status'] === 'VALID' || $reply['status'] === 'VALIDATED') {

                //update Customer Payment
                $sms_payment->pgw_txnid = $reply['val_id'];
                $sms_payment->bank_txnid = $reply['bank_tran_id'];
                $sms_payment->pay_status = 'Successful';
                $sms_payment->store_amount = $reply['store_amount'];
                $sms_payment->transaction_fee = $sms_payment->amount_paid - $reply['store_amount'];
                $sms_payment->save();

                //Add balance || Delete SMS Bill
                if ($sms_payment->pay_for == 'balance') {
                    SmsBalanceHistoryController::store($sms_payment);
                } else {
                    $sms_bill = sms_bill::find($sms_payment->sms_bill_id);
                    $sms_bill->delete();
                }

                //Show SMS Payments
                return redirect()->route('sms_payments.index')->with('success', 'Payment Successful');
            } else {
                $sms_payment->pay_status = 'Failed';
                $sms_payment->store_amount = 0;
                $sms_payment->transaction_fee = 0;
                $sms_payment->save();
                return redirect()->route('sms_bills.index')->with('error', 'Payment Failed!');
            }
        } else {
            abort(500);
        }
    }


    /**
     * Process Failed Customer Payment with SSL Commerz Payment Gateway
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function failSmsPayment(Request $request)
    {
        $sms_payment = sms_payment::where('mer_txnid', $request->tran_id)->firstOrFail();

        // debug
        $input = $request->all();

        if (config('consumer.debug_payment')) {
            Storage::put('sms_payment/' . $sms_payment->id . '.json', json_encode($input));
        }

        Auth::loginUsingId($sms_payment->operator_id);

        if ($sms_payment->pay_status == 'Pending' && $request->status == 'FAILED') {
            $sms_payment->pay_status = 'Failed';
            $sms_payment->store_amount = 0;
            $sms_payment->transaction_fee = 0;
            $sms_payment->save();
            //Show Bill
            return redirect()->route('sms_bills.index')->with('error', 'Payment Failed!');
        } else {
            //Show Bill
            return redirect()->route('sms_bills.index')->with('error', 'Payment Failed!');
        }
    }



    /**
     * Process Canceled SMS Payment with SSL Commerz Payment Gateway
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function cancelSmsPayment(Request $request)
    {
        $sms_payment = sms_payment::where('mer_txnid', $request->tran_id)->firstOrFail();

        // debug
        $input = $request->all();

        if (config('consumer.debug_payment')) {
            Storage::put('sms_payment/' . $sms_payment->id . '.json', json_encode($input));
        }

        Auth::loginUsingId($sms_payment->operator_id);

        if ($sms_payment->pay_status == 'Pending') {
            $sms_payment->pay_status = 'Failed';
            $sms_payment->store_amount = 0;
            $sms_payment->transaction_fee = 0;
            $sms_payment->save();
            //Show Bill
            return redirect()->route('sms_bills.index')->with('error', 'Payment Failed!');
        } else {
            //Show Bill
            return redirect()->route('sms_bills.index')->with('error', 'Payment Failed!');
        }
    }


    /**
     * Initiate Subscription Payment through SSL Commerz Payment Gateway
     *
     * @param \App\Models\subscription_payment $subscription_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function initiateSubscriptionPayment(subscription_payment $subscription_payment)
    {

        $payment_gateway = payment_gateway::findOrFail($subscription_payment->payment_gateway_id);

        $operator = operator::find($subscription_payment->mgid);

        $response =  Http::asForm()->post($this->payment_url, [
            'store_id' => $payment_gateway->username,
            'store_passwd' => $payment_gateway->password,
            'total_amount' => $subscription_payment->amount_paid,
            'currency' => 'BDT',
            'tran_id' => $subscription_payment->mer_txnid,
            'success_url' => route('sslcommerz.subscription_payment.success'),
            'fail_url' => route('sslcommerz.subscription_payment.failed'),
            'cancel_url' => route('sslcommerz.subscription_payment.canceled'),
            'emi_option' => 0,
            'cus_name' => $operator->company,
            'cus_email' => $operator->email,
            'cus_add1' => 'Bangladesh',
            'cus_city' => 'Bangladesh',
            'cus_postcode' => '1200',
            'cus_country' => 'Bangladesh',
            'cus_phone' => '01751000000',
            'shipping_method' => 'NO',
            'product_name' => 'Subscription Payment',
            'product_category' => 'Subscription',
            'product_profile' => 'non-physical-goods',
        ]);

        $reply = json_decode($response, true);

        if ($reply['status'] === 'SUCCESS') {
            $subscription_payment->pgw_payment_identifier = $reply['sessionkey'];
            $subscription_payment->save();
            return redirect()->away($reply['GatewayPageURL']);
        } else {
            abort(500);
        }
    }



    /**
     * Process Success subscription Payment with SSL Commerz Payment Gateway
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */

    public function successSubscriptionPayment(Request $request)
    {
        if ($request->status === 'VALID') {

            $subscription_payment = subscription_payment::where('mer_txnid', $request->tran_id)->firstOrFail();

            Auth::loginUsingId($subscription_payment->mgid);

            $payment_gateway = payment_gateway::findOrFail($subscription_payment->payment_gateway_id);

            $response = Http::retry(3, 100)->asForm()->get($this->validation_url, [
                'val_id' => $request->val_id,
                'store_id' => $payment_gateway->username,
                'store_passwd' => $payment_gateway->password,
                'format' => 'json',
            ]);

            // debug
            $input = $request->all();

            if (config('consumer.debug_payment')) {
                Storage::put('subscription_payment/' . $subscription_payment->id . '.json', json_encode($input));
            }

            $reply = json_decode($response, true);

            if ($reply['status'] === 'VALID' || $reply['status'] === 'VALIDATED') {

                //update subscription Payment
                $subscription_payment->pgw_txnid = $reply['val_id'];
                $subscription_payment->bank_txnid = $reply['bank_tran_id'];
                $subscription_payment->pay_status = 'Successful';
                $subscription_payment->store_amount = $reply['store_amount'];
                $subscription_payment->transaction_fee = $subscription_payment->amount_paid - $reply['store_amount'];
                $subscription_payment->save();

                //Delete subscription Bill
                $subscription_bill = subscription_bill::find($subscription_payment->subscription_bill_id);
                $subscription_bill->delete();

                //record incomes
                SubscriptionPaymentController::recordIncomes($subscription_payment);

                //Show  Payments
                return redirect()->route('subscription_payments.index')->with('success', 'Payment Successful');
            } else {
                $subscription_payment->pay_status = 'Failed';
                $subscription_payment->store_amount = 0;
                $subscription_payment->transaction_fee = 0;
                $subscription_payment->save();
                return redirect()->route('subscription_bills.index')->with('error', 'Payment Failed!');
            }
        } else {
            abort(500);
        }
    }


    /**
     * Process Failed Customer Payment with SSL Commerz Payment Gateway
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function failSubscriptionPayment(Request $request)
    {
        $subscription_payment = subscription_payment::where('mer_txnid', $request->tran_id)->firstOrFail();

        // debug
        $input = $request->all();

        if (config('consumer.debug_payment')) {
            Storage::put('subscription_payment/' . $subscription_payment->id . '.json', json_encode($input));
        }

        Auth::loginUsingId($subscription_payment->mgid);

        if ($subscription_payment->pay_status == 'Pending' && $request->status == 'FAILED') {
            $subscription_payment->pay_status = 'Failed';
            $subscription_payment->store_amount = 0;
            $subscription_payment->transaction_fee = 0;
            $subscription_payment->save();
            //Show Bill
            return redirect()->route('subscription_payments.index')->with('error', 'Payment Failed!');
        } else {
            //Show Bill
            return redirect()->route('subscription_payments.index')->with('error', 'Payment Failed!');
        }
    }


    /**
     * Process Canceled SMS Payment with SSL Commerz Payment Gateway
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function cancelSubscriptionPayment(Request $request)
    {
        $subscription_payment = subscription_payment::where('mer_txnid', $request->tran_id)->firstOrFail();

        // debug
        $input = $request->all();

        if (config('consumer.debug_payment')) {
            Storage::put('subscription_payment/' . $subscription_payment->id . '.json', json_encode($input));
        }

        Auth::loginUsingId($subscription_payment->mgid);

        if ($subscription_payment->pay_status == 'Pending') {
            $subscription_payment->pay_status = 'Failed';
            $subscription_payment->store_amount = 0;
            $subscription_payment->transaction_fee = 0;
            $subscription_payment->save();
            //Show Bill
            return redirect()->route('subscription_payments.index')->with('error', 'Payment Failed!');
        } else {
            //Show Bill
            return redirect()->route('subscription_payments.index')->with('error', 'Payment Failed!');
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

        $response = Http::retry(3, 100)->asForm()->get($this->recheck_url, [
            'sessionkey' => $customer_payment->pgw_payment_identifier,
            'store_id' => $payment_gateway->username,
            'store_passwd' => $payment_gateway->password,
        ]);

        // debug
        if (config('consumer.debug_payment')) {
            Storage::put('customer_payment/' . $customer_payment->id . '.json', $response);
        }

        $reply = json_decode($response, true);

        if ($reply['status'] === 'VALID' || $reply['status'] === 'VALIDATED') {

            //update Customer Payment
            $customer_payment->pgw_txnid = $reply['val_id'];
            $customer_payment->bank_txnid = $reply['bank_tran_id'];
            $customer_payment->pay_status = 'Successful';
            $customer_payment->store_amount = $reply['store_amount'];
            $customer_payment->transaction_fee = $customer_payment->amount_paid - $reply['store_amount'];
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

        $response = Http::retry(3, 100)->asForm()->get($this->recheck_url, [
            'sessionkey' => $sms_payment->pgw_payment_identifier,
            'store_id' => $payment_gateway->username,
            'store_passwd' => $payment_gateway->password,
        ]);

        // debug
        if (config('consumer.debug_payment')) {
            Storage::put('sms_payment/' . $sms_payment->id . '.json', $response);
        }

        $reply = json_decode($response, true);

        if ($reply['status'] === 'VALID' || $reply['status'] === 'VALIDATED') {

            //update Customer Payment
            $sms_payment->pgw_txnid = $reply['val_id'];
            $sms_payment->bank_txnid = $reply['bank_tran_id'];
            $sms_payment->pay_status = 'Successful';
            $sms_payment->store_amount = $reply['store_amount'];
            $sms_payment->transaction_fee = $sms_payment->amount_paid - $reply['store_amount'];
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

        $response = Http::retry(3, 100)->asForm()->get($this->recheck_url, [
            'sessionkey' => $subscription_payment->pgw_payment_identifier,
            'store_id' => $payment_gateway->username,
            'store_passwd' => $payment_gateway->password,
        ]);

        // debug
        if (config('consumer.debug_payment')) {
            Storage::put('subscription_payment/' . $subscription_payment->id . '.json', $response);
        }

        $reply = json_decode($response, true);

        if ($reply['status'] === 'VALID' || $reply['status'] === 'VALIDATED') {

            //update Customer Payment
            $subscription_payment->pgw_txnid = $reply['val_id'];
            $subscription_payment->bank_txnid = $reply['bank_tran_id'];
            $subscription_payment->pay_status = 'Successful';
            $subscription_payment->store_amount = $reply['store_amount'];
            $subscription_payment->transaction_fee = $subscription_payment->amount_paid - $reply['store_amount'];
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
