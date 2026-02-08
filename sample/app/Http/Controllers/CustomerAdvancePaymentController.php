<?php

namespace App\Http\Controllers;

use App\Models\Freeradius\customer;
use Illuminate\Http\Request;

class CustomerAdvancePaymentController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, customer $customer)
    {
        $this->authorize('advancePayment', $customer);

        $operator = $request->user();

        switch ($operator->role) {

            case 'group_admin':
                return view('admins.group_admin.customers-advance-payment-create', [
                    'customer' => $customer,
                ]);
                break;

            case 'operator':
                return view('admins.operator.customers-advance-payment-create', [
                    'customer'  => $customer,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.customers-advance-payment-create', [
                    'customer'  => $customer,
                ]);
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, customer $customer)
    {
        $this->authorize('advancePayment', $customer);

        $request->validate([
            'new_payment' => 'required|numeric',
        ]);

        $new_advance_payment = $customer->advance_payment + $request->new_payment;

        $new_payment = $new_advance_payment > 0 ? $new_advance_payment : 0;

        $customer->advance_payment = $new_payment;
        $customer->save();

        return redirect()->route('customers.index')->with('success', 'advance payment saved successfully!');
    }
}
