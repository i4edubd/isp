<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CustomerPayment\CustomersPaymentProcessController;
use App\Http\Controllers\enum\BillingTerms;
use App\Http\Controllers\enum\PaymentPurpose;
use App\Models\customer_payment;
use App\Models\Freeradius\customer;
use App\Models\operator;
use App\Models\package;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DailyBillingPackageChangeController extends Controller
{

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, customer $customer)
    {
        $this->authorize('dailyRecharge', $customer);

        $operator = $request->user();
        $active_package = CacheController::getPackage($customer->package_id);
        $billing_profile = CacheController::getBillingProfile($customer->billing_profile_id);

        // packages
        $packages = $operator->packages->where('name', '!=', 'Trial');
        $connection_type = $customer->connection_type;
        $packages = $packages->filter(function ($package) use ($connection_type) {
            return $package->master_package->connection_type == $connection_type;
        });
        $packages = $packages->where('id', '!=', $active_package->id);

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.daily-billing-package-change', [
                    'customer' => $customer,
                    'packages' => $packages,
                    'billing_profile' => $billing_profile,
                    'active_package' => $active_package,
                ]);
                break;

            case 'operator':
                return view('admins.operator.daily-billing-package-change', [
                    'customer' => $customer,
                    'packages' => $packages,
                    'billing_profile' => $billing_profile,
                    'active_package' => $active_package,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.daily-billing-package-change', [
                    'customer' => $customer,
                    'packages' => $packages,
                    'billing_profile' => $billing_profile,
                    'active_package' => $active_package,
                ]);
                break;

            case 'manager':
                return view('admins.manager.daily-billing-package-change', [
                    'customer' => $customer,
                    'packages' => $packages,
                    'billing_profile' => $billing_profile,
                    'active_package' => $active_package,
                ]);
                break;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, customer $customer)
    {
        $this->authorize('dailyRecharge', $customer);

        $package = package::findOrFail($request->package_id);

        Gate::forUser($customer)->authorize('usePackage', [$package]);

        // Same package
        if ($customer->package_id == $package->id) {
            return redirect()->route('customers.index')->with('info', 'Same package');
        }

        $now = Carbon::now(getTimeZone($customer->operator_id));
        $expired_at = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($customer->operator_id), 'en');

        if ($expired_at->lessThan($now)) { // expired
            $invoice = BillingHelper::getRuntimeInvoice($customer, $package, BillingTerms::INTERVAL_UNIT_MINUTE->value, $package->master_package->total_minute);
        } else {
            $validity_minute = $now->diffInMinutes($expired_at);
            $invoice = BillingHelper::getRuntimeInvoice($customer, $package, BillingTerms::INTERVAL_UNIT_MINUTE->value, $validity_minute);
        }

        // authorization
        $operator = operator::find($customer->operator_id);
        if (Gate::forUser($operator)->denies('recharge', [$invoice->get('operators_payable_amount')])) {
            return redirect()->route('customers.index')->with('error', 'The balance of the operator account is less');
        }

        $customer_payment = new customer_payment();
        $customer_payment->mgid = $customer->mgid;
        $customer_payment->gid = $customer->gid;
        $customer_payment->operator_id = $customer->operator_id;
        $customer_payment->cash_collector_id = $request->user()->id;
        $customer_payment->customer_id = $customer->id;
        $customer_payment->customer_bill_id = 0;
        $customer_payment->package_id = $package->id;
        $customer_payment->validity_period = $invoice->get('interval_count');
        $customer_payment->previous_package_id = $customer->package_id;
        $customer_payment->payment_gateway_id = 0;
        $customer_payment->payment_gateway_name = 'Cash';
        $customer_payment->mobile = $customer->mobile;
        $customer_payment->name = $customer->name;
        $customer_payment->username = $customer->username;
        $customer_payment->type = 'Cash';
        $customer_payment->payment_mode = 'prepaid';
        $customer_payment->pay_status = 'Successful';
        $customer_payment->amount_paid = $invoice->get('customers_payable_amount');
        $customer_payment->store_amount = $invoice->get('customers_payable_amount');
        $customer_payment->transaction_fee = 0;
        $customer_payment->discount = 0;
        $customer_payment->mer_txnid = Carbon::now(config('app.timezone'))->timestamp;
        $customer_payment->date = date_format(date_create($request->date), config('app.date_format'));
        $customer_payment->week = date_format(date_create($request->date), config('app.week_format'));
        $customer_payment->month = date_format(date_create($request->date), config('app.month_format'));
        $customer_payment->year = date_format(date_create($request->date), config('app.year_format'));
        $customer_payment->used = 0;
        $customer_payment->require_sms_notice = 0;
        $customer_payment->package_started_at = $invoice->get('package_started_at');
        $customer_payment->package_expired_at = $invoice->get('package_expired_at');
        if ($invoice->get('customers_payable_amount') > 0) {
            $customer_payment->purpose = PaymentPurpose::PACKAGE_UPGRADE->value;
        } else {
            $customer_payment->purpose = PaymentPurpose::PACKAGE_DOWNGRADE->value;
        }
        $customer_payment->save();

        CustomersPaymentProcessController::store($customer_payment);

        return redirect()->route('customers.index')->with('success', 'Package Change successful!');
    }

    /**
     * Run time Invoice
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function runtimeInvoice(customer $customer, package $package)
    {
        // Same Package
        if ($customer->package_id == $package->id) {
            $invoice = BillingHelper::getRuntimeInvoice($customer, $package, BillingTerms::INTERVAL_UNIT_MINUTE->value, 0);
            return view('admins.components.runtime-invoice-v2', [
                'invoice' => $invoice,
            ]);
        }

        // expired
        $now = Carbon::now(getTimeZone($customer->operator_id));
        $expired_at = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($customer->operator_id), 'en');
        if ($expired_at->lessThan($now)) {
            $invoice = BillingHelper::getRuntimeInvoice($customer, $package, BillingTerms::INTERVAL_UNIT_MINUTE->value, $package->master_package->total_minute);
            return view('admins.components.runtime-invoice-v2', [
                'invoice' => $invoice,
            ]);
        }

        // invoice
        $validity_minute = $now->diffInMinutes($expired_at);
        $invoice = BillingHelper::getRuntimeInvoice($customer, $package, BillingTerms::INTERVAL_UNIT_MINUTE->value, $validity_minute);
        return view('admins.components.runtime-invoice-v2', [
            'invoice' => $invoice,
        ]);
    }
}
