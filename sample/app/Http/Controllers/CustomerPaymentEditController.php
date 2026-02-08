<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CustomerPayment\CustomerPaymentAccountingController;
use App\Models\customer_payment;
use Illuminate\Http\Request;

class CustomerPaymentEditController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\customer_payment  $customer_payment
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, customer_payment $customer_payment)
    {
        $this->authorize('update', $customer_payment);

        $operator = $request->user();

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.customer-payment-edit', [
                    'customer_payment' => $customer_payment,
                ]);
                break;

            case 'operator':
                return view('admins.operator.customer-payment-edit', [
                    'customer_payment' => $customer_payment,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.customer-payment-edit', [
                    'customer_payment' => $customer_payment,
                ]);
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\customer_payment  $customer_payment
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, customer_payment $customer_payment)
    {
        $this->authorize('update', $customer_payment);

        $request->validate([
            'amount_paid' => 'required|numeric',
        ]);

        if ($customer_payment->amount_paid == $request->amount_paid) {
            return redirect()->route('customer_payments.index')->with('success', 'No changes made!');
        }

        // if request amount paid is less new amount will be negative.
        // if request amount paid is greater new amount will be positive.
        $new_amount = $request->amount_paid - $customer_payment->amount_paid;

        $new_payment = $customer_payment->replicate()
            ->fill([
                'amount_paid' => $new_amount,
                'store_amount' => $new_amount,
                'transaction_fee' => 0,
                'type' => 'adjustment',
            ]);
        $new_payment->save();

        // update payment
        $customer_payment->amount_paid = $request->amount_paid;
        $customer_payment->store_amount = $request->amount_paid;
        $customer_payment->transaction_fee = 0;
        $customer_payment->save();

        $controller = new CustomerPaymentAccountingController();
        $controller->distributePaymet($new_payment);

        return redirect()->route('customer_payments.index', ['refresh' => 1, 'operator_id' => $customer_payment->operator_id])->with('success', 'Accounts adjusted accordingly');
    }
}
