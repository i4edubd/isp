<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\CacheController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Sms\SmsGatewayController;
use App\Http\Controllers\SmsGenerator;
use App\Http\Controllers\SubscriptionStatusController;
use App\Models\customer_payment;
use App\Models\operator;
use App\Models\package;
use App\Models\payment_gateway;
use App\Models\subscription_bill;
use App\Models\subscription_payment;
use Illuminate\Http\Request;

class SendMoneyController extends Controller
{

    /**
     * Initiate Send Money Payment for Customer
     *
     * @param  \Illuminate\Http\Request  $request
     * @param \App\Models\customer_payment $customer_payment
     * @return \Illuminate\Http\Response
     */

    public function createCustomerPayment(Request $request, customer_payment $customer_payment)
    {

        if ($customer_payment->pay_status !== 'Pending') {
            abort(500, 'Invalid Request!');
        }

        // delete previous attempts without submits
        $pattempts = customer_payment::where('operator_id', $customer_payment->operator_id)
            ->where('customer_id', $customer_payment->customer_id)
            ->where('payment_gateway_id', $customer_payment->payment_gateway_id)
            ->whereNull('card_number')
            ->whereNull('bank_txnid')
            ->get()
            ->except([$customer_payment->id]);

        foreach ($pattempts as $pattempt) {
            $pattempt->delete();
        }

        // operator
        $all_customer = $request->user('customer');
        $operator = CacheController::getOperator($all_customer->operator_id);

        //get payment gateway
        $payment_gateway = payment_gateway::findOrFail($customer_payment->payment_gateway_id);

        //package
        $package = package::find($customer_payment->package_id);

        return view('customers.send-money', [
            'operator' => $operator,
            'customer_payment' => $customer_payment,
            'package' => $package,
            'payment_gateway' => $payment_gateway,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param \App\Models\customer_payment $customer_payment
     * @return \Illuminate\Http\Response
     */
    public function storeCustomerPayment(Request $request, customer_payment $customer_payment)
    {
        $request->validate([
            'card_number' => 'required|string',
            'bank_txnid' => 'required|string',
        ]);

        $card_number = validate_mobile($request->card_number);

        if ($card_number == 0) {
            $customer_payment->delete();
            abort(500, 'Invalid Sender!');
        }

        $customer_payment->card_number = $card_number;
        $customer_payment->bank_txnid = $request->bank_txnid;
        $customer_payment->save();

        // <<Prevent duplicate send money
        $pending_send_money = [
            ['operator_id', '=', $customer_payment->operator_id],
            ['customer_id', '=', $customer_payment->customer_id],
            ['payment_gateway_id', '=', $customer_payment->payment_gateway_id],
            ['pay_status', '=', 'Pending'],
        ];

        if (customer_payment::where($pending_send_money)->count() > 1) {
            $customer_payment->delete();
            return redirect()->route('customers.profile')->with('error', 'Please allow us to verify your previous payment!');
        }
        // Prevent duplicate send money>>

        // <<sms
        $customer = CacheController::getCustomer($customer_payment->mobile);

        $operator = operator::find($customer_payment->operator_id);

        $message = SmsGenerator::sendMoneyNotification($operator, $customer_payment->amount_paid, $card_number, $customer->id);

        $payment_gateway = payment_gateway::findOrFail($customer_payment->payment_gateway_id);

        $controller = new SmsGatewayController();

        $controller->sendSms($operator, $payment_gateway->msisdn, $message);
        // sms>>
        return redirect()->route('send_money.customer_payment.success', ['customer_payment' => $customer_payment->id]);
    }

    /**
     * Success Message for Send Money
     *
     * @param \App\Models\customer_payment $customer_payment
     * @return \Illuminate\Http\Response
     */
    public function successCustomerPayment(customer_payment $customer_payment)
    {
        $operator = operator::findOrFail($customer_payment->operator_id);
        return view('customers.send-money-success', [
            'operator' => $operator,
        ]);
    }

    /**
     * Initiate Subscription Payment through Send Money
     *
     * @param \App\Models\subscription_payment $subscription_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function createSubscriptionPayment(subscription_payment $subscription_payment)
    {
        // delete previous attempts without submits
        $pattempts = customer_payment::where('mgid', $subscription_payment->mgid)
            ->where('payment_gateway_id', $subscription_payment->payment_gateway_id)
            ->whereNull('card_number')
            ->whereNull('bank_txnid')
            ->get()
            ->except([$subscription_payment->id]);

        foreach ($pattempts as $pattempt) {
            $pattempt->delete();
        }

        $payment_gateway = payment_gateway::find($subscription_payment->payment_gateway_id);

        return view('admins.group_admin.subscription-payment-send-money', [
            'subscription_payment' => $subscription_payment,
            'payment_gateway' => $payment_gateway,
        ]);
    }

    /**
     * Store Information paid through send money
     *
     * @param \Illuminate\Http\Request
     * @param \App\Models\subscription_payment $subscription_payment
     * @return \Illuminate\Http\Response
     */

    public function storeSubscriptionPayment(Request $request, subscription_payment $subscription_payment)
    {

        $request->validate([
            'card_number' => 'required|string',
            'bank_txnid' => 'required|string',
        ]);

        $card_number = validate_mobile($request->card_number);

        if ($card_number == 0) {
            $subscription_payment->delete();
            abort(500, 'Invalid Sender!');
        }

        $subscription_payment->card_number = $card_number;
        $subscription_payment->bank_txnid = $request->bank_txnid;
        $subscription_payment->save();

        $bill = subscription_bill::find($subscription_payment->subscription_bill_id);
        $bill->delete();

        SubscriptionStatusController::activate($request->user());

        return redirect()->route('subscription_payments.index')->with('success', 'An operator will verify the payment!');
    }
}
