<?php

namespace App\Http\Controllers\CustomerPayment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Payment\BkashCheckoutController;
use App\Http\Controllers\Payment\EasypaywayTransactionController;
use App\Http\Controllers\Payment\NagadPaymentGatewayController;
use App\Http\Controllers\Payment\SslcommerzTransactionController;
use App\Http\Controllers\Payment\WalletMixGatewayController;
use App\Models\customer_payment;
use App\Models\operator;
use App\Models\payment_gateway;
use Illuminate\Http\Request;

class CustomersPendingPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //operator
        $operator = $request->user();

        // where
        $where = [];

        // operator_id
        if ($request->filled('operator_id')) {
            $where[] = ['operator_id', '=', $request->operator_id];
        } else {
            $where[] = ['operator_id', '=', $operator->id];
        }

        // pay_status
        $where[] = ['pay_status', '!=', 'Successful'];

        //payment_gateway_name
        $where[] = ['payment_gateway_name', '!=', 'send_money'];

        // mobile
        if ($request->filled('mobile')) {
            $where[] = ['mobile', '=', $request->mobile];
        }

        $payments = customer_payment::where($where)->paginate(15);

        switch ($operator->role) {

            case 'super_admin':
                $operators = operator::where('gid', $operator->gid)->get();
                return view('admins.super_admin.customers-pending-payments', [
                    'payments' => $payments,
                    'operators' => $operators,
                ]);
                break;

            case 'group_admin':
                $operators = operator::where('gid', $operator->gid)->get();
                return view('admins.group_admin.customers-pending-payments', [
                    'payments' => $payments,
                    'operators' => $operators,
                ]);
                break;

            case 'operator':
                return view('admins.operator.customers-pending-payments', [
                    'payments' => $payments,
                    'operators' => 0,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.customers-pending-payments', [
                    'payments' => $payments,
                    'operators' => 0,
                ]);
                break;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\customer_payment  $customer_payment
     * @return \Illuminate\Http\Response
     */
    public function update(customer_payment $customer_payment)
    {

        if ($customer_payment->payment_gateway_id == 0) {

            $customer_payment->delete();

            return redirect()->route('customers-pending-payments.index')->with('error', 'Payment Was Failed');
        }

        $payment_gateway = payment_gateway::findOrFail($customer_payment->payment_gateway_id);

        $status = 0;

        switch ($payment_gateway->provider_name) {
            case 'easypayway':
                $controller = new EasypaywayTransactionController();
                $status = $controller->recheckCustomerPayment($customer_payment);
                break;
            case 'sslcommerz':
                $controller = new SslcommerzTransactionController();
                $status = $controller->recheckCustomerPayment($customer_payment);
                break;
            case 'bkash_checkout':
                $controller = new BkashCheckoutController();
                $status = $controller->recheckCustomerPayment($customer_payment);
                break;
            case 'nagad':
                $controller = new NagadPaymentGatewayController();
                $status = $controller->recheckCustomerPayment($customer_payment);
                break;
            case 'walletmix':
                $controller = new WalletMixGatewayController();
                $status = $controller->recheckCustomerPayment($customer_payment);
                break;
        }

        if ($status) {
            return redirect()->route('customers-pending-payments.index')->with('success', 'Payment was Successfull');
        } else {
            return redirect()->route('customers-pending-payments.index')->with('error', 'Payment Was Failed');
        }
    }
}
