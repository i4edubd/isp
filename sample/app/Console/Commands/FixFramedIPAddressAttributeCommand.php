<?php

namespace App\Console\Commands;

use App\Http\Controllers\Customer\PPPoECustomersFramedIPAddressController;
use App\Http\Controllers\Ipv4addressController;
use App\Http\Controllers\PPPCustomerDisconnectController;
use App\Models\Freeradius\customer;
use App\Models\operator;
use Illuminate\Console\Command;

class FixFramedIPAddressAttributeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:FramedIPAddressAttribute {mgid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix Framed-IP-Address Attribute';

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

            $ip_address = Ipv4addressController::getCustomersIpv4Address($customer);
            $assigned_ip = long2ip($ip_address);

            if ($customer->login_ip !== $assigned_ip) {
                $this->warn('Assigned IP : ' . $assigned_ip);
                $this->warn('False IP : ' . $customer->login_ip);
                $this->warn('Customer Info: ' . ' customer id => ' . $customer->id . ' operator id => ' . $customer->operator_id);
                PPPoECustomersFramedIPAddressController::updateOrCreate($customer);
                PPPCustomerDisconnectController::disconnect($customer);
            }

            $bar->advance();
        }

        $bar->finish();

        return 0;
    }
}
