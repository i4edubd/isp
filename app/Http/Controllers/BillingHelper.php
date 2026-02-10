<?php

namespace App\Http\Controllers;

use App\Http\Controllers\enum\BillingTerms;
use App\Models\billing_profile;
use App\Models\billing_profile_operator;
use App\Models\customer_bill;
use App\Models\customer_payment;
use App\Models\Freeradius\customer;
use App\Models\operator;
use App\Models\package;
use Carbon\Carbon;

class BillingHelper extends Controller
{
    /**
     * Runtime Invoice Collection
     *
     * @return Illuminate\Support\Collection
     */
    public static function runtimeInvoiceCollection()
    {
        return collect([
            'currency' => config('consumer.currency'),
            'package_name' => '',
            'package_customers_price' => 0,
            'package_operators_price' => 0,
            'interval_unit' => BillingTerms::INTERVAL_UNIT_DAY->value,
            'interval_count' => 0,
            'package_started_at' => '',
            'customers_unit_price' => 0,
            'operators_unit_price' => 0,
            'package_expired_at' => '',
            'bill_period' => false,

            'customers_bill_amount' => 0,
            'operators_bill_amount' => 0,
            'next_payment_date' => false,

            'customers_payable_amount' => 0,
            'operators_payable_amount' => 0,

            'discount_package_name' => '',
            'discount_package_customers_price' => 0,
            'discount_package_operators_price' => 0,
            'discount_interval_count' => 0,
            'discount_customers_unit_price' => 0,
            'discount_operators_unit_price' => 0,
            'customers_discount_amount' => 0,
            'operators_discount_amount' => 0,
            'discount_bill_period' => false,
        ]);
    }

