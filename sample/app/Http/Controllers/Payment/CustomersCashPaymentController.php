<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\BillingHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomerBillController;
use App\Http\Controllers\CustomerPayment\CustomersPaymentProcessController;
use App\Http\Controllers\PackageController;
use App\Models\billing_profile;
use App\Models\customer_bill;
use App\Models\Freeradius\customer;
use App\Models\customer_payment;
use App\Models\package;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomersCashPaymentController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, customer_bill $customer_bill)
    {

        $operator = $request->user();

        $customer = customer::findOrFail($customer_bill->customer_id);

        $billing_profile = billing_profile::findOrFail($customer->billing_profile_id);

        $package = package::findOrFail($customer_bill->package_id);

        $package_price = PackageController::price($customer, $package);

        $validity = BillingHelper::getBillValidity($customer_bill);

        $operators_amount = $customer_bill->operator_amount;

        $customers_amount = $customer_bill->amount;

        $period_stop = BillingHelper::stoppingDate($customer, $validity);

        $invoice = collect([
            'package_name' => $package->name,
            'package_price' => $package_price,
            'customers_amount' => $customers_amount,
            'operators_amount' => $operators_amount,
            'validity' => $validity,
            'next_payment_date' => $period_stop,
        ]);

        switch ($operator->role) {

            case 'group_admin':
                return view('admins.group_admin.customers-cash-payment', [
                    'customer_bill' => $customer_bill,
                    'invoice' => $invoice,
                    'billing_profile' => $billing_profile,
                ]);
                break;

            case 'operator':
                return view('admins.operator.customers-cash-payment', [
                    'customer_bill' => $customer_bill,
                    'invoice' => $invoice,
                    'billing_profile' => $billing_profile,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.customers-cash-payment', [
                    'customer_bill' => $customer_bill,
                    'invoice' => $invoice,
                    'billing_profile' => $billing_profile,
                ]);
                break;

            case 'manager':
                return view('admins.manager.customers-cash-payment', [
                    'customer_bill' => $customer_bill,
                    'invoice' => $invoice,
                    'billing_profile' => $billing_profile,
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
    public function store(Request $request, customer_bill $customer_bill)
    {
        $this->authorize('receivePayment', [$customer_bill]);

        $request->validate([
            'date' => 'required|string',
            'amount_paid' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'note' => 'string|nullable|max:254',
        ]);

        if ($request->filled('amount_paid')) {
            $amount_paid = $request->amount_paid;
        } else {
            $amount_paid = $customer_bill->amount;
        }

        if ($request->filled('discount')) {
            if ($request->user()->can('discount', $customer_bill)) {
                $discount = $request->discount;
            } else {
                $discount = 0;
            }
        } else {
            $discount = 0;
        }

        $customer_payment = new customer_payment();
        $customer_payment->mgid = $customer_bill->mgid;
        $customer_payment->gid = $customer_bill->gid;
        $customer_payment->operator_id = $customer_bill->operator_id;
        $customer_payment->cash_collector_id = $request->user()->id;
        $customer_payment->customer_id = $customer_bill->customer_id;
        $customer_payment->customer_bill_id = $customer_bill->id;
        $customer_payment->package_id = $customer_bill->package_id;
        $customer_payment->payment_gateway_name = 'Cash';
        $customer_payment->mobile = $customer_bill->mobile;
        $customer_payment->name = $customer_bill->name;
        $customer_payment->username = $customer_bill->username;
        $customer_payment->type = 'Cash';
        $customer_payment->payment_mode = 'prepaid';
        $customer_payment->pay_status = 'Successful';
        $customer_payment->amount_paid = $amount_paid;
        $customer_payment->store_amount = $amount_paid;
        $customer_payment->discount = $discount;
        $customer_payment->mer_txnid = Carbon::now(config('app.timezone'))->timestamp;
        $customer_payment->date = date_format(date_create($request->date), config('app.date_format'));
        $customer_payment->week = date_format(date_create($request->date), config('app.week_format'));
        $customer_payment->month = date_format(date_create($request->date), config('app.month_format'));
        $customer_payment->year = date_format(date_create($request->date), config('app.year_format'));
        $customer_payment->used = 0;
        $customer_payment->require_accounting = 1;
        $customer_payment->note = $request->note;
        $customer_payment->purpose = $customer_bill->purpose;
        if ($request->filled('require_sms_notice')) {
            $customer_payment->require_sms_notice = 1;
        } else {
            $customer_payment->require_sms_notice = 0;
        }
        if ($request->filled('dnb_operator')) {
            $customer_payment->dnb_operator = 1;
        } else {
            $customer_payment->dnb_operator = 0;
        }
        $customer_payment->save();

        // process payment
        $customer_bill->processing = 1;
        $customer_bill->save();
        CustomersPaymentProcessController::store($customer_payment);

        // New Bill
        $total_paid = $amount_paid + $discount;

        if ($customer_bill->amount > $total_paid) {
            $new_bill = new customer_bill();
            $new_bill->mgid = $customer_bill->mgid;
            $new_bill->gid = $customer_bill->gid;
            $new_bill->operator_id = $customer_bill->operator_id;
            $new_bill->parent_customer_id = $customer_bill->parent_customer_id;
            $new_bill->customer_id = $customer_bill->customer_id;
            $new_bill->package_id = $customer_bill->package_id;
            $new_bill->customer_zone_id = $customer_bill->customer_zone_id;
            $new_bill->name = $customer_bill->name;
            $new_bill->mobile = $customer_bill->mobile;
            $new_bill->username = $customer_bill->username;
            $new_bill->amount = round($customer_bill->amount - $total_paid);
            $new_bill->currency = $customer_bill->currency;
            $new_bill->description = $customer_bill->description;
            $new_bill->billing_period = $customer_bill->billing_period;
            $new_bill->due_date = $customer_bill->due_date;
            $new_bill->purpose = $customer_bill->purpose;
            $new_bill->year = $customer_bill->year;
            $new_bill->month = $customer_bill->month;
            $new_bill->remark = $total_paid . ' ' . config('consumer.currency') . ' paid on ' . date(config('app.date_format'));
            $new_bill->save();
            $new_bill->operator_amount = CustomerBillController::operatorAmount($new_bill);
            $new_bill->validity_period = BillingHelper::getBillValidity($new_bill);
            $new_bill->save();
        }

        return redirect()->route('customer_bills.index')->with('success', 'Payment Received!');
    }
}
