<?php

namespace App\Http\Controllers;

use App\Models\billing_profile;
use App\Models\package;
use App\Models\temp_customer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TempCustomerBillInfoController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\temp_customer  $temp_customer
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, temp_customer $temp_customer)
    {
        $operator = $request->user();

        $invoice = [];

        switch ($temp_customer->connection_type) {
            case 'PPPoE':
                $invoice['package_name'] = $temp_customer->package_name;
                $invoice['start_date'] = $temp_customer->package_started_at;
                $invoice['stop_date'] = $temp_customer->package_expired_at;
                $invoice['next_payment_date'] = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $temp_customer->package_expired_at, getTimeZone($temp_customer->operator_id), 'en')->format(config('app.date_format'));
                $package = package::findOrFail($temp_customer->package_id);
                $master_package = $package->master_package;
                if ($temp_customer->billing_type == 'Daily') {
                    $validity_minute = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $temp_customer->package_started_at, getTimeZone($temp_customer->operator_id), 'en')->diffInMinutes(Carbon::createFromIsoFormat(config('app.expiry_time_format'), $temp_customer->package_expired_at, getTimeZone($temp_customer->operator_id), 'en'));
                    $customers_amount = round(($package->price / $master_package->total_minute) * $validity_minute);
                    $operators_amount = round(($package->operator_price / $master_package->total_minute) * $validity_minute);
                    $invoice['validity_minute'] = $validity_minute;
                    $invoice['customers_amount'] = $customers_amount;
                    $invoice['operators_amount'] = $operators_amount;
                } elseif ($temp_customer->billing_type == 'Free') {
                    $invoice['validity'] = 'Unlimited';
                    $invoice['customers_amount'] = 0;
                    $invoice['operators_amount'] = 0;
                    $invoice['start_date'] = 'N/A';
                    $invoice['stop_date'] = 'N/A';
                    $invoice['next_payment_date'] = 'N/A';
                } else {
                    $billing_profile = billing_profile::findOrFail($temp_customer->billing_profile_id);
                    $validity = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $temp_customer->package_started_at, getTimeZone($temp_customer->operator_id), 'en')->diffInDays(Carbon::createFromIsoFormat(config('app.expiry_time_format'), $temp_customer->package_expired_at, getTimeZone($temp_customer->operator_id), 'en')) + 1;
                    $validity = $validity - $billing_profile->grace_period;
                    $customers_amount = round(($package->price / $master_package->validity) * $validity);
                    $operators_amount = round(($package->operator_price / $master_package->validity) * $validity);
                    if ($billing_profile->cycle_ends_with_month) {
                        $invoice['stop_date'] =  Carbon::createFromFormat(config('app.date_format'), $billing_profile->end_of_billing_cycle, getTimeZone($temp_customer->operator_id))->isoFormat(config('app.expiry_time_format'));
                    }
                    $invoice['validity'] = $validity;
                    $invoice['customers_amount'] = $customers_amount;
                    $invoice['operators_amount'] = $operators_amount;
                }
                break;
            case 'Hotspot':
                $invoice['package_name'] = $temp_customer->package_name;
                $invoice['start_date'] = $temp_customer->package_started_at;
                $invoice['stop_date'] = $temp_customer->package_expired_at;
                $invoice['next_payment_date'] = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $temp_customer->package_expired_at, getTimeZone($temp_customer->operator_id), 'en')->format(config('app.date_format'));
                $package = package::findOrFail($temp_customer->package_id);
                $master_package = $package->master_package;
                $invoice['validity'] = $master_package->validity;
                $invoice['customers_amount'] = $package->price;
                $invoice['operators_amount'] = $package->operator_price;
                break;

            case 'StaticIp':
            case 'Other':
                return redirect()->route('temp_customers.customers.create', ['temp_customer' => $temp_customer->id]);
                break;
        }

        $invoice = collect($invoice);

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.temp-customer-billinfo', [
                    'temp_customer' => $temp_customer,
                    'invoice' => $invoice,
                ]);
                break;

            case 'operator':
                return view('admins.operator.temp-customer-billinfo', [
                    'temp_customer' => $temp_customer,
                    'invoice' => $invoice,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.temp-customer-billinfo', [
                    'temp_customer' => $temp_customer,
                    'invoice' => $invoice,
                ]);
                break;

            case  'manager':
                return view('admins.manager.temp-customer-billinfo', [
                    'temp_customer' => $temp_customer,
                    'invoice' => $invoice,
                ]);
                break;
        }
    }
}
