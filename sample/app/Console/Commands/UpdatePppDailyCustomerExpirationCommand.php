<?php

namespace App\Console\Commands;

use App\Http\Controllers\CacheController;
use App\Http\Controllers\Customer\PPPoECustomersExpirationController;
use App\Http\Controllers\PPPCustomerDisconnectController;
use App\Models\Freeradius\customer;
use App\Models\operator;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdatePppDailyCustomerExpirationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:ppp_daily_customer_expiration {mgid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update ppp daily customer expiration';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $mgid = $this->argument('mgid');
        $madmin = operator::findOrFail($mgid);

        $info = "Name: " . $madmin->name . " Email: " . $madmin->email . " Company: " . $madmin->company;
        $this->info($info);

        if ($this->confirm('Do you wish to continue?') == false) {
            return 0;
        }

        $model = new customer();
        $model->setConnection($madmin->node_connection);
        $customers = $model->where('mgid', $madmin->mgid)
            ->where('connection_type', 'PPPoE')
            ->get();

        $bar = $this->output->createProgressBar(count($customers));
        $bar->start();

        while ($customer = $customers->shift()) {

            $billing_profile = CacheController::getBillingProfile($customer->billing_profile_id);
            $customer->billing_type = $billing_profile->billing_type;
            $customer->save();

            if ($customer->billing_type == 'Daily') {
                PPPoECustomersExpirationController::updateOrCreate($customer);
                if (Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at)->lessThan(Carbon::now(config('app.timezone')))) {
                    PPPCustomerDisconnectController::disconnect($customer);
                    $this->warn("Disconnecting ...");
                }
            }

            $bar->advance();
        }

        $bar->finish();


        return 0;
    }
}
