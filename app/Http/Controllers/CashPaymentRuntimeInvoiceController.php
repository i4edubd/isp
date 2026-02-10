<?php

namespace App\Http\Controllers;

use App\Models\customer_bill;
use App\Models\Freeradius\customer;
use App\Models\package;
use Illuminate\Http\Request;

class CashPaymentRuntimeInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\customer_bill  $customer_bill
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, customer_bill $customer_bill)
    {
        $request->validate([
            'amount_paid' => 'required|numeric|min:1',
            'discount' => 'required|numeric',
        ]);

        $amount_paid = $request->amount_paid;

        $discount = $request->discount;

        $customer = customer::findOrFail($customer_bill->customer_id);

        $package = package::findOrFail($customer_bill->package_id);

        $master_package = $package->master_package;

        $package_price = PackageController::price($customer, $package);

        $validity = round(($master_package->validity / $package_price) * $amount_paid);

        if ($discount) {
            $discount_validity = round(($master_package->validity / $package_price) * $discount);
        } else {
            $discount_validity = 0;
        }

        $validity = $validity + $discount_validity;

        $operators_amount = round(($package->operator_price / $master_package->validity) * $validity);

        $period_stop = BillingHelper::stoppingDate($customer, $validity);

        $total_paid = $amount_paid + $discount;

        if ($customer_bill->amount > $total_paid) {
            $amount_due = $customer_bill->amount - $total_paid;
        } else {
            $amount_due = 0;
        }

        $invoice = collect([
            'package_name' => $package->name,
            'package_price' => $package_price,
            'discount' => $discount,
            'customers_amount' => $amount_paid,
            'operators_amount' => $operators_amount,
            'validity' => $validity,
            'next_payment_date' => $period_stop,
            'amount_due' => $amount_due,
        ]);

        return view('admins.components.runtime-invoice', [
            'invoice' => $invoice,
        ]);
    }
}
