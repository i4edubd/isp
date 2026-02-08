<?php

namespace App\Console\Commands;

use App\Http\Controllers\Customer\PPPoECustomersFramedIPAddressController;
use App\Models\Freeradius\customer;
use App\Models\ipv4address;
use App\Models\operator;
use Illuminate\Console\Command;

class DuplicateIpv4addressCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'constraint_violation_check:duplicate_ipv4address';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Duplicate Ipv4address';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

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

            $operators = operator::where('mgid', $madmin->id)->get();

            foreach ($operators as $operator) {

                $duplicates = [];

                $ipv4addresses = ipv4address::where('operator_id', $operator->id)->get();

                foreach ($ipv4addresses as $ipv4address) {

                    if ($ipv4addresses->where('ip_address', $ipv4address->ip_address)->count() > 1) {

                        $duplicates[] = $ipv4address->ip_address;
                    }
                }

                if (count($duplicates)) {
                    $this->info('mgid : ' . $madmin->id . ' operator id: ' . $operator->id . ' duplicate ip count : ' . count($duplicates));

                    $duplicates = array_unique($duplicates);

                    $customer_ids = [];

                    foreach ($duplicates as $duplicate) {
                        $ipv4s = ipv4address::where('operator_id', $operator->id)
                            ->where('ip_address', $duplicate)->get();

                        foreach ($ipv4s as $ipv4) {
                            $customer_ids[] = $ipv4->customer_id;
                            $ipv4->delete();
                        }
                    }

                    foreach ($customer_ids as $customer_id) {

                        $model = new customer();
                        $model->setConnection($operator->node_connection);
                        $customer = $model->find($customer_id);

                        if ($customer) {
                            PPPoECustomersFramedIPAddressController::updateOrCreate($customer);
                        }
                    }
                }
            }
        }

        return 0;
    }
}
