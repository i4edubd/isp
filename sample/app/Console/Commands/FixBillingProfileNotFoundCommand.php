<?php

namespace App\Console\Commands;

use App\Http\Controllers\CacheController;
use App\Models\billing_profile;
use App\Models\Freeradius\customer;
use Illuminate\Console\Command;

class FixBillingProfileNotFoundCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix_not_found:billing_profile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $customers = customer::where('connection_type', '!=', 'Hotspot')->get();

        $bar = $this->output->createProgressBar(count($customers));
        $bar->start();

        foreach ($customers as $customer) {

            billing_profile::where('id', $customer->billing_profile_id)->firstOr(function () use ($customer) {
                $info = 'billing_profile Not Found';
                $info .= ' Server: ' . config('app.name');
                $info .= ' Operator: ' . $customer->operator_id;
                $info .= ' Customer: ' . $customer->id;
                $info .= ' billing_profile_id: ' . $customer->billing_profile_id;
                $this->info($info);

                $operator = CacheController::getOperator($customer->operator_id);
                if (!$operator) {
                    $customer->delete();
                    return 1;
                }
                $billing_profiles = $operator->billing_profiles;
                $billing_type = $customer->billing_type;
                $billing_profiles = $billing_profiles->filter(function ($billing_profile) use ($billing_type) {
                    return $billing_profile->billing_type == $billing_type;
                });
                $billing_profile = $billing_profiles->first();
                if ($billing_profile) {
                    $customer->billing_profile_id = $billing_profile->id;
                    $customer->save();
                    $this->warn('Problem Fixed');
                }
            });

            $bar->advance();
        }

        $bar->finish();

        return 0;
    }
}
