<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Customer\CustomerActivateController;
use App\Http\Controllers\Customer\CustomerBillGenerateController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Customer\CustomerDisableController;
use App\Http\Controllers\Customer\CustomerMacBindController;
use App\Http\Controllers\Customer\CustomerPackageUpdateController;
use App\Http\Controllers\Customer\CustomerSuspendController;
use App\Http\Controllers\Customer\PPPoECustomersExpirationController;
use App\Http\Controllers\Customer\PPPoECustomersFramedIPAddressController;
use App\Jobs\CustomerBillingProfileUpdateJob;
use App\Jobs\WithSelectedSendSmsJob;
use App\Models\billing_profile;
use App\Models\bulk_customer_bill_paid;
use App\Models\customer_bill;
use App\Models\extend_package_validity;
use App\Models\Freeradius\customer;
use App\Models\operator;
use App\Models\package;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MultipleCustomerEditController extends Controller
{

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateOrDestroy(Request $request)
    {
        $request->validate([
            'verb' => 'in:activate,suspend,disable,edit_zone,pay_bills,delete,change_operator,change_package,change_billing_profile,change_exp_date,extend_package_validity,generate_bill,remove_mac_bind,send_sms|required',
        ]);

        if ($request->verb == 'change_operator') {
            $new_operator = operator::findOrFail($request->operator_id);
            if ($new_operator->role == 'manager') {
                return redirect(url()->previous())->with('error', 'Operator Can not be manager!');
            }
        }

        if ($request->verb == 'change_package') {
            $new_package = package::findOrFail($request->package_id);
            $master_package = $new_package->master_package;
        }

        if ($request->verb == 'change_exp_date') {
            $new_date = date_format(date_create($request->new_exp_date), config('app.date_format'));
            $package_expired_at = Carbon::createFromFormat(config('app.date_format'), $new_date, getTimeZone($request->user()->id))->setHour(22)->isoFormat(config('app.expiry_time_format'));
        }

        if ($request->verb == 'change_billing_profile') {
            $billing_profile = billing_profile::findOrFail($request->billing_profile_id);
        }

        if ($request->verb == 'extend_package_validity') {
            $request->validate([
                'validity' => 'numeric|required|min:1'
            ]);
            $validity = $request->validity;

            // flush previous request
            if ($request->filled('customer_ids')) {
                $first_customer_id = $request->customer_ids[0];
                $first_customer = customer::find($first_customer_id);
                if ($first_customer) {
                    extend_package_validity::where('operator_id', $first_customer->operator_id)->delete();
                } else {
                    extend_package_validity::where('operator_id', $request->user()->id)->delete();
                }
            }
        }

        if ($request->verb == 'edit_zone') {
            $request->validate([
                'zone_id' => 'numeric|required'
            ]);
            $zone_id = $request->zone_id;
        }

        if ($request->verb == 'pay_bills') {
            // flush previous request
            bulk_customer_bill_paid::where('requester_id', $request->user()->id)->delete();
        }

        if ($request->verb == 'send_sms') {
            $request->validate([
                'message' => 'required|string',
            ]);
        }

        $extend_package_operator_id = 0;

        if ($request->filled('customer_ids')) {
            foreach ($request->customer_ids as $customer_id) {
                $customer = customer::find($customer_id);
                switch ($request->verb) {
                    case 'activate':
                        if ($request->user()->can('activate', $customer)) {
                            $controller = new CustomerActivateController();
                            $controller->update($customer);
                        }
                        break;
                    case 'suspend':
                        if ($request->user()->can('suspend', $customer)) {
                            $controller = new CustomerSuspendController();
                            $controller->update($customer);
                        }
                        break;
                    case 'disable':
                        if ($request->user()->can('disable', $customer)) {
                            $controller = new CustomerDisableController();
                            $controller->update($customer);
                        }
                        break;
                    case 'remove_mac_bind':
                        if ($request->user()->can('removeMacBind', $customer)) {
                            CustomerMacBindController::destroy($customer);
                        }
                        break;
                    case 'edit_zone':
                        if ($request->user()->can('update', $customer)) {
                            $customer->zone_id = $zone_id;
                            $customer->save();
                        }
                        break;
                    case 'pay_bills':
                        $bills = customer_bill::where('operator_id', $customer->operator_id)
                            ->where('customer_id', $customer->id)->get();
                        foreach ($bills as $bill) {
                            if ($request->user()->can('receivePayment', $bill)) {
                                $entry = new bulk_customer_bill_paid();
                                $entry->requester_id = $request->user()->id;
                                $entry->customer_bill_id = $bill->id;
                                $entry->amount = $bill->amount;
                                $entry->operator_amount = $bill->operator_amount;
                                $entry->save();
                            }
                        }
                        break;
                    case 'delete':
                        if ($request->user()->can('delete', $customer)) {
                            $controller = new CustomerController();
                            $controller->destroy($customer);
                        }
                        break;
                    case 'change_package':
                        if ($customer->connection_type !== $master_package->connection_type) {
                            break;
                        }
                        if ($customer->package_id == $new_package->id) {
                            break;
                        }
                        if ($request->user()->can('editPackageWithoutAccounting', $customer)) {
                            if (Gate::forUser($customer)->allows('usePackage', [$new_package])) {
                                CustomerPackageUpdateController::update($customer, $new_package);
                            }
                        }
                        break;
                    case 'change_exp_date':
                        if ($request->user()->can('editSuspendDate', $customer)) {
                            $customer->package_expired_at = $package_expired_at;
                            $customer->exptimestamp = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($request->user()->id), 'en')->timestamp;
                            $customer->save();
                            if ($customer->connection_type === 'PPPoE') {
                                PPPoECustomersFramedIPAddressController::updateOrCreate($customer);
                                PPPoECustomersExpirationController::updateOrCreate($customer);
                            }
                        }
                        break;
                    case 'change_billing_profile':
                        if ($request->user()->can('update', $customer)) {
                            CustomerBillingProfileUpdateJob::dispatch($customer, $billing_profile, 0)
                                ->onConnection('database')
                                ->onQueue('default');
                        }
                        break;
                    case 'change_operator':
                        if ($request->user()->can('changeOperator', $customer)) {
                            OperatorChangeController::changeOperator($customer, $new_operator);
                        }
                        break;
                    case 'extend_package_validity':
                        if (
                            $request->user()->can('dailyRecharge', $customer) ||
                            $request->user()->can('hotspotRecharge', $customer)
                        ) {
                            $extend_package_validity = new extend_package_validity();
                            $extend_package_validity->operator_id = $customer->operator_id;
                            $extend_package_validity->customer_id = $customer->id;
                            $extend_package_validity->connection_type = $customer->connection_type;
                            $extend_package_validity->billing_profile_id = $customer->billing_profile_id;
                            $extend_package_validity->package_id = $customer->package_id;
                            $extend_package_validity->validity = $validity;
                            $extend_package_validity->save();
                        }
                        $extend_package_operator_id = $customer->operator_id;
                        break;
                    case 'generate_bill':
                        if ($request->user()->can('generateBill', $customer)) {
                            CustomerBillGenerateController::generateBill($customer);
                        }
                        break;
                    case 'send_sms':
                        if ($request->user()->can('sendSms', $customer)) {
                            WithSelectedSendSmsJob::dispatch($customer, $request->message)
                                ->onConnection('database')
                                ->onQueue('default');
                        }
                }
            }
        }

        if ($request->verb == 'extend_package_validity' && $extend_package_operator_id) {
            return redirect()->route('operators.extend_package_validity.create', ['operator' => $extend_package_operator_id]);
        }

        if ($request->verb == 'pay_bills') {
            if (bulk_customer_bill_paid::where('requester_id', $request->user()->id)->count()) {
                return redirect()->route('bulk_customer_bill_paids.create');
            }
        }

        return redirect(url()->previous())->with('success', 'Done successfully');
    }
}
