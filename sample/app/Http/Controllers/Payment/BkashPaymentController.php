<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\CacheController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Sms\SmsGatewayController;
use App\Http\Controllers\SmsGenerator;
use App\Models\customer_payment;
use App\Models\operator;
use App\Models\package;
use App\Models\payment_gateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BkashPaymentController extends Controller
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

        //get payment gateway
        $payment_gateway = payment_gateway::findOrFail($customer_payment->payment_gateway_id);

        //package
        $package = package::find($customer_payment->package_id);

        // operator
        $all_customer = $request->user('customer');
        $operator = CacheController::getOperator($all_customer->operator_id);

        // Qr Code
        $credentials_path = Storage::path($payment_gateway->credentials_path);

        if (file_exists($credentials_path)) {
            $qr_code = 'data:image/png;base64,' . base64_encode(file_get_contents($credentials_path));
        } else {
            $qr_code = "";
        }

        return view('customers.bkash-payment', [
            'operator' => $operator,
            'customer_payment' => $customer_payment,
            'package' => $package,
            'payment_gateway' => $payment_gateway,
            'qr_code' => $qr_code,
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

        // <<Prevent duplicate bkash payment
        $pending_bkash_payment = [
            ['operator_id', '=', $customer_payment->operator_id],
            ['customer_id', '=', $customer_payment->customer_id],
            ['payment_gateway_id', '=', $customer_payment->payment_gateway_id],
            ['pay_status', '=', 'Pending'],
        ];

        if (customer_payment::where($pending_bkash_payment)->count() > 1) {
            $customer_payment->delete();
            return redirect()->route('customers.profile')->with('error', 'Please allow us to verify your previous payment!');
        }
        // Prevent duplicate bkash payment>>

        // <<sms
        $customer = CacheController::getCustomer($customer_payment->mobile);

        $operator = operator::find($customer_payment->operator_id);

        $message = SmsGenerator::sendMoneyNotification($operator, $customer_payment->amount_paid, $card_number, $customer->id);

        $payment_gateway = payment_gateway::findOrFail($customer_payment->payment_gateway_id);

        $controller = new SmsGatewayController();

        $controller->sendSms($operator, $payment_gateway->msisdn, $message);
        // sms>>
        return redirect()->route('bkash_payment.customer_payment.success', ['customer_payment' => $customer_payment->id]);
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
        return view('customers.bkash-payment-success', [
            'operator' => $operator,
        ]);
    }
}
