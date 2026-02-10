<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\CustomerBill;
use Carbon\Carbon;

class GenerateMonthlyBills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:generate-monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly bills for all active customers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to generate monthly bills...');

        $today = Carbon::now();
        $customers = Customer::where('is_active', true)->get();

        foreach ($customers as $customer) {
            // Basic check to prevent duplicate bills. The DB constraint is the real safeguard.
            $billExists = CustomerBill::where('customer_id', $customer->id)
                ->where('year', $today->year)
                ->where('month', $today->month)
                ->exists();

            if ($billExists) {
                $this->warn("Bill for customer #{$customer->id} ({$customer->username}) already exists for {$today->format('F Y')}. Skipping.");
                continue;
            }
            
            // This is a simplified example. A real implementation would have more complex logic
            // to determine the billing amount, package details, etc.
            // We'll assume the customer has a billing_profile with the necessary info.
            if ($customer->billingProfile && $customer->billingProfile->package) {
                $billingProfile = $customer->billingProfile;
                $package = $billingProfile->package;

                CustomerBill::create([
                    'mgid' => $customer->mgid,
                    'gid' => $customer->gid,
                    'operator_id' => $customer->operator_id,
                    'customer_id' => $customer->id,
                    'package_id' => $package->id,
                    'validity_period' => 30, // Assuming monthly
                    'customer_zone_id' => $customer->customer_zone_id,
                    'name' => $customer->name,
                    'mobile' => $customer->mobile,
                    'username' => $customer->username,
                    'amount' => $package->price, // Simplified
                    'description' => "Monthly bill for {$package->name}.",
                    'billing_period' => $today->format('F Y'),
                    'due_date' => $today->addDays(15)->format('Y-m-d'), // Due in 15 days
                    'year' => $today->year,
                    'month' => $today->month,
                ]);

                $this->line("Successfully generated bill for customer #{$customer->id} ({$customer->username}).");
            } else {
                 $this->error("Customer #{$customer->id} ({$customer->username}) is missing a valid billing profile or package. Skipping.");
            }
        }

        $this->info('Finished generating monthly bills.');
        return 0;
    }
}