<?php

namespace App\Http\Controllers\Sms;

use App\Http\Controllers\Controller;
use App\Models\payment_gateway;
use App\Models\sms_bill;
use App\Models\sms_gateway;
use App\Models\sms_payment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdvanceSmsPaymentController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $operator = $request->user();

        // sms_bill
        $has_bill = sms_bill::where('operator_id', $operator->id)->count();

        if ($has_bill) {
            return redirect()->route('sms_bills.index');
        }

        // merchant
        $merchant = sms_gateway::where('operator_id', $operator->id)->count();

        if ($merchant) {
            return redirect(url()->previous())->with('success', 'You do not need SMS balance!');
        }

        // sms gateway
        $c = new SmsGatewayController();
        $sms_gateway = $c->getSmsGw($operator);

        if ($sms_gateway->id == 0) {
            return redirect(url()->previous())->with('error', 'SMS Gateway Not Found!');
        }

        // payment_gateways
        $payment_gateways = payment_gateway::where('operator_id', $sms_gateway->operator_id)->get();

        if ($payment_gateways->count() == 0) {
            return redirect(url()->previous())->with('error', 'Payment Gateway Not Found!');
        }

        switch ($operator->role) {

            case 'group_admin':
                return view('admins.group_admin.advance-sms-payment', [
                    'payment_gateways' => $payment_gateways,
                ]);
                break;

            case 'operator':
                return view('admins.operator.advance-sms-payment', [
                    'payment_gateways' => $payment_gateways,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.advance-sms-payment', [
                    'payment_gateways' => $payment_gateways,
                ]);
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:50',
            'payment_gateway_id' => 'required|numeric',
        ]);

        $operator = $request->user();

        //payment_gateway
        $payment_gateway = payment_gateway::findOrFail($request->payment_gateway_id);
        //mer_txnid
        $mer_txnid = 'sms_' . random_int(1000, 9999) . Carbon::now(config('app.timezone'))->timestamp;
        //sms_payment
        $sms_payment = new sms_payment();
        $sms_payment->operator_id = $operator->id;
        $sms_payment->merchant_id = $payment_gateway->operator_id;
        $sms_payment->sms_bill_id = 0;
        $sms_payment->payment_gateway_id = $payment_gateway->id;
        $sms_payment->payment_gateway_name = $payment_gateway->provider_name;
        $sms_payment->pay_for = 'balance';
        $sms_payment->used = 0;
        $sms_payment->type = 'Online';
        $sms_payment->pay_status = 'Pending';
        $sms_payment->sms_count = 0;
        $sms_payment->sms_cost = $request->amount;
        $sms_payment->amount_paid = $request->amount;
        $sms_payment->mer_txnid = $mer_txnid;
        $sms_payment->date = date(config('app.date_format'));
        $sms_payment->week = date(config('app.week_format'));
        $sms_payment->month = date(config('app.month_format'));
        $sms_payment->year = date(config('app.year_format'));
        $sms_payment->save();

        return SmsPaymentRouteController::smsPgwRoute($payment_gateway, $sms_payment);
    }
}
