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

class EasypaywayTransactionController extends Controller
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


    public function __construct()

    {
        if (config('local.is_sandbox_pgw')) {
            //$this->payment_url = 'http://epwsandbox.com/payment/request.php';
            $this->payment_url = 'http://epwsandbox.com/payment/index.php';
            $this->validation_url = 'http://epwsandbox.com/api/v1/trxcheck/request.php?';
        } else {
            //$this->payment_url = 'https://securepay.easypayway.com/payment/request.php';
            $this->payment_url = 'https://securepay.easypayway.com/payment/index.php';
            $this->validation_url = 'https://securepay.easypayway.com/api/v1/trxcheck/request.php?';
        }
    }



    /**
     * Initiate Customer Payment through EasyPayWay Payment Gateway
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

        $response =  Http::asForm()->post($this->payment_url, [
            'store_id' => $payment_gateway->username,
            'tran_id' => $customer_payment->mer_txnid,
            'success_url' => route('easypayway.customer_payment.success'),
            'fail_url' => route('easypayway.customer_payment.failed'),
            'cancel_url' => route('easypayway.customer_payment.canceled'),
            'amount' => $customer_payment->amount_paid,
            'currency' => 'BDT',
            'signature_key' => $payment_gateway->password,
            'desc' => 'Internet Bill',
            'cus_name' => $customer_payment->mobile,
            'cus_email' => $cus_email,
            'cus_add1' => 'Bangladesh',
            'cus_city' => 'Bangladesh',
            'cus_state' => 'BD',
            'cus_postcode' => '1200',
            'cus_country' => 'Bangladesh',
            'cus_phone' => '01751000000',
        ]);

        return $response;
    }



    /**
     * Process Success Customer Payment with easypayway Payment Gateway
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */

    public function successCustomerPayment(Request $request)
    {
        if ($request->pay_status === 'Successful') {

            $customer_payment = customer_payment::where('mer_txnid', $request->mer_txnid)->firstOrFail();

            // debug
            $input = $request->all();

            if (config('consumer.debug_payment')) {
                Storage::put('customer_payment/' . $customer_payment->id . '.json', json_encode($input));
            }

            $payment_gateway = payment_gateway::findOrFail($customer_payment->payment_gateway_id);

            $response = Http::retry(3, 100)->asForm()->get($this->validation_url, [
                'request_id' => $request->mer_txnid,
                'store_id' => $payment_gateway->username,
                'signature_key' => $payment_gateway->password,
                'type' => 'json',
            ]);

            $reply = json_decode($response, true);

            if ($customer_payment->pay_status !== 'Successful' && $reply['pay_status'] === 'Successful') {

                //update Customer Payment
                $customer_payment->pgw_txnid = $reply['epw_txnid'];

                $customer_payment->pay_status = 'Successful';

                if (array_key_exists('rec_amount', $reply)) {
                    $customer_payment->store_amount = $reply['rec_amount'];
                } else {
                    if (array_key_exists('store_amount', $reply)) {
                        $customer_payment->store_amount = $reply['store_amount'];
                    }
                }

                $customer_payment->transaction_fee = $customer_payment->amount_paid - $customer_payment->store_amount;

                if (array_key_exists('bank_txn', $reply)) {
                    $customer_payment->bank_txnid = $reply['bank_txn'];
                } else {
                    if (array_key_exists('bank_trxid', $reply)) {
                        $customer_payment->bank_txnid = $reply['bank_trxid'];
                    }
                }

                if (array_key_exists('card_type', $reply)) {
                    $customer_payment->card_type = $reply['card_type'];
                } else {
                    if (array_key_exists('payment_processor', $reply)) {
                        $customer_payment->card_type = $reply['payment_processor'];
                    }
                }

                if (array_key_exists('card_number', $reply)) {
                    $customer_payment->card_number = $reply['card_number'];
                } else {
                    if (array_key_exists('card_no', $reply)) {
                        $customer_payment->card_number = $reply['card_no'];
                    }
                }

                $customer_payment->save();

                CustomersPaymentProcessController::store($customer_payment);

                //Show Profile
                return redirect()->route('customers.profile')->with('success', 'Package has been activated successfully');
            } else {
                abort(500, 'Payment Validation Failed!');
            }
        } else {
            abort(500);
        }
    }



    /**
     * Process Failed Customer Payment with easypayway Payment Gateway
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
            $customer_payment->bank_txnid = $request->bank_txn;
            $customer_payment->card_type = $request->card_type;
            $customer_payment->card_number = $request->card_number;
            $customer_payment->save();
            //Show Profile
            return redirect()->route('customers.profile')->with('error', 'Payment Failed!');
        } else {
            //Show Profile
            return redirect()->route('customers.profile')->with('error', 'Payment Failed!');
        }
    }


    /**
     * Process Canceled Customer Payment with easypayway Payment Gateway
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
            return redirect()->route('customers.profile')->with('error', 'Payment Failed!');
        } else {
            //Show Profile
            return redirect()->route('customers.profile')->with('error', 'Payment Failed!');
        }
    }


    /**
     * Initiate SMS Payment through EasyPayWay Payment Gateway
     *
     * @param \App\Models\sms_payment $sms_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function initiateSmsPayment(sms_payment $sms_payment)
    {
        //payment_gateway
        $payment_gateway = payment_gateway::findOrFail($sms_payment->payment_gateway_id);

        //operator
        $operator = operator::find($sms_payment->operator_id);

        //response
        $response =  Http::asForm()->post($this->payment_url, [
            'store_id' => $payment_gateway->username,
            'tran_id' => $sms_payment->mer_txnid,
            'success_url' => route('easypayway.sms_payment.success'),
            'fail_url' => route('easypayway.sms_payment.failed'),
            'cancel_url' => route('easypayway.sms_payment.canceled'),
            'amount' => $sms_payment->amount_paid,
            'currency' => 'BDT',
            'signature_key' => $payment_gateway->password,
            'desc' => 'SMS Bill',
            'cus_name' => $operator->company,
            'cus_email' => $operator->email,
            'cus_add1' => 'Bangladesh',
            'cus_city' => 'Bangladesh',
            'cus_state' => 'BD',
            'cus_postcode' => '1200',
            'cus_country' => 'Bangladesh',
            'cus_phone' => '01751000000',
            'opt_a' => $sms_payment->operator_id,
        ]);

        return $response;
    }



    /**
     * Process Success SMS Payment with easypayway Payment Gateway
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */

    public function successSmsPayment(Request $request)
    {
        if ($request->pay_status === 'Successful') {

            $sms_payment = sms_payment::where('mer_txnid', $request->mer_txnid)->firstOrFail();

            // debug
            $input = $request->all();

            if (config('consumer.debug_payment')) {
                Storage::put('sms_payment/' . $sms_payment->id . '.json', json_encode($input));
            }

            Auth::loginUsingId($sms_payment->operator_id);

            $payment_gateway = payment_gateway::findOrFail($sms_payment->payment_gateway_id);

            $response = Http::retry(3, 100)->asForm()->get($this->validation_url, [
                'request_id' => $request->mer_txnid,
                'store_id' => $payment_gateway->username,
                'signature_key' => $payment_gateway->password,
                'type' => 'json',
            ]);

            $reply = json_decode($response, true);

            if ($sms_payment->pay_status !== 'Successful' && $reply['pay_status'] === 'Successful') {

                //update SMS Payment
                $sms_payment->pgw_txnid = $reply['epw_txnid'];

                $sms_payment->pay_status = 'Successful';

                if (array_key_exists('rec_amount', $reply)) {
                    $sms_payment->store_amount = $reply['rec_amount'];
                } else {
                    if (array_key_exists('store_amount', $reply)) {
                        $sms_payment->store_amount = $reply['store_amount'];
                    }
                }

                $sms_payment->transaction_fee = $sms_payment->amount_paid - $sms_payment->store_amount;

                if (array_key_exists('bank_txn', $reply)) {
                    $sms_payment->bank_txnid = $reply['bank_txn'];
                } else {
                    if (array_key_exists('bank_trxid', $reply)) {
                        $sms_payment->bank_txnid = $reply['bank_trxid'];
                    }
                }

                $sms_payment->save();

                SmsBalanceHistoryController::store($sms_payment);

                $sms_bill = sms_bill::find($sms_payment->sms_bill_id);

                if ($sms_bill) {
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
     * Process Failed SMS Payment with easypayway Payment Gateway
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function failSmsPayment(Request $request)
    {
        $sms_payment = sms_payment::where('mer_txnid', $request->mer_txnid)->firstOrFail();

        // debug
        $input = $request->all();

        if (config('consumer.debug_payment')) {
            Storage::put('sms_payment/' . $sms_payment->id . '.json', json_encode($input));
        }

        Auth::loginUsingId($sms_payment->operator_id);

        if ($sms_payment->pay_status == 'Pending' && $request->pay_status == 'Failed') {
            $sms_payment->pay_status = 'Failed';
            $sms_payment->store_amount = 0;
            $sms_payment->transaction_fee = 0;
            $sms_payment->save();
            //Show bill
            return redirect()->route('sms_bills.index')->with('error', 'Payment Failed!');
        } else {
            //Show bill
            return redirect()->route('sms_bills.index')->with('error', 'Payment Failed!');
        }
    }


    /**
     * Process Canceled SMS Payment with easypayway Payment Gateway
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function cancelSmsPayment(Request $request)
    {
        $sms_payment = sms_payment::where('mer_txnid', $request->mer_txnid)->firstOrFail();

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
     * Initiate Subscription Payment through EasyPayWay Payment Gateway
     *
     * @param \App\Models\subscription_payment $subscription_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function initiateSubscriptionPayment(subscription_payment $subscription_payment)
    {
        //payment_gateway
        $payment_gateway = payment_gateway::findOrFail($subscription_payment->payment_gateway_id);

        //operator
        $operator = operator::find($subscription_payment->mgid);

        //response
        $response =  Http::asForm()->post($this->payment_url, [
            'store_id' => $payment_gateway->username,
            'tran_id' => $subscription_payment->mer_txnid,
            'success_url' => route('easypayway.subscription_payment.success'),
            'fail_url' => route('easypayway.subscription_payment.failed'),
            'cancel_url' => route('easypayway.subscription_payment.canceled'),
            'amount' => $subscription_payment->amount_paid,
            'currency' => 'BDT',
            'signature_key' => $payment_gateway->password,
            'desc' => 'Subscription Payment',
            'cus_name' => $operator->company,
            'cus_email' => $operator->email,
            'cus_add1' => 'Bangladesh',
            'cus_city' => 'Bangladesh',
            'cus_state' => 'BD',
            'cus_postcode' => '1200',
            'cus_country' => 'Bangladesh',
            'cus_phone' => '01751000000',
            'opt_a' => $subscription_payment->mgid,
            'opt_b' => $subscription_payment->subscription_bill_id,
            'opt_c' => $subscription_payment->operator_name,
            'opt_d' => $subscription_payment->operator_email,
        ]);

        return $response;
    }



    /**
     * Process Success SMS Payment with easypayway Payment Gateway
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */

    public function successSubscriptionPayment(Request $request)
    {
        if ($request->pay_status === 'Successful') {

            $subscription_payment = subscription_payment::where('mer_txnid', $request->mer_txnid)->firstOrFail();

            Auth::loginUsingId($subscription_payment->mgid);

            $payment_gateway = payment_gateway::findOrFail($subscription_payment->payment_gateway_id);

            $response = Http::retry(3, 100)->asForm()->get($this->validation_url, [
                'request_id' => $request->mer_txnid,
                'store_id' => $payment_gateway->username,
                'signature_key' => $payment_gateway->password,
                'type' => 'json',
            ]);

            // debug
            $input = $request->all();

            if (config('consumer.debug_payment')) {
                Storage::put('subscription_payment/' . $subscription_payment->id . '.json', json_encode($input));
            }

            $reply = json_decode($response, true);

            if ($subscription_payment->pay_status === 'Pending' && $reply['pay_status'] === 'Successful') {

                //update Subscription Payment
                $subscription_payment->pgw_txnid = $reply['epw_txnid'];

                $subscription_payment->pay_status = 'Successful';

                if (array_key_exists('rec_amount', $reply)) {
                    $subscription_payment->store_amount = $reply['rec_amount'];
                } else {
                    if (array_key_exists('store_amount', $reply)) {
                        $subscription_payment->store_amount = $reply['store_amount'];
                    }
                }

                $subscription_payment->transaction_fee = $subscription_payment->amount_paid - $subscription_payment->store_amount;

                if (array_key_exists('bank_txn', $reply)) {
                    $subscription_payment->bank_txnid = $reply['bank_txn'];
                } else {
                    if (array_key_exists('bank_trxid', $reply)) {
                        $subscription_payment->bank_txnid = $reply['bank_trxid'];
                    }
                }

                $subscription_payment->save();

                //Delete Subscription Bill
                $subscription_bill = subscription_bill::find($subscription_payment->subscription_bill_id);

                if ($subscription_bill) {
                    $subscription_bill->delete();
                }

                // Delete all Failed Payments
                $where = [
                    ['pay_status', '!=', 'Successful'],
                    ['subscription_bill_id', '=', $subscription_payment->subscription_bill_id],
                ];
                subscription_payment::where($where)->delete();

                //Record Incomes
                SubscriptionPaymentController::recordIncomes($subscription_payment);

                //Show Subscription Payments
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
     * Process Failed SMS Payment with easypayway Payment Gateway
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function failSubscriptionPayment(Request $request)
    {
        $subscription_payment = subscription_payment::where('mer_txnid', $request->mer_txnid)->firstOrFail();

        // debug
        $input = $request->all();

        if (config('consumer.debug_payment')) {
            Storage::put('subscription_payment/' . $subscription_payment->id . '.json', json_encode($input));
        }

        Auth::loginUsingId($subscription_payment->mgid);

        if ($subscription_payment->pay_status == 'Pending' && $request->pay_status == 'Failed') {
            $subscription_payment->pay_status = 'Failed';
            $subscription_payment->store_amount = 0;
            $subscription_payment->transaction_fee = 0;
            $subscription_payment->save();
            //Show bill
            return redirect()->route('subscription_bills.index')->with('error', 'Payment Failed!');
        } else {
            //Show bill
            return redirect()->route('subscription_bills.index')->with('error', 'Payment Failed!');
        }
    }


    /**
     * Process Canceled SMS Payment with easypayway Payment Gateway
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function cancelSubscriptionPayment(Request $request)
    {
        $subscription_payment = subscription_payment::where('mer_txnid', $request->mer_txnid)->firstOrFail();

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
            return redirect()->route('subscription_bills.index')->with('error', 'Payment Failed!');
        } else {
            //Show Bill
            return redirect()->route('subscription_bills.index')->with('error', 'Payment Failed!');
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
        $payment_gateway = payment_gateway::findOrFail($customer_payment->payment_gateway_id);

        $response = Http::retry(3, 100)->asForm()->get($this->validation_url, [
            'request_id' => $customer_payment->mer_txnid,
            'store_id' => $payment_gateway->username,
            'signature_key' => $payment_gateway->password,
            'type' => 'json',
        ]);

        // debug
        if (config('consumer.debug_payment')) {
            Storage::put('customer_payment/' . $customer_payment->id . '.json', $response);
        }

        $reply = json_decode($response, true);

        // Invalid-Data
        if (array_key_exists('status', $reply)) {
            if ($reply['status'] == 'Invalid-Data') {
                $customer_payment->delete();
                return 0;
            }
        }

        if ($customer_payment->pay_status !== 'Successful' && $reply['pay_status'] === 'Successful') {

            //update Customer Payment
            $customer_payment->pgw_txnid = $reply['epw_txnid'];

            $customer_payment->pay_status = 'Successful';

            if (array_key_exists('store_amount', $reply)) {
                $customer_payment->store_amount = $reply['store_amount'];
            } else {
                if (array_key_exists('rec_amount', $reply)) {
                    $customer_payment->store_amount = $reply['rec_amount'];
                }
            }

            $customer_payment->transaction_fee = $customer_payment->amount_paid - $customer_payment->store_amount;

            if (array_key_exists('bank_txn', $reply)) {
                $customer_payment->bank_txnid = $reply['bank_txn'];
            } else {
                if (array_key_exists('bank_trxid', $reply)) {
                    $customer_payment->bank_txnid = $reply['bank_trxid'];
                }
            }

            if (array_key_exists('card_type', $reply)) {
                $customer_payment->card_type = $reply['card_type'];
            } else {
                if (array_key_exists('payment_processor', $reply)) {
                    $customer_payment->card_type = $reply['payment_processor'];
                }
            }

            if (array_key_exists('card_number', $reply)) {
                $customer_payment->card_number = $reply['card_number'];
            } else {
                if (array_key_exists('cardnumber', $reply)) {
                    $customer_payment->card_number = $reply['cardnumber'];
                }
            }

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
        $payment_gateway = payment_gateway::findOrFail($sms_payment->payment_gateway_id);

        $response = Http::retry(3, 100)->asForm()->get($this->validation_url, [
            'request_id' => $sms_payment->mer_txnid,
            'store_id' => $payment_gateway->username,
            'signature_key' => $payment_gateway->password,
            'type' => 'json',
        ]);

        // debug
        if (config('consumer.debug_payment')) {
            Storage::put('sms_payment/' . $sms_payment->id . '.json', $response);
        }

        $reply = json_decode($response, true);

        if (array_key_exists('status', $reply)) {
            if ($reply['status'] == 'Invalid-Data') {
                $sms_payment->delete();
                return 0;
            }
        }

        if ($sms_payment->pay_status !== 'Successful' && $reply['pay_status'] === 'Successful') {

            $sms_bill = sms_bill::where('id', $sms_payment->sms_bill_id)->firstOr(function () {
                return sms_bill::make([
                    'id' => 0,
                ]);
            });

            //update SMS Payment
            $sms_payment->pgw_txnid = $reply['epw_txnid'];

            $sms_payment->pay_status = 'Successful';

            $sms_payment->amount_paid = $sms_payment->sms_cost;

            if (array_key_exists('store_amount', $reply)) {
                $sms_payment->store_amount = $reply['store_amount'];
            } else {
                if (array_key_exists('rec_amount', $reply)) {
                    $sms_payment->store_amount = $reply['rec_amount'];
                }
            }

            $sms_payment->transaction_fee = $sms_payment->amount_paid - $sms_payment->store_amount;

            if (array_key_exists('bank_txn', $reply)) {
                $sms_payment->bank_txnid = $reply['bank_txn'];
            } else {
                if (array_key_exists('bank_trxid', $reply)) {
                    $sms_payment->bank_txnid = $reply['bank_trxid'];
                }
            }

            $sms_payment->save();

            //Delete SMS Bill
            if ($sms_bill->id > 0) {
                $sms_bill->delete();
            }

            SmsBalanceHistoryController::store($sms_payment);

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
        $payment_gateway = payment_gateway::findOrFail($subscription_payment->payment_gateway_id);

        $response = Http::retry(3, 100)->asForm()->get($this->validation_url, [
            'request_id' => $subscription_payment->mer_txnid,
            'store_id' => $payment_gateway->username,
            'signature_key' => $payment_gateway->password,
            'type' => 'json',
        ]);

        // debug
        if (config('consumer.debug_payment')) {
            Storage::put('subscription_payment/' . $subscription_payment->id . '.json', $response);
        }

        $reply = json_decode($response, true);

        if (array_key_exists('status', $reply)) {
            if ($reply['status'] == 'Invalid-Data') {
                $subscription_payment->delete();
                return 0;
            }
        }

        if ($subscription_payment->pay_status !== 'Successful' && $reply['pay_status'] === 'Successful') {

            //update Subscription Payment
            $subscription_payment->pgw_txnid = $reply['epw_txnid'];

            $subscription_payment->pay_status = 'Successful';

            if (array_key_exists('store_amount', $reply)) {
                $subscription_payment->store_amount = $reply['store_amount'];
            } else {
                if (array_key_exists('rec_amount', $reply)) {
                    $subscription_payment->store_amount = $reply['rec_amount'];
                }
            }

            $subscription_payment->transaction_fee = $subscription_payment->amount_paid - $subscription_payment->store_amount;

            if (array_key_exists('bank_txn', $reply)) {
                $subscription_payment->bank_txnid = $reply['bank_txn'];
            } else {
                if (array_key_exists('bank_trxid', $reply)) {
                    $subscription_payment->bank_txnid = $reply['bank_trxid'];
                }
            }

            $subscription_payment->save();

            //Delete Subscription Bill
            $subscription_bill = subscription_bill::find($subscription_payment->subscription_bill_id);

            if ($subscription_bill) {
                $subscription_bill->delete();
            }

            // Delete all Failed Payments
            $where = [
                ['pay_status', '!=', 'Successful'],
                ['subscription_bill_id', '=', $subscription_payment->subscription_bill_id],
            ];
            subscription_payment::where($where)->delete();

            //Record Incomes
            SubscriptionPaymentController::recordIncomes($subscription_payment);

            return 1;
        } else {
            $subscription_payment->delete();
            return 0;
        }
    }
}