    /**
     * Runtime Invoice
     *
     * @param  \App\Models\Freeradius\customer $customer
     * @param  \App\Models\package $package
     * @param  string $interval_unit
     * @param  int $interval_count
     * @return Illuminate\Support\Collection
     */
    public static function getRuntimeInvoice(customer $customer, package $package, string $interval_unit, int $interval_count)
    {
        if (in_array($customer->type, ['PPP_FREE', 'HOTSPOT_FREE', 'STATIC_FREE', 'OTHER_FREE', 'HOTPOST_MONTHLY', 'STATIC_DAILY', 'OTHER_DAILY'])) {
            return self::runtimeInvoiceCollection();
        }

        $interval_units = [BillingTerms::INTERVAL_UNIT_DAY->value, BillingTerms::INTERVAL_UNIT_MINUTE->value];
        if (in_array($interval_unit, $interval_units) == false) {
            abort(500, 'Bad Interval Unit');
        }

        $currency = getCurrency($customer->operator_id);
        $timezone = getTimeZone($customer->operator_id);

        $package_price = PackageController::price($customer, $package);

        $collection = self::runtimeInvoiceCollection();
        $collection->put('currency', $currency);
        $collection->put('package_name', $package->name);
        $collection->put('package_customers_price', $package_price);
        $collection->put('package_operators_price', $package->operator_price);
        $collection->put('interval_unit', $interval_unit);
        $collection->put('interval_count', $interval_count);

        $start_date = self::getStartingDate($customer, $package);
        $collection->put('package_started_at', $start_date);

        if ($interval_unit == BillingTerms::INTERVAL_UNIT_DAY->value) {
            $customers_unit_price = $package_price / $package->master_package->validity;
            $operators_unit_price = $package->operator_price / $package->master_package->validity;
            $stop_date = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $start_date, $timezone)->addDays($interval_count)->isoFormat(config('app.expiry_time_format'));
        }
        if ($interval_unit == BillingTerms::INTERVAL_UNIT_MINUTE->value) {
            $customers_unit_price = $package_price / $package->master_package->total_minute;
            $operators_unit_price = $package->operator_price / $package->master_package->total_minute;
            $stop_date = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $start_date, $timezone)->addMinutes($interval_count)->isoFormat(config('app.expiry_time_format'));
        }

        $collection->put('customers_unit_price', $customers_unit_price);
        $collection->put('operators_unit_price', $operators_unit_price);
        $collection->put('package_expired_at', $stop_date);

        $bill_period = 'From ' . $start_date . ' To ' . $stop_date;
        $collection->put('bill_period', $bill_period);

        $customers_bill_amount = round($customers_unit_price * $interval_count);
        $operators_bill_amount = round($operators_unit_price * $interval_count);
        $collection->put('customers_bill_amount', $customers_bill_amount);
        $collection->put('operators_bill_amount', $operators_bill_amount);

        $next_payment_date = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $stop_date, $timezone)->format(config('app.date_format'));
        $collection->put('next_payment_date', $next_payment_date);

        $collection->put('customers_payable_amount', $customers_bill_amount);
        $collection->put('operators_payable_amount', $operators_bill_amount);

        if ($customer->package_id == $package->id) {
            return $collection;
        }

        //  Discount Calculations
        if (Carbon::today($timezone)->lessThan(Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, $timezone))) {
            $discount_package = CacheController::getPackage($customer->package_id);

            if (!$discount_package) {
                return $collection;
            }
            if ($discount_package->name == 'Trial') {
                return $collection;
            }

            $collection->put('discount_package_name', $discount_package->name);
            $collection->put('discount_package_customers_price', $discount_package->price);
            $collection->put('discount_package_operators_price', $discount_package->operator_price);

            if ($interval_unit == BillingTerms::INTERVAL_UNIT_DAY->value) {
                $discount_interval_count = Carbon::now($timezone)->diffInDays(Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, $timezone));
                $discount_customers_unit_price = $discount_package->price / $discount_package->master_package->validity;
                $discount_operators_unit_price = $discount_package->operator_price / $discount_package->master_package->validity;
            }
            if ($interval_unit == BillingTerms::INTERVAL_UNIT_MINUTE->value) {
                $discount_interval_count =  Carbon::now($timezone)->diffInMinutes(Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, $timezone));
                $discount_customers_unit_price = $discount_package->price / $discount_package->master_package->total_minute;
                $discount_operators_unit_price = $discount_package->operator_price / $discount_package->master_package->total_minute;
            }

            $collection->put('discount_interval_count', $discount_interval_count);
            $collection->put('discount_customers_unit_price', $discount_customers_unit_price);
            $collection->put('discount_operators_unit_price', $discount_operators_unit_price);
            $collection->put('customers_discount_amount', round($discount_interval_count * $discount_customers_unit_price));
            $collection->put('operators_discount_amount', round($discount_interval_count * $discount_operators_unit_price));
            $collection->put('customers_payable_amount', $collection->get('customers_bill_amount') - $collection->get('customers_discount_amount'));
            $collection->put('operators_payable_amount', $collection->get('operators_bill_amount') - $collection->get('operators_discount_amount'));

            $discount_bill_period = 'From ' . Carbon::now($timezone)->isoFormat(config('app.expiry_time_format')) . ' To ' . $customer->package_expired_at;
            $collection->put('discount_bill_period', $discount_bill_period);

            return $collection;
        }

        // always
        return $collection;
    }

    /**
     * Return Start and Stop Time
     *
     * @param  \App\Models\Freeradius\customer $customer
     * @param  \App\Models\package $package
     * @return string
     */
    public static function getStartingDate(customer $customer, package $package)
    {
        if ($customer->package_id == $package->id) {
            $expiration = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($customer->operator_id), 'en');
            $now = Carbon::now(getTimeZone($customer->operator_id));
            if ($expiration->lessThan($now)) {
                return $now->isoFormat(config('app.expiry_time_format'));
            } else {
                return $expiration->isoFormat(config('app.expiry_time_format'));
            }
        }

        return Carbon::now(getTimeZone($customer->operator_id))->isoFormat(config('app.expiry_time_format'));
    }

    /**
     * Return Start and Stop Time for the purchased package
     *
     * @param  \App\Models\Freeradius\customer $customer
     * @param  \App\Models\package $package
     * @return Illuminate\Support\Collection
     */
    public static function getStartAndStopDate(customer $customer, package $package)
    {
        $invoice = self::getRuntimeInvoice($customer, $package, BillingTerms::INTERVAL_UNIT_DAY->value, $package->master_package->validity);
        $start_date = $invoice->get('package_started_at');
        $master_package = $package->master_package;
        $customer_discount = $invoice->get('customers_discount_amount');
        if ($customer_discount > 0) {
            $plus_minutes = round(($master_package->total_minute / $package->price) * $customer_discount);
        } else {
            $plus_minutes = 0;
        }
        $stop_date = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $start_date, getTimeZone($customer->operator_id), 'en')->addDays($master_package->validity)->addMinutes($plus_minutes)->isoFormat(config('app.expiry_time_format'));
        return collect(['start_date' => $start_date, 'stop_date' => $stop_date]);
    }

    /**
     * Return Due Date
     *
     * @param  \App\Models\Freeradius\customer $customer
     * @return string
     */
    public static function dueDate(customer $customer)
    {
        $expiration = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($customer->operator_id), 'en');

        $now = Carbon::now(getTimeZone($customer->operator_id));

        if ($expiration->lessThan($now)) {
            return $now->format(config('app.date_format'));
        } else {
            $due_date = $expiration->format(config('app.date_format'));
        }

        // Hotspot
        if ($customer->connection_type === 'Hotspot') {
            return $due_date;
        }

        // Not Monthly package
        $package = CacheController::getPackage($customer->package_id);
        if ($package->master_package->validity !== 30) {
            return $due_date;
        }

        // new customer
        if ($customer->registration_date == date(config('app.date_format'))) {
            return $customer->registration_date;
        }

        // According to Billing Profile
        $billing_profile = CacheController::getBillingProfile($customer->billing_profile_id);
        return $billing_profile->payment_date;
    }

    /**
     * Return package_expired_at
     *
     * @param  \App\Models\Freeradius\customer $customer
     * @param int $interval_count
     * @param string $interval_unit
     * @return string
     */
    public static function stoppingDate(customer $customer, int $interval_count, string $interval_unit = 'Day')
    {
        $package = CacheController::getPackage($customer->package_id);
        $master_package = $package->master_package;

        $package_started_at = self::getStartingDate($customer, $package);

        if ($interval_unit == BillingTerms::INTERVAL_UNIT_MINUTE->value) {
            $package_expired_at = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $package_started_at, getTimeZone($customer->operator_id), 'en')->addMinutes($interval_count)->isoFormat(config('app.expiry_time_format'));
        } else {
            $package_expired_at = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $package_started_at, getTimeZone($customer->operator_id), 'en')->addDays($interval_count)->isoFormat(config('app.expiry_time_format'));
        }

        if ($customer->connection_type == 'Hotspot') {
            return $package_expired_at;
        } else {
            $billing_profile = CacheController::getBillingProfile($customer->billing_profile_id);

            // Daily Billing
            if ($billing_profile->billing_type == 'Daily') {
                return $package_expired_at;
            }

            if ($master_package->validity !== 30) {
                return $package_expired_at;
            }

            // Monthly Billing
            $next_payment_date = $billing_profile->next_payment_date;

            return Carbon::createFromFormat(config('app.date_format'), $next_payment_date, getTimeZone($customer->operator_id))->isoFormat(config('app.expiry_time_format'));
        }
    }

    /**
     * Return Billing Period
     *
     * @param  string $starting_date
     * @param int $validity
     * @return string
     */
    public static function billingPeriod(string $starting_date, int $validity)
    {
        if ($validity !== 30) {
            $period_start = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $starting_date)->format(config('app.billing_period_format'));
            $period_stop =  Carbon::createFromIsoFormat(config('app.expiry_time_format'), $starting_date)->addDays($validity - 1)->format(config('app.billing_period_format'));
            return $period_start . ' to ' . $period_stop;
        }
        return date('F-Y');
    }

    /**
     * Return validity_period
     *
     * @param  \App\Models\customer_bill $customer_bill
     * @return int
     */
    public static function getBillValidity(customer_bill $customer_bill)
    {
        // package
        $package = CacheController::getPackage($customer_bill->package_id);
        if (!$package) {
            return 0;
        }
        $master_package = $package->master_package;

        // operator
        $operator = CacheController::getOperator($customer_bill->operator_id);
        if (!$operator) {
            return 0;
        }

        // customer
        $model = new customer();
        $model->setConnection($operator->node_connection);
        $customer = $model->find($customer_bill->customer_id);
        if (!$customer) {
            return 0;
        }

        // package_price
        $package_price = PackageController::price($customer, $package);
        if ($package_price == 0) {
            return 0;
        }

        // validity
        return round(($master_package->validity / $package_price) * $customer_bill->amount);
    }

    /**
     * Return Purchased Minutes
     *
     * @param  \App\Models\customer_payment $customer_payment
     * @return int
     */
    public static function getPurchasedMinutes(customer_payment $customer_payment)
    {
        // package
        $package = CacheController::getPackage($customer_payment->package_id);
        if (!$package) {
            return 0;
        }
        $master_package = $package->master_package;

        // operator
        $operator = CacheController::getOperator($customer_payment->operator_id);
        if (!$operator) {
            return 0;
        }

        // customer
        $model = new customer();
        $model->setConnection($operator->node_connection);
        $customer = $model->find($customer_payment->customer_id);
        if (!$customer) {
            return 0;
        }

        // package_price
        $package_price = PackageController::price($customer, $package);
        if ($package_price == 0) {
            return 0;
        }

        // Purchased Minutes
        return round(($master_package->total_minute / $package_price) * $customer_payment->amount_paid);
    }

    /**
     * Return Validity Period
     *
     * @param  int $interval_count
     * @param  string $interval_unit
     * @return  string
     */
    public static function getValidityPeriod(int $interval_count, string $interval_unit)
    {
        if ($interval_unit == BillingTerms::INTERVAL_UNIT_MINUTE->value) {
            return mToDhm($interval_count);
        }

        return $interval_count . ' ' . $interval_unit;
    }

    /**
     * Return Default Billing Profile
     *
     * @param  \App\Models\customer_bill $customer_bill
     * @return  \App\Models\billing_profile
     */
    public static function getDefaultBillingProfile(operator $operator)
    {
        $month_length =  date('t');

        $today = date('j');

        $billing_due_date = $today;

        switch ($month_length) {
            case '28':
                $billing_due_date = $billing_due_date + 2;
                break;
            case '29':
                $billing_due_date = $billing_due_date + 1;
                break;
            case '30':
                $billing_due_date = $billing_due_date;
                break;
            case '31':
                $billing_due_date = $billing_due_date - 1;
                break;
        }

        if ($billing_due_date > 28) {
            $billing_due_date = 28;
        }

        if ($billing_due_date < 1) {
            $billing_due_date = $today;
        }

        if ($operator->role == 'manager') {
            $operator = operator::find($operator->gid);
        }

        $selected_profile =  billing_profile::where('mgid', $operator->mgid)
            ->where('billing_type', 'Monthly')
            ->where('billing_due_date', $billing_due_date)
            ->first();

        switch ($operator->role) {

            case 'group_admin':
                if ($selected_profile) {
                    return $selected_profile;
                } else {
                    return billing_profile::where('mgid', $operator->mgid)->where('billing_type', 'Monthly')
                        ->first();
                }
                break;

            default:
                if ($selected_profile) {
                    $permission = billing_profile_operator::where('billing_profile_id', $selected_profile->id)
                        ->where('operator_id', $operator->id)
                        ->count();

                    if ($permission) {
                        return $selected_profile;
                    }
                }

                $permitted_profile = billing_profile_operator::where('operator_id', $operator->id)
                    ->first();

                if ($permitted_profile) {
                    return billing_profile::find($permitted_profile->billing_profile_id);
                }

                return 0;
                break;
        }
    }
}
