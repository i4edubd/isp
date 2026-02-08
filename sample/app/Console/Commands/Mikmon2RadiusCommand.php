<?php

namespace App\Console\Commands;

use App\Http\Controllers\AllCustomerController;
use App\Http\Controllers\Customer\HotspotCustomersRadAttributesController;
use App\Models\all_customer;
use App\Models\Freeradius\customer;
use App\Models\master_package;
use App\Models\operator;
use App\Models\package;
use Carbon\Carbon;
use Illuminate\Console\Command;
use RouterOS\Sohag\RouterosAPI;

class Mikmon2RadiusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mikmon2radius {router_ip} {user} {password} {port} {oid}';

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
        $operator = operator::findOrFail($this->argument('oid'));

        $info = "Name: " . $operator->name . " Email: " . $operator->email . " Company: " . $operator->company;
        $this->info($info);

        // confirm before process
        if ($this->confirm('Do you wish to continue?') == false) {
            return 0;
        }

        $config  = [
            'host' => $this->argument('router_ip'),
            'user' => $this->argument('user'),
            'pass' => $this->argument('password'),
            'port' => $this->argument('port'),
            'attempts' => 1
        ];

        $api = new RouterosAPI($config);

        if (!$api->connect($config['host'], $config['user'], $config['pass'])) {

            $this->error('could not connect to the router');
        }

        $rows = $api->getMktRows('hotspot_user');

        $this->info("Total Row Count: " . count($rows));

        $interested_rows = [];
        foreach ($rows as $row) {
            if (($row['bytes-in'] == "0") || (count($row) < 9) || (count(explode('/', $row['comment'])) != 3)) {
                continue;
            } else {
                $interested_rows[] = $row;
            }
        }
        $rows = [];

        $this->info("Interested Rows Count: " . count($interested_rows));

        foreach ($interested_rows as $interested_row) {

            $package = package::where('name', $interested_row['profile'])->firstOr(function () use ($operator, $interested_row) {

                $master_package = master_package::where('connection_type', 'Hotspot')
                    ->where('mgid', $operator->mgid)
                    ->where('name', $interested_row['profile'])
                    ->first();

                if ($master_package) {
                    if ($operator->role == 'operator' || $operator->role == 'group_admin') {
                        $package = new package();
                        $package->mgid = $operator->mgid;
                        $package->gid = $operator->gid;
                        $package->operator_id = $operator->id;
                        $package->mpid = $master_package->id;
                        $package->name = $master_package->name;
                        $package->price = 1;
                        $package->save();
                        $package->ppid = $package->id;
                        $package->save();
                        return $package;
                    }

                    if ($operator->role == 'sub_operator') {
                        $gadmin = operator::find($operator->gid);
                        $package = new package();
                        $package->mgid = $gadmin->mgid;
                        $package->gid = $gadmin->gid;
                        $package->operator_id = $gadmin->id;
                        $package->mpid = $master_package->id;
                        $package->name = $master_package->name;
                        $package->price = 1;
                        $package->save();
                        $package->ppid = $package->id;
                        $package->save();

                        $sub_package = new package();
                        $sub_package->mgid = $operator->mgid;
                        $sub_package->gid = $operator->gid;
                        $sub_package->operator_id = $operator->id;
                        $sub_package->mpid = $master_package->id;
                        $sub_package->ppid = $package->id;
                        $sub_package->name = $master_package->name;
                        $sub_package->price = 1;
                        $sub_package->save();
                        return $sub_package;
                    }
                }

                return null;
            });

            if (!$package) {
                $this->info("Package Not Found");
                continue;
            }

            $exp_date = Carbon::createFromFormat('M/d/Y H:i:s', $interested_row['comment'], getTimeZone($operator->id))->isoFormat(config('app.expiry_time_format'));
            $master_package = $package->master_package;

            // duplicate
            $model = new customer();
            $model->setConnection($operator->node_connection);
            if ($model->where('username', $interested_row['mac-address'])->count()) {
                continue;
            }

            $customer = new customer();
            $customer->setConnection($operator->node_connection);
            $customer->mgid = $operator->mgid;
            $customer->gid = $operator->gid;
            $customer->operator_id = $operator->id;
            $customer->company = $operator->company;
            $customer->connection_type = 'Hotspot';
            $customer->name = $interested_row['name'];
            $customer->mobile = null;
            $customer->username = $interested_row['mac-address'];
            $customer->password = $interested_row['mac-address'];
            $customer->package_id = $package->id;
            $customer->package_name = $package->name;
            $customer->rate_limit = $master_package->rate_limit;
            $customer->total_octet_limit = $master_package->total_octet_limit;
            $customer->package_started_at = Carbon::now(getTimeZone($operator->id))->isoFormat(config('app.expiry_time_format'));
            $customer->package_expired_at = $exp_date;
            $customer->exptimestamp = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($operator->id), 'en')->timestamp;
            $customer->registration_date = date(config('app.date_format'));
            $customer->registration_week = date(config('app.week_format'));
            $customer->registration_month = date(config('app.month_format'));
            $customer->registration_year = date(config('app.year_format'));
            $customer->save();
            $customer->parent_id = $customer->id;
            $customer->save();

            AllCustomerController::updateOrCreate($customer);

            HotspotCustomersRadAttributesController::updateOrCreate($customer);
        }

        return 0;
    }
}
