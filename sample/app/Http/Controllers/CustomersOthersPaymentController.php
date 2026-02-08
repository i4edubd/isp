<?php

namespace App\Http\Controllers;

use App\Http\Controllers\enum\PaymentPurpose;
use App\Models\customer_payment;
use App\Models\Freeradius\customer;
use App\Models\operators_income;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomersOthersPaymentController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, customer $customer)
    {
        $operator = $request->user();

        switch ($operator->role) {

            case 'group_admin':
                return view('admins.group_admin.customers-others-payment', [
                    'customer' => $customer,
                ]);
                break;

            case 'operator':
                return view('admins.operator.customers-others-payment', [
                    'customer' => $customer,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.customers-others-payment', [
                    'customer' => $customer,
                ]);
                break;

            case 'manager':
                return view('admins.manager.customers-others-payment', [
                    'customer' => $customer,
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
    public function store(Request $request, customer $customer)
    {

        $customer_payment = new customer_payment();
        $customer_payment->mgid = $customer->mgid;
        $customer_payment->gid = $customer->gid;
        $customer_payment->operator_id  = $customer->operator_id;
        $customer_payment->customer_id = $customer->id;
        $customer_payment->package_id = 0;
        $customer_payment->validity_period = 0;
        $customer_payment->gid = $customer->gid;
        $customer_payment->payment_gateway_name = "Cash";
        $customer_payment->mobile = $customer->mobile;
        $customer_payment->name = $customer->name;
        $customer_payment->username = $customer->username;
        $customer_payment->type = 'Cash';
        $customer_payment->pay_status = 'Successful';
        $customer_payment->amount_paid = $request->amount_paid;
        $customer_payment->store_amount = $request->amount_paid;
        $customer_payment->mer_txnid = Carbon::now(config('app.timezone'))->timestamp;
        $customer_payment->date = date(config('app.date_format'));
        $customer_payment->week = date(config('app.week_format'));
        $customer_payment->month = date(config('app.month_format'));
        $customer_payment->year = date(config('app.year_format'));
        $customer_payment->used = 1;
        $customer_payment->note = $request->note;
        $customer_payment->purpose = PaymentPurpose::OTHER_PAYMENT->value;
        $customer_payment->save();

        $operators_income = new operators_income();
        $operators_income->operator_id = $customer->operator_id;
        $operators_income->payment_id = $customer_payment->id;
        $operators_income->source_operator_id = $customer->operator_id;
        $operators_income->source = 'customers_payment';
        $operators_income->amount = $request->amount_paid;
        $operators_income->date = date(config('app.date_format'));
        $operators_income->week = date(config('app.week_format'));
        $operators_income->month = date(config('app.month_format'));
        $operators_income->year = date(config('app.year_format'));
        $operators_income->save();

        return redirect()->route('customers.index')->with('success', 'Payment recorded successfully!');
    }
}
