<?php

namespace App\Console\Commands;

use App\Http\Controllers\Customer\PPPoECustomersFramedIPAddressController;
use App\Http\Controllers\PPPCustomerDisconnectController;
use App\Models\Freeradius\customer;
use App\Models\ipv4address;
use App\Models\operator;
use Illuminate\Console\Command;

class FixMissingIpv4Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:missingIpv4 {mgid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix Missing IPv4 Addresses';

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

            $countIpv4 = ipv4address::where('operator_id', $customer->operator_id)
                ->where('customer_id', $customer->id)->count();

            if ($countIpv4 == 0) {
                $this->warn('IPv4 Addreess not found => operator_id : '  . $customer->operator_id . ' customer id: ' . $customer->id);
                $this->warn('False IP : ' . $customer->login_ip);
                PPPoECustomersFramedIPAddressController::updateOrCreate($customer);
                PPPCustomerDisconnectController::disconnect($customer);
                $freshCustomer = $customer->fresh();
                $this->warn('New IP : ' . $freshCustomer->login_ip);
            }

            $bar->advance();
        }

        $bar->finish();


        return 0;
    }
}
