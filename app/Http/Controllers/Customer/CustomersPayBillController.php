<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\CacheController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Payment\CustomerPaymentGatewayRouteController;
use App\Models\customer_bill;
use App\Models\customer_payment;
use App\Models\payment_gateway;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomersPayBillController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\customer_bill  $customer_bill
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, customer_bill $customer_bill)
    {
        $request->validate([
            'payment_gateway_id' => 'required'
        ]);
        $payment_gateway = payment_gateway::findOrFail($request->payment_gateway_id);
        $all_customer = $request->user('customer');
        $customer = CacheController::getCustomerUseAllCustomer($all_customer);
        $mer_txnid = random_int(1000, 9999) . Carbon::now(config('app.timezone'))->timestamp;

        //customer_payment
        $customer_payment = new customer_payment();
        $customer_payment->mgid = $customer->mgid;
        $customer_payment->gid = $customer->gid;
        $customer_payment->operator_id = $customer->operator_id;
        $customer_payment->customer_id = $customer->id;
        $customer_payment->customer_bill_id = $customer_bill->id;
        $customer_payment->package_id = $customer_bill->package_id;
        $customer_payment->validity_period = $customer_bill->validity_period;
        $customer_payment->payment_gateway_id = $payment_gateway->id;
        $customer_payment->payment_gateway_name = $payment_gateway->provider_name;
        $customer_payment->mobile = $customer->mobile;
        $customer_payment->name = $customer->name;
        $customer_payment->username = $customer->username;
        $customer_payment->type = 'Online';
        $customer_payment->payment_mode = 'prepaid';
        $customer_payment->amount_paid = $customer_bill->amount;
        $customer_payment->mer_txnid = $mer_txnid;
        $customer_payment->date = date(config('app.date_format'));
        $customer_payment->week = date(config('app.week_format'));
        $customer_payment->month = date(config('app.month_format'));
        $customer_payment->year = date(config('app.year_format'));
        $customer_payment->require_accounting = 1;
        $customer_payment->purpose = $customer_bill->purpose;
        $customer_payment->save();

        return CustomerPaymentGatewayRouteController::pgwRoute($payment_gateway, $customer_payment);
    }
}
