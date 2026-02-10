<?php

namespace App\Http\Controllers\Sms;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Payment\BkashCheckoutController;
use App\Http\Controllers\Payment\bKashTokenizedSmsPaymentController;
use App\Http\Controllers\Payment\EasypaywayTransactionController;
use App\Http\Controllers\Payment\NagadPaymentGatewayController;
use App\Http\Controllers\Payment\ShurjoPaySmsPaymentController;
use App\Http\Controllers\Payment\SslcommerzTransactionController;
use App\Models\sms_payment;
use App\Models\sms_bill;
use App\Models\operator;
use App\Models\payment_gateway;
use App\Models\sms_gateway;
use Carbon\Carbon;

use Illuminate\Http\Request;

class SmsPaymentController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();

        $merchant = sms_gateway::where('operator_id', $operator->id)->count();

        $where = [];

        if ($merchant) {
            $where[] = ['merchant_id', '=', $operator->id];
        } else {
            $where[] = ['operator_id', '=', $operator->id];
        }

        // year
        if ($request->filled('year')) {
            $where[] = ['year', '=', $request->year];
        }

        // month
        if ($request->filled('month')) {
            $where[] = ['month', '=', $request->month];
        }

        $sms_payments = sms_payment::where($where)->paginate(15);

        $total_amount = sms_payment::where($where)->sum('amount_paid');

        switch ($operator->role) {

            case 'super_admin':
                return view('admins.super_admin.sms-payments', [
                    'sms_payments' => $sms_payments,
                    'merchant' => $merchant,
                    'total_amount' => $total_amount,
                ]);
                break;

            case 'group_admin':
                return view('admins.group_admin.sms-payments', [
                    'sms_payments' => $sms_payments,
                    'merchant' => $merchant,
                    'total_amount' => $total_amount,
                ]);
                break;

            case 'operator':
                return view('admins.operator.sms-payments', [
                    'sms_payments' => $sms_payments,
                    'merchant' => $merchant,
                    'total_amount' => $total_amount,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.sms-payments', [
                    'sms_payments' => $sms_payments,
                    'merchant' => $merchant,
                    'total_amount' => $total_amount,
                ]);
                break;
        }
    }



    /**
     * Show the form for creating a new resource.
     *
     * @param \App\Models\sms_bill $sms_bill
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(sms_bill $sms_bill, Request $request)
    {
        $request->validate([
            'payment_gateway_id' => 'required'
        ]);

        //operator
        $operator = operator::findOrFail($sms_bill->operator_id);
        //payment_gateway
        $payment_gateway = payment_gateway::findOrFail($request->payment_gateway_id);
        //mer_txnid
        $mer_txnid = random_int(1000, 9999) . Carbon::now(config('app.timezone'))->timestamp;
        //sms_payment
        $sms_payment = new sms_payment();
        $sms_payment->operator_id = $operator->id;
        $sms_payment->merchant_id = $sms_bill->merchant_id;
        $sms_payment->sms_bill_id = $sms_bill->id;
        $sms_payment->payment_gateway_id = $payment_gateway->id;
        $sms_payment->payment_gateway_name = $payment_gateway->provider_name;
        $sms_payment->type = 'Online';
        $sms_payment->sms_count = $sms_bill->sms_count;
        $sms_payment->sms_cost = $sms_bill->sms_cost;
        $sms_payment->amount_paid = $sms_bill->sms_cost;
        $sms_payment->mer_txnid = $mer_txnid;
        $sms_payment->date = date(config('app.date_format'));
        $sms_payment->week = date(config('app.week_format'));
        $sms_payment->month = date(config('app.month_format'));
        $sms_payment->year = date(config('app.year_format'));
        $sms_payment->save();

        return SmsPaymentRouteController::smsPgwRoute($payment_gateway, $sms_payment);
    }


    /**
     * Recheck the Pending Payment Status.
     *
     * @param \App\Models\sms_payment $sms_payment
     * @return \Illuminate\Http\Response
     */

    public static function recheckPayment(sms_payment $sms_payment)
    {

        if ($sms_payment->payment_gateway_id == 0) {
            $sms_payment->delete();
            return redirect()->route('sms_payments.index')->with('error', 'Payment Was Failed');
        }

        $payment_gateway = payment_gateway::findOrFail($sms_payment->payment_gateway_id);

        $status = 0;

        switch ($payment_gateway->provider_name) {
            case 'easypayway':
                $controller = new EasypaywayTransactionController();
                $status = $controller->recheckSmsPayment($sms_payment);
                break;
            case 'sslcommerz':
                $controller = new SslcommerzTransactionController();
                $status = $controller->recheckSmsPayment($sms_payment);
                break;
            case 'bkash_checkout':
                $controller = new BkashCheckoutController();
                $status = $controller->recheckSmsPayment($sms_payment);
                break;
            case 'nagad':
                $controller = new NagadPaymentGatewayController();
                $status = $controller->recheckSmsPayment($sms_payment);
                break;
            case 'bkash_tokenized_checkout':
                $controller = new bKashTokenizedSmsPaymentController();
                $status = $controller->recheckPayment($sms_payment);
                break;
            case 'shurjopay':
                $controller = new ShurjoPaySmsPaymentController();
                $status = $controller->verifyPayment($sms_payment);
                break;
        }

        if ($status) {
            return redirect()->route('sms_payments.index')->with('success', 'Payment was Successfull');
        } else {
            return redirect()->route('sms_payments.index')->with('error', 'Payment Was Failed');
        }
    }
}
