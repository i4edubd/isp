<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Customer\CustomerBillGenerateController;
use App\Http\Controllers\Customer\PPPoECustomersExpirationController;
use App\Http\Controllers\enum\PaymentPurpose;
use App\Jobs\CustomerBillingProfileUpdateJob;
use App\Models\billing_profile;
use App\Models\customer_bill;
use App\Models\Freeradius\customer;
use App\Models\package;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomerBillingProfileEditController extends Controller
{

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, customer $customer)
    {
        $this->authorize('editBillingProfile', $customer);

        $operator = $request->user();

        $active_profile = billing_profile::findOrFail($customer->billing_profile_id);

        $billing_profiles = $operator->billing_profiles;

        $billing_profiles = $billing_profiles->filter(function ($value, $key) use ($customer) {
            switch ($customer->connection_type) {
                case 'PPPoE':
                    return true;
                case 'StaticIp':
                case 'Other':
                    return $value->billing_type !== 'Daily';
                    break;
                case 'Hotspot':
                    return false;
            }
        });

        switch ($operator->role) {

            case 'group_admin':
                return view('admins.group_admin.customer-billing-profile-edit', [
                    'customer' => $customer,
                    'active_profile' => $active_profile,
                    'billing_profiles' => $billing_profiles,
                ]);
                break;

            case 'operator':
                return view('admins.operator.customer-billing-profile-edit', [
                    'customer' => $customer,
                    'active_profile' => $active_profile,
                    'billing_profiles' => $billing_profiles,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.customer-billing-profile-edit', [
                    'customer' => $customer,
                    'active_profile' => $active_profile,
                    'billing_profiles' => $billing_profiles,
                ]);
                break;

            case 'manager':
                return view('admins.manager.customer-billing-profile-edit', [
                    'customer' => $customer,
                    'active_profile' => $active_profile,
                    'billing_profiles' => $billing_profiles,
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
        $this->authorize('editBillingProfile', $customer);

        $request->validate([
            'billing_profile_id' => 'required|numeric',
        ]);

        $billing_profile = billing_profile::findOrFail($request->billing_profile_id);

        CustomerBillingProfileUpdateJob::dispatch($customer, $billing_profile, 1)
            ->onConnection('database')
            ->onQueue('default');

        return redirect()->route('customers.index')->with('success', 'Billing Profile Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function runtimeInvoice(customer $customer, billing_profile $billing_profile)
    {
        $invoice = self::getInvoice($customer, $billing_profile);

        if ($invoice->get('customers_amount') < 1) {
            return 'No Bill';
        } else {
            return view('admins.components.runtime-invoice', [
                'invoice' => $invoice,
            ]);
        }
    }

    /**
     * Run billing_profile update
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @param  \App\Models\billing_profile  $billing_profile
     * @param  int $payment
     * @return int
     */
    public static function doUpdate(customer $customer, billing_profile $billing_profile, $payment = 0)
    {
        #SL1 <Get Active Profile>
        $active_profile = billing_profile::find($customer->billing_profile_id);
        if (!$active_profile) {
            $customer->billing_profile_id = $billing_profile->id;
            $customer->billing_type =  $billing_profile->billing_type;
            $customer->save();
            return 0;
        }

        #SL2 <Get Invoice | The incorrect invoice will be returned if invoked once the profile is updated.>
        $invoice = self::getInvoice($customer, $billing_profile);

        #SL3 <Update Profile>
        $case = $active_profile->billing_type . 'To' . $billing_profile->billing_type;
        switch ($case) {
            case 'FreeToFree':
            case 'DailyToFree':
            case 'MonthlyToFree':
                $customer->billing_profile_id = $billing_profile->id;
                $customer->billing_type = 'Free';
                $customer->save();
                self::deleteBills($customer);
                break;

            case 'FreeToDaily':
            case 'DailyToDaily':
            case 'MonthlyToDaily':
                $customer->billing_profile_id = $billing_profile->id;
                $customer->billing_type = 'Daily';
                $customer->save();
                self::deleteBills($customer);
                break;

            case 'FreeToMonthly':
            case 'DailyToMonthly':
            case 'MonthlyToMonthly':
                $customer->billing_profile_id = $billing_profile->id;
                $customer->billing_type = 'Monthly';
                $customer->save();
                break;
        }

        #SL4 <Insert(For Daily) Or Delete(For Free & Monthly) Expiration Attribute>
        PPPoECustomersExpirationController::updateOrCreate($customer);

        #SL5 <Disconnect to apply Expiration Attribute (N/A for Free & Monthly)>
        if ($case == 'MonthlyToDaily') {
            PPPCustomerDisconnectController::disconnect($customer);
        }

        #SL6 <Bill>
        if ($payment == 0) {
            return 0;
        }
        if ($invoice->get('validity') > 1) {
            CustomerBillGenerateController::generateBill($customer, (int)$invoice->get('validity'),  PaymentPurpose::BILLING_PROFILE_CHANGE->value);
        }
    }

    /**
     * Get Invoice
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @param  \App\Models\billing_profile  $billing_profile
     * @return \lluminate\Support\Collection
     */
    private static function getInvoice(customer $customer, billing_profile $billing_profile)
    {
        $invoice = [
            'package_name' => "",
            'customers_amount' => 0,
            'operators_amount' => 0,
            'validity' => 0,
        ];

        $active_profile = billing_profile::find($customer->billing_profile_id);
        if (!$active_profile) {
            return collect($invoice);
        }

        $package = CacheController::getPackage($customer->package_id);

        $case = $active_profile->billing_type . 'To' . $billing_profile->billing_type;

        switch ($case) {
            case 'FreeToFree':
            case 'DailyToFree':
            case 'MonthlyToFree':
            case 'FreeToDaily':
            case 'DailyToDaily':
            case 'MonthlyToDaily':
            case 'FreeToMonthly':
                return collect($invoice);
                break;

            case 'DailyToMonthly':
                $start_date = Carbon::createFromIsoFormat(config('app.expiry_time_format'), BillingHelper::getStartingDate($customer, $package), getTimeZone($customer->operator_id), 'en');
                $stop_date =  Carbon::createFromFormat(config('app.date_format'), $billing_profile->next_payment_date, getTimeZone($customer->operator_id));
                break;

            case 'MonthlyToMonthly':
                $start_date = Carbon::createFromFormat(config('app.date_format'), $active_profile->payment_date, getTimeZone($customer->operator_id));
                $stop_date = Carbon::createFromFormat(config('app.date_format'), $billing_profile->payment_date, getTimeZone($customer->operator_id));
                break;
        }

        if ($start_date->lessThan($stop_date)) {
            $package = package::findOrFail($customer->package_id);
            $package_price = PackageController::price($customer, $package);
            $master_package = $package->master_package;
            $validity = $start_date->diffInDays($stop_date);
            $customers_amount = round(($package_price / $master_package->validity) * $validity);
            $operators_amount = round(($package->operator_price / $master_package->validity) * $validity);
            $invoice['package_name'] = $package->name;
            $invoice['customers_amount'] = $customers_amount;
            $invoice['operators_amount'] = $operators_amount;
            $invoice['validity'] = $validity;
        }

        return collect($invoice);
    }

    /**
     * Delete Bills
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return void
     */
    public static function deleteBills(customer $customer)
    {
        customer_bill::where('customer_id', $customer->id)
            ->where('operator_id', $customer->operator_id)
            ->delete();
        $customer->payment_status = 'paid';
        $customer->save();
    }
}
