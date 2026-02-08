<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\OperatorsOnlinePaymentController;
use App\Models\operator;
use App\Models\operators_online_payment;
use App\Models\payment_gateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SslcommerzForOperatorsOnlinePaymentController extends SslcommerzBaseController
{
    /**
     * Create Payment
     *
     * @param \App\Models\operators_online_payment $operators_online_payment
     * @return \Illuminate\Http\Response
     */
    public function createPayment(operators_online_payment $operators_online_payment)
    {

        $payment_gateway = payment_gateway::findOrFail($operators_online_payment->payment_gateway_id);

        $operator = operator::find($operators_online_payment->operator_id);

        $response =  Http::asForm()->post($this->payment_url, [
            'store_id' => $payment_gateway->username,
            'store_passwd' => $payment_gateway->password,
            'total_amount' => $operators_online_payment->amount_paid,
            'currency' => 'BDT',
            'tran_id' => $operators_online_payment->id,
            'product_category' => 'Internet Topup',
            'success_url' => route('sslcommerz.operators_online_payment.callback'),
            'fail_url' => route('sslcommerz.operators_online_payment.callback'),
            'cancel_url' => route('sslcommerz.operators_online_payment.callback'),
            'emi_option' => 0,
            'cus_name' => $operator->mobile,
            'cus_email' => $operator->email,
            'cus_add1' => 'Bangladesh',
            'cus_city' => 'Bangladesh',
            'cus_postcode' => '1200',
            'cus_country' => 'Bangladesh',
            'cus_phone' => '01751000000',
            'shipping_method' => 'NO',
            'product_name' => 'Internet Package',
            'product_profile' => 'non-physical-goods',
        ]);

        if (config('consumer.debug_payment')) {
            Storage::put('Sslcommerz_debug/createPayment.json', $response);
        }

        $reply = json_decode($response, true);

        if ($reply['status'] === 'SUCCESS') {
            $operators_online_payment->pgw_payment_identifier = $reply['sessionkey'];
            $operators_online_payment->save();
            return redirect()->away($reply['GatewayPageURL']);
        } else {
            abort(500, $reply['failedreason']);
        }
    }

    /**
     * Success callbackURL
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function successCallback(Request $request)
    {
        $request->validate([
            'tran_id' => 'required',
            'val_id' => 'required',
        ]);

        $operators_online_payment = operators_online_payment::findOrFail($request->tran_id);
        $operators_online_payment->pgw_txnid = $request->val_id;
        $operators_online_payment->save();

        $response = $this->verifyPayment($operators_online_payment);

        if (is_bool($response)) {
            if ($operators_online_payment->payment_purpose == 'cash_in') {
                return redirect()->route('accounts.receivable')->with('error', 'Payment Failed');
            }
            if ($operators_online_payment->payment_purpose == 'cash_out') {
                return redirect()->route('accounts.payable')->with('error', 'Payment Failed');
            }
        } else {
            return $response;
        }
    }

    /**
     * Verify Payment
     *
     * @param \App\Models\operators_online_payment $operators_online_payment
     * @return \Illuminate\Http\Response
     */
    public function verifyPayment(operators_online_payment $operators_online_payment)
    {
        if ($operators_online_payment->pay_status === 'Successful') {
            return false;
        }

        if (is_null($operators_online_payment->pgw_payment_identifier)) {
            $operators_online_payment->delete();
            return false;
        }

        if (is_null($operators_online_payment->pgw_txnid)) {
            $operators_online_payment->delete();
            return false;
        }

        $payment_gateway = payment_gateway::findOrFail($operators_online_payment->payment_gateway_id);

        $response = Http::retry(3, 100)->asForm()->get($this->validation_url, [
            'val_id' => $operators_online_payment->pgw_txnid,
            'store_id' => $payment_gateway->username,
            'store_passwd' => $payment_gateway->password,
            'format' => 'json',
        ]);

        if (config('consumer.debug_payment')) {
            Storage::put('Sslcommerz_debug/verifyPayment.json', $response);
        }

        $reply = json_decode($response, true);

        if ($reply['status'] === 'VALID' || $reply['status'] === 'VALIDATED') {
            //update Payment
            $transaction_fee = $operators_online_payment->amount_paid * ($payment_gateway->service_charge_percentage / 100);
            $operators_online_payment->bank_txnid = $reply['bank_tran_id'];
            $operators_online_payment->pay_status = 'Successful';
            $operators_online_payment->store_amount = $operators_online_payment->amount_paid - $transaction_fee;
            $operators_online_payment->transaction_fee =  $transaction_fee;
            $operators_online_payment->save();
            return OperatorsOnlinePaymentController::store($operators_online_payment);
        }

        // Failed
        $operators_online_payment->delete();
        return false;
    }
}
