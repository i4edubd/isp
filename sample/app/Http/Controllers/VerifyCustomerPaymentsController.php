<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CustomerPayment\CustomersPaymentProcessController;
use App\Models\customer_payment;
use Illuminate\Http\Request;

class VerifyCustomerPaymentsController extends Controller
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

        $payments = customer_payment::where('pay_status', 'Pending')
            ->where('operator_id', $operator->id)
            ->where(function ($query) {
                $query->where('payment_gateway_name', 'send_money')
                    ->orWhere('payment_gateway_name', 'bkash_payment');
            })
            ->orderBy('id', 'desc')->paginate(10);

        switch ($operator->role) {

            case 'group_admin':
                return view('admins.group_admin.verify-customers-payments', [
                    'payments' => $payments,
                ]);
                break;

            case 'operator':
                return view('admins.operator.verify-customers-payments', [
                    'payments' => $payments,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.verify-customers-payments', [
                    'payments' => $payments,
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
    public function update(Request $request, customer_payment $customer_payment)
    {
        $request->validate([
            'action' => 'required|in:accept,reject',
        ]);

        if ($request->user()->id !== $customer_payment->operator_id) {
            abort(403);
        }

        if ($request->action == 'reject') {
            $customer_payment->delete();
            return redirect()->route('verify-send-money.index')->with('success', 'Payment Rejected!');
        }

        $customer_payment->store_amount = $customer_payment->amount_paid;
        $customer_payment->pay_status = 'Successful';
        $customer_payment->save();

        CustomersPaymentProcessController::store($customer_payment);

        return redirect()->route('verify-send-money.index')->with('success', 'Payment Accepted!');
    }
}
