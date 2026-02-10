<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\BillingHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomerPayment\CustomersPaymentProcessController;
use App\Http\Controllers\enum\BillingTerms;
use App\Http\Controllers\enum\PaymentPurpose;
use App\Http\Controllers\PPPCustomerDisconnectController;
use App\Models\billing_profile;
use App\Models\customer_bill;
use App\Models\customer_payment;
use App\Models\Freeradius\customer;
use App\Models\operator;
use App\Models\package;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CustomerPackageChangeController extends Controller
{

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, customer $customer)
    {
        $this->authorize('changePackage', $customer);

        $operator = $request->user();
        $billing_profile = billing_profile::where('id', $customer->billing_profile_id)->firstOrFail();
        $active_package = package::where('id', $customer->package_id)->firstOrFail();

        // packages
        $packages = $operator->packages->where('name', '!=', 'Trial');
        $connection_type = $customer->connection_type;
        $packages = $packages->filter(function ($package) use ($connection_type) {
            return $package->master_package->connection_type == $connection_type;
        });
        $packages = $packages->where('id', '!=', $active_package->id);

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.customers-package-edit', [
                    'customer' => $customer,
                    'packages' => $packages,
                    'billing_profile' => $billing_profile,
                    'active_package' => $active_package,
                ]);
                break;

            case 'operator':
                return view('admins.operator.customers-package-edit', [
                    'customer' => $customer,
                    'packages' => $packages,
                    'billing_profile' => $billing_profile,
                    'active_package' => $active_package,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.customers-package-edit', [
                    'customer' => $customer,
                    'packages' => $packages,
                    'billing_profile' => $billing_profile,
                    'active_package' => $active_package,
                ]);
                break;

            case 'manager':
                return view('admins.manager.customers-package-edit', [
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
        $request->validate([
            'package_id' => 'required|numeric',
        ]);

        $this->authorize('changePackage', $customer);

        $package = package::findOrFail($request->package_id);

        Gate::forUser($customer)->authorize('usePackage', [$package]);

        // Same package
        if ($customer->package_id == $package->id) {
            return redirect()->route('customers.index')->with('info', 'Same package');
        }

        // invoice
        $billing_profile = billing_profile::where('id', $customer->billing_profile_id)->firstOrFail();
        $period_start = BillingHelper::getStartingDate($customer, $package);
        $period_stop = Carbon::createFromFormat(config('app.date_format'), $billing_profile->next_payment_date, getTimeZone($customer->operator_id))->isoFormat(config('app.expiry_time_format'));
        $validity = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $period_start, getTimeZone($customer->operator_id), 'en')->diffInDays(Carbon::createFromIsoFormat(config('app.expiry_time_format'), $period_stop, getTimeZone($customer->operator_id), 'en'));
        $invoice = BillingHelper::getRuntimeInvoice($customer, $package, BillingTerms::INTERVAL_UNIT_DAY->value, $validity);

        // submit_payment ?
        $submit_payment = 'yes';
        if ($request->filled('submit_payment')) {
            if ($request->submit_payment == 'no') {
                $submit_payment = 'no';
            }
        }

        // authorization
        $operator = operator::find($customer->operator_id);
        if ($submit_payment == 'yes') {
            if (Gate::forUser($operator)->denies('recharge', [$invoice->get('operators_payable_amount')])) {
                return redirect()->route('customers.index')->with('error', 'The balance of the operator account is less');
            }
        }

        // change Package
        $customer->package_expired_at = Carbon::now(getTimeZone($customer->operator_id))->isoFormat(config('app.expiry_time_format'));
        $customer->exptimestamp = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($customer->operator_id), 'en')->timestamp;
        $customer->save();
        CustomerPackageUpdateController::update($customer, $package);

        // Delete bills for this month.
        self::deleteBills($customer);

        // payment
        if (($invoice->get('customers_payable_amount') > 0 && $submit_payment == 'yes') || $invoice->get('customers_payable_amount') < 0) {
            $customer_payment = new customer_payment();
            $customer_payment->mgid = $customer->mgid;
            $customer_payment->gid = $customer->gid;
            $customer_payment->operator_id = $customer->operator_id;
            $customer_payment->customer_id = $customer->id;
            $customer_payment->customer_bill_id = 0;
            $customer_payment->package_id = $package->id;
            $customer_payment->validity_period = $invoice->get('interval_count');
            $customer_payment->payment_gateway_name = 'Cash';
            $customer_payment->mobile = $customer->mobile;
            $customer_payment->name = $customer->name;
            $customer_payment->username = $customer->username;
            $customer_payment->type = 'Cash';
            $customer_payment->pay_status = 'Successful';
            $customer_payment->amount_paid = $invoice->get('customers_payable_amount');
            $customer_payment->store_amount = $invoice->get('customers_payable_amount');
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
        }

        // bill
        if ($invoice->get('customers_payable_amount') > 0 && $submit_payment == 'no') {
            if ($invoice->get('interval_count') > 1) {
                $customer_bill = new customer_bill();
                $customer_bill->mgid = $customer->mgid;
                $customer_bill->gid = $customer->gid;
                $customer_bill->operator_id = $customer->operator_id;
                $customer_bill->parent_customer_id = $customer->parent_id;
                $customer_bill->customer_id = $customer->id;
                $customer_bill->package_id = $package->id;
                $customer_bill->validity_period = $validity;
                $customer_bill->customer_zone_id = $customer->zone_id;
                $customer_bill->name = $customer->name;
                $customer_bill->mobile = $customer->mobile;
                $customer_bill->username = $customer->username;
                $customer_bill->amount = $invoice->get('customers_payable_amount');
                $customer_bill->operator_amount = $invoice->get('operators_payable_amount');
                $customer_bill->currency = $invoice->get('currency');
                $customer_bill->description = $package->name;
                $customer_bill->billing_period = $invoice->get('bill_period');
                $customer_bill->due_date = BillingHelper::dueDate($customer);
                $customer_bill->purpose = PaymentPurpose::PACKAGE_CHANGE->value;
                $customer_bill->year = date(config('app.year_format'));
                $customer_bill->month = date(config('app.month_format'));
                $customer_bill->save();

                $customer->payment_status = 'billed';
                $customer->last_billing_month = date(config('app.month_format'));
                $customer->save();
            }
        }

        // disconnect PPPoE customer
        PPPCustomerDisconnectController::disconnect($customer);

        return redirect()->route('customers.index')->with('success', 'Package has been changed successfully!');
    }

    /**
     * Show runtime invoice for package change.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @param  \App\Models\package $package
     * @return \Illuminate\Http\Response
     */
    public function runtimeInvoice(customer $customer, package $package)
    {
        $billing_profile = billing_profile::where('id', $customer->billing_profile_id)->firstOrFail();
        $period_start = BillingHelper::getStartingDate($customer, $package);
        $period_stop = Carbon::createFromFormat(config('app.date_format'), $billing_profile->next_payment_date, getTimeZone($customer->operator_id))->isoFormat(config('app.expiry_time_format'));
        $validity = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $period_start, getTimeZone($customer->operator_id), 'en')->diffInDays(Carbon::createFromIsoFormat(config('app.expiry_time_format'), $period_stop, getTimeZone($customer->operator_id), 'en'));
        $invoice = BillingHelper::getRuntimeInvoice($customer, $package, BillingTerms::INTERVAL_UNIT_DAY->value, $validity);
        return view('admins.components.runtime-invoice-v2', [
            'invoice' => $invoice,
        ]);
    }

    /**
     * Delete bills for this month.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     *  @return int
     */
    public static function deleteBills(customer $customer)
    {
        $where = [
            ['operator_id', '=', $customer->operator_id],
            ['customer_id', '=', $customer->id],
            ['year', '=', date(config('app.year_format'))],
            ['month', '=', date(config('app.month_format'))],
        ];

        customer_bill::where($where)->delete();

        if (
            customer_bill::where('operator_id', $customer->operator_id)
            ->where('customer_id', $customer->id)
            ->count() == 0
        ) {
            $customer->payment_status = 'paid';
            $customer->status = 'active';
            $customer->save();
        }

        return 0;
    }
}
