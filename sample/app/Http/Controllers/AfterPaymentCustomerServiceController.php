<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Customer\HotspotCustomersRadAttributesController;
use App\Http\Controllers\Customer\PPPoECustomersRadAttributesController;
use App\Http\Controllers\Customer\RadAcctDeleteController;
use App\Http\Controllers\Customer\StaticIpCustomersFirewallController;
use App\Http\Controllers\enum\BillingTerms;
use App\Models\customer_payment;
use App\Models\Freeradius\customer;
use App\Models\operator;
use App\Models\package;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AfterPaymentCustomerServiceController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param \App\Models\customer_payment $customer_payment
     * @return void
     */
    public static function update(customer_payment $customer_payment)
    {
        // Service Already Given || Not Require
        if ($customer_payment->payment_mode == 'postpaid') {
            return 0;
        }

        $operator = operator::findOrFail($customer_payment->operator_id);
        $package = package::findOrFail($customer_payment->package_id);
        $master_package = $package->master_package;

        $model = new customer();
        $model->setConnection($operator->node_connection);
        $customer = $model->findOrFail($customer_payment->customer_id);

        $package_price = PackageController::price($customer, $package);

        if (strlen($customer_payment->package_started_at)) {
            $package_started_at = $customer_payment->package_started_at;
            $package_expired_at = $customer_payment->package_expired_at;
        } else { // deprecated
            $package_started_at = BillingHelper::getStartingDate($customer, $package);
            $package_expired_at = BillingHelper::stoppingDate($customer, BillingHelper::getPurchasedMinutes($customer_payment), BillingTerms::INTERVAL_UNIT_MINUTE->value);
        }

        $octet_limit = round(($master_package->total_octet_limit / $package_price) * $customer_payment->amount_paid);

        //update customer
        $customer->package_id = $package->id;
        $customer->package_name = $package->name;
        $customer->last_recharge_time = Carbon::now();
        $customer->package_started_at = $package_started_at;
        $customer->package_expired_at = $package_expired_at;
        $customer->exptimestamp = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($customer->operator_id), 'en')->timestamp;
        $customer->rate_limit = $master_package->rate_limit;
        $customer->total_octet_limit = $octet_limit;
        $customer->save();

        if ($customer->connection_type == 'PPPoE' || $customer->connection_type == 'Hotspot') {
            RadAcctDeleteController::deleteRadaccts($customer);
        }

        if ($customer->connection_type == 'PPPoE') {
            PPPoECustomersRadAttributesController::updateOrCreate($customer);
        }

        if ($customer->connection_type == 'Hotspot') {
            HotspotCustomersRadAttributesController::updateOrCreate($customer);
        }

        if ($customer->connection_type == 'StaticIp') {
            StaticIpCustomersFirewallController::updateOrCreate($customer);
        }
    }
}
