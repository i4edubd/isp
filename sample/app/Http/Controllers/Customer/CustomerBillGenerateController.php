<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\BillingHelper;
use App\Http\Controllers\CacheController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\enum\PaymentPurpose;
use App\Http\Controllers\PackageController;
use App\Models\Freeradius\customer;
use App\Models\customer_bill;
use App\Models\operator;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomerBillGenerateController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, customer $customer)
    {
        $this->authorize('generateBill', $customer);

        $billing_periods = ['From Today To Next Payment Date'];
        $period = Carbon::parse(Carbon::now()->subMonths(3))->monthsUntil(Carbon::now());
        foreach ($period as $key => $value) {
            $billing_periods[] = $value->format(config('app.monthly_billing_period_format'));
        }

        $invoice = self::getRuntimeInvoice($customer, 'From Today To Next Payment Date');
        $operator = $request->user();

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.customer-bills-create', [
                    'customer' => $customer,
                    'invoice' => $invoice,
                    'billing_periods' => $billing_periods,
                ]);
                break;

            case 'operator':
                return view('admins.operator.customer-bills-create', [
                    'customer' => $customer,
                    'invoice' => $invoice,
                    'billing_periods' => $billing_periods,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.customer-bills-create', [
                    'customer' => $customer,
                    'invoice' => $invoice,
                    'billing_periods' => $billing_periods,
                ]);
                break;

            case 'manager':
                return view('admins.manager.customer-bills-create', [
                    'customer' => $customer,
                    'invoice' => $invoice,
                    'billing_periods' => $billing_periods,
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
    public function store(Request $request, customer $customer)
    {
        $request->validate([
            'billing_period' => 'required|string',
        ]);

        $this->authorize('generateBill', $customer);

        $invoice = self::getRuntimeInvoice($customer, $request->billing_period);

        $customer_bill = new customer_bill();
        $customer_bill->mgid = $customer->mgid;
        $customer_bill->gid = $customer->gid;
        $customer_bill->operator_id = $customer->operator_id;
        $customer_bill->parent_customer_id = $customer->parent_id;
        $customer_bill->customer_id = $customer->id;
        $customer_bill->package_id = $customer->package_id;
        $customer_bill->validity_period = $invoice->get('validity');
        $customer_bill->customer_zone_id = $customer->zone_id;
        $customer_bill->name = $customer->name;
        $customer_bill->mobile = $customer->mobile;
        $customer_bill->username = $customer->username;
        $customer_bill->amount = $invoice->get('customers_amount');
        $customer_bill->operator_amount = $invoice->get('operators_amount');
        $customer_bill->currency = $invoice->get('currency');
        $customer_bill->description = $invoice->get('package_name');
        $customer_bill->billing_period = $invoice->get('billing_period');
        $customer_bill->due_date = $invoice->get('due_date');
        $customer_bill->purpose = $invoice->get('purpose');
        $customer_bill->year = $invoice->get('year');
        $customer_bill->month = $invoice->get('month');
        $customer_bill->save();

        $customer->payment_status = 'billed';
        $customer->save();

        return redirect()->route('customers.index')->with('success', 'Bill has been generated successfully');
    }

    /**
     * Generate Customer's Monthly Bill || converting validity to amount
     * amount = price per day * days
     *
     * @return void
     */
    public static function generateBill(customer $customer, int $validity = 30, string $purpose = "Monthly Bill")
    {
        if ($customer->status === 'suspended') {
            return 0;
        }

        if ($customer->status === 'disabled') {
            return 0;
        }

        $package = CacheController::getPackage($customer->package_id);

        if (!$package) {
            return 0;
        }

        if ($package->price < 2) {
            return 0;
        }

        $billing_profile = CacheController::getBillingProfile($customer->billing_profile_id);

        if (!$billing_profile) {
            return 0;
        }

        $package_price = PackageController::price($customer, $package);

        $master_package = $package->master_package;

        $amount =  round(($package_price / $master_package->validity) * $validity);

        $operator_amount = round(($package->operator_price / $master_package->validity) * $validity);

        $currency = getCurrency($customer->operator_id);

        $period_start = BillingHelper::getStartingDate($customer, $package);

        $billing_period = BillingHelper::billingPeriod($period_start, $validity);

        $due_date = BillingHelper::dueDate($customer);

        if ($amount > 0) {
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
            $customer_bill->amount = $amount;
            $customer_bill->operator_amount = $operator_amount;
            $customer_bill->currency = $currency;
            $customer_bill->description = $package->name;
            $customer_bill->billing_period = $billing_period;
            $customer_bill->due_date = $due_date;
            $customer_bill->purpose = $purpose;
            $customer_bill->year = date(config('app.year_format'));
            $customer_bill->month = date(config('app.month_format'));
            $customer_bill->save();

            $customer->payment_status = 'billed';
            $customer->last_billing_month = date(config('app.month_format'));
            $customer->save();
        }
    }

    /**
     * Generate Customer's Monthly Bill
     *
     * @return void
     */
    public static function monthlyBill()
    {
        $group_admins = operator::where('role', 'group_admin')->get();

        while ($group_admin = $group_admins->shift()) {

            $customers_where = [
                ['mgid', '=', $group_admin->id],
                ['connection_type', '!=', 'Hotspot'],
            ];

            $model = new customer();
            $model->setConnection($group_admin->radius_db_connection);
            $customers = $model->where($customers_where)->get();

            while ($customer = $customers->shift()) {
                if (self::isBillableMonthly($customer)) {
                    $package = CacheController::getPackage($customer->package_id);
                    self::generateBill($customer, $package->master_package->validity);
                }
            }
        }
    }

    /**
     * Generate Customer's Monthly Bill
     *
     * @return void
     */
    public static function monthlyBillForOperator($operator_id)
    {
        $operator = operator::findOrFail($operator_id);

        $customers_where = [
            ['operator_id', '=', $operator->id],
            ['connection_type', '!=', 'Hotspot'],
        ];

        $model = new customer();
        $model->setConnection($operator->radius_db_connection);
        $customers = $model->where($customers_where)->get();

        while ($customer = $customers->shift()) {
            if (self::isBillableMonthly($customer)) {
                $package = CacheController::getPackage($customer->package_id);
                self::generateBill($customer, $package->master_package->validity);
            }
        }
    }

    /**
     * Is customer billable monthly?
     *
     * @return void
     */
    public static function isBillableMonthly(customer $customer)
    {
        if ($customer->last_billing_month === date(config('app.month_format'))) {
            return false;
        }

        if ($customer->connection_type === 'Hotspot') {
            return false;
        }

        $billing_profile = CacheController::getBillingProfile($customer->billing_profile_id);

        if (!$billing_profile) {
            return false;
        }

        if ($billing_profile->billing_type === 'Free') {
            return false;
        }

        if ($billing_profile->billing_type === 'Daily') {
            return false;
        }

        if ($billing_profile->auto_bill === 'no') {
            return false;
        }

        $package = CacheController::getPackage($customer->package_id);

        if (!$package) {
            return false;
        }

        if ($package->price < 2) {
            return false;
        }

        if ($package->master_package->validity != 30) {
            return false;
        }

        return true;
    }

    /**
     * Show Runtime Invoice For generate bill action
     *
     * @param  \App\Models\Freeradius\customer $customer
     * @param  string $billing_period
     * @return Illuminate\Support\Collection
     */
    public function showRuntimeInvoice(customer $customer, string $billing_period)
    {
        $invoice = self::getRuntimeInvoice($customer, $billing_period);

        return view('admins.components.runtime-invoice', [
            'invoice' => $invoice,
        ]);
    }

    /**
     * Get Runtime Invoice For generate bill action
     *
     * @param  \App\Models\Freeradius\customer $customer
     * @param  string $billing_period
     * @return Illuminate\Support\Collection
     */
    public static function getRuntimeInvoice(customer $customer, string $billing_period)
    {
        $invoice = [];

        $package = CacheController::getPackage($customer->package_id);
        $billing_profile = CacheController::getBillingProfile($customer->billing_profile_id);
        $invoice['package_name'] = $package->name;
        $invoice['package_price'] = $package->price;

        $interval_count = match ($billing_period) {
            'From Today To Next Payment Date' => Carbon::now()->diffInDays(Carbon::createFromFormat(config('app.date_format'), $billing_profile->next_payment_date)),
            default => 30,
        };
        $invoice['validity'] = $interval_count;

        if ($interval_count == 30) {
            $amount = $package->price;
            $operator_amount = $package->operator_price;
        } else {
            $amount = ($package->price / $package->master_package->validity_in_days) * $interval_count;
            $operator_amount = ($package->operator_price / $package->master_package->validity_in_days) * $interval_count;
        }
        $invoice['customers_amount'] = $amount;
        $invoice['operators_amount'] = $operator_amount;
        $invoice['currency'] = getCurrency($customer->operator_id);

        $bill_period = match ($billing_period) {
            'From Today To Next Payment Date' => Carbon::now()->format(config('app.date_format')) . ' To ' . Carbon::createFromFormat(config('app.date_format'), $billing_profile->next_payment_date)->format(config('app.date_format')),
            default => $billing_period,
        };
        $invoice['billing_period'] = $bill_period;

        $date = match ($billing_period) {
            'From Today To Next Payment Date' => Carbon::now(),
            default => Carbon::createFromFormat(config('app.monthly_billing_period_format'), $billing_period),
        };

        $billing_due_date = is_null($billing_profile->billing_due_date) ? 1 : $billing_profile->billing_due_date;
        $invoice['due_date'] = $date->setDay($billing_due_date)->format(config('app.date_format'));
        $invoice['purpose'] = PaymentPurpose::MONTHLY_BILL->value;
        $invoice['year'] = $date->format(config('app.year_format'));
        $invoice['month'] = $date->format(config('app.month_format'));

        return collect($invoice);
    }
}
