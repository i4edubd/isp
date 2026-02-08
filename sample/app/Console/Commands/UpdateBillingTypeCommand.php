<?php

namespace App\Console\Commands;

use App\Models\billing_profile;
use App\Models\Freeradius\customer;
use App\Models\operator;
use Illuminate\Console\Command;

class UpdateBillingTypeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:billing_type';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update billing_type';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (config('local.host_type') !== 'central') {
            return 0;
        }

        $madmins = operator::where('role', 'group_admin')->get();

        foreach ($madmins as $madmin) {

            $this->info('Processing admin of id : ' . $madmin->id);

            $model = new customer();
            $model->setConnection($madmin->node_connection);
            $customers = $model->where('mgid', $madmin->id)->get();

            while ($customer = $customers->shift()) {
                switch ($customer->connection_type) {
                    case 'PPPoE':
                        $billing_profile = billing_profile::findOrFail($customer->billing_profile_id);
                        if ($billing_profile->billing_type == 'Daily') {
                            $customer->billing_type = 'Daily';
                            $customer->save();
                        } elseif ($billing_profile->billing_type == 'Free') {
                            $customer->billing_type = 'Free';
                            $customer->save();
                        } else {
                        }
                        break;
                    case 'Hotspot':
                        $customer->billing_type = 'Daily';
                        $customer->save();
                        break;
                    case 'StaticIp':
                    case 'Other':
                        break;
                }
            }
        }

        return 0;
    }
}
