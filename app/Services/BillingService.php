<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerBill;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BillingService
{
    /**
     * Generate monthly bills for active customers.
     * Returns an array with counts and errors.
     */
    public function generateMonthlyBills(Carbon $date = null): array
    {
        $date = $date ?: Carbon::now();
        $created = 0;
        $errors = [];

        $customers = Customer::where('is_active', true)->get();

        foreach ($customers as $customer) {
            try {
                $exists = CustomerBill::where('customer_id', $customer->id)
                    ->where('year', $date->year)
                    ->where('month', $date->month)
                    ->exists();

                if ($exists) {
                    continue;
                }

                // Defensive checks: try to derive amount from billing profile or package
                $amount = null;
                if (isset($customer->billingProfile) && isset($customer->billingProfile->package->price)) {
                    $amount = $customer->billingProfile->package->price;
                } elseif (isset($customer->package_price)) {
                    $amount = $customer->package_price;
                }

                if ($amount === null) {
                    $errors[] = "Missing amount for customer {$customer->id}";
                    continue;
                }

                CustomerBill::create([
                    'mgid' => $customer->mgid ?? null,
                    'gid' => $customer->gid ?? null,
                    'operator_id' => $customer->operator_id ?? null,
                    'customer_id' => $customer->id,
                    'package_id' => $customer->package_id ?? null,
                    'validity_period' => $customer->validity_period ?? 30,
                    'customer_zone_id' => $customer->customer_zone_id ?? null,
                    'name' => $customer->name,
                    'mobile' => $customer->mobile ?? null,
                    'username' => $customer->username ?? null,
                    'amount' => $amount,
                    'description' => "Auto-generated monthly bill",
                    'billing_period' => $date->format('F Y'),
                    'due_date' => $date->copy()->addDays(15)->format('Y-m-d'),
                    'year' => $date->year,
                    'month' => $date->month,
                    'status' => 'pending',
                ]);

                $created++;
            } catch (\Throwable $e) {
                Log::error('Failed to generate bill', ['customer_id' => $customer->id, 'error' => $e->getMessage()]);
                $errors[] = $e->getMessage();
            }
        }

        return ['created' => $created, 'errors' => $errors];
    }

    /**
     * Process daily billing for customers that are billed daily.
     * This is intentionally minimal — implement the business rules as needed.
     */
    public function processDailyBilling(Carbon $date = null): array
    {
        $date = $date ?: Carbon::now();
        $created = 0;
        $errors = [];

        $customers = Customer::where('is_active', true)
            ->where('billing_cycle', 'daily')
            ->get();

        foreach ($customers as $customer) {
            try {
                // calculate prorated amount or daily price
                $amount = $customer->daily_price ?? ($customer->package_price / 30 ?? null);

                if ($amount === null) {
                    $errors[] = "Missing daily amount for customer {$customer->id}";
                    continue;
                }

                CustomerBill::create([
                    'customer_id' => $customer->id,
                    'name' => $customer->name,
                    'mobile' => $customer->mobile ?? null,
                    'username' => $customer->username ?? null,
                    'amount' => $amount,
                    'description' => "Daily bill for {$date->toDateString()}",
                    'billing_period' => $date->toDateString(),
                    'due_date' => $date->copy()->addDays(3)->format('Y-m-d'),
                    'year' => $date->year,
                    'month' => $date->month,
                    'status' => 'pending',
                ]);

                $created++;
            } catch (\Throwable $e) {
                Log::error('Failed to create daily bill', ['customer_id' => $customer->id, 'error' => $e->getMessage()]);
                $errors[] = $e->getMessage();
            }
        }

        return ['created' => $created, 'errors' => $errors];
    }
}
<?php

namespace App\Services;

class BillingService
{
    //
}
