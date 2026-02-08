<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CustomerPayment\CustomerPaymentAccountingController;
use App\Models\customer_payment;
use Illuminate\Http\Request;

class CustomerPaymentDestroyController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\customer_payment  $customer_payment
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, customer_payment $customer_payment)
    {
        $this->authorize('delete', $customer_payment);

        $operator = $request->user();

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.customer-payment-delete', [
                    'customer_payment' => $customer_payment,
                ]);
                break;

            case 'operator':
                return view('admins.operator.customer-payment-delete', [
                    'customer_payment' => $customer_payment,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.customer-payment-delete', [
                    'customer_payment' => $customer_payment,
                ]);
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Models\customer_payment  $customer_payment
     * @return \Illuminate\Http\Response
     */
    public function store(customer_payment $customer_payment)
    {
        $this->authorize('delete', $customer_payment);

        $new_amount =  0 - $customer_payment->amount_paid;

        $new_payment = $customer_payment->replicate()
            ->fill([
                'amount_paid' => $new_amount,
                'store_amount' => $new_amount,
                'transaction_fee' => 0,
                'type' => 'adjustment',
            ]);
        $new_payment->save();

        // delete payment
        $customer_payment->delete();

        $controller = new CustomerPaymentAccountingController();
        $controller->distributePaymet($new_payment);

        return redirect()->route('customer_payments.index', ['refresh' => 1, 'operator_id' => $customer_payment->operator_id])->with('success', 'Accounts adjusted accordingly');
    }
}
