<?php

namespace App\Console\Commands;

use App\Http\Controllers\NasIdentifierController;
use App\Models\backup_setting;
use App\Models\Freeradius\customer;
use App\Models\Freeradius\nas;
use App\Models\Freeradius\radacct;
use App\Models\operator;
use Carbon\Carbon;
use Illuminate\Console\Command;
use RouterOS\Sohag\RouterosAPI;

class SyncOnlineCustomersWithApiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:online_customers {operator_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync online clients with the router with the help of the API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // for whom ?
        $operator_id = $this->argument('operator_id');
        $operator = operator::findOrFail($operator_id);

        // get router
        $backup_setting = backup_setting::where('operator_id', $operator->id)->where('primary_authenticator', 'Radius')->first();
        if (!$backup_setting) {
            $this->info('Backup & Auth settings not found for the operator.');
            return 0;
        }

        $model = new nas();
        $model->setConnection($operator->node_connection);
        $router = $model->where('id', $backup_setting->nas_id)->first();
        if (!$router) {
            $this->info('Router Not Found.');
            return 0;
        }

        // check API
        $config  = [
            'host' => $router->nasname,
            'user' => $router->api_username,
            'pass' => $router->api_password,
            'port' => $router->api_port,
            'attempts' => 1
        ];
        $api = new RouterosAPI($config);
        if (!$api->connect($config['host'], $config['user'], $config['pass'])) {
            $this->info('Failed to connect API to router.');
            return 0;
        }

        // get customers info
        if ($operator->role == 'manager') {
            $operator_id = $operator->gid;
        } else {
            $operator_id = $operator->id;
        }

        $model = new customer();
        $model->setConnection($operator->node_connection);
        $customers = $model->where('operator_id', $operator_id)->get();
        $this->info('Total Customers Count: ' . $customers->count());

        $model = new radacct();
        $model->setConnection($operator->node_connection);
        $online_customers = $model->where('operator_id', $operator_id)
            ->whereNull('acctstoptime')
            ->get();
        $this->info('Total Online Customers Count: ' . $online_customers->count());

        $this->info('Total Offline Customers Count: ' . $customers->count() - $online_customers->count());

        // get online customers from routers
        $active_connections = $api->getMktRows('ppp_active');

        // process active connections
        $sync_count = 0;
        while ($active_connection = array_shift($active_connections)) {

            $keys_found = array_keys($active_connection);
            $expected_keys = [
                'name',
                'caller-id',
                'address',
            ];
            $key_diff = array_diff($expected_keys, $keys_found);
            if (count($key_diff)) {
                continue;
            }

            // sync (online in router but not in radius)
            if ($online_customers->where('username', $active_connection['name'])->count() == 0) {
                $customer = $customers->where('username', $active_connection['name'])->first();
                try {
                    $radacct = new radacct();
                    $radacct->setConnection($operator->node_connection);
                    $radacct->mgid = $customer->mgid;
                    $radacct->operator_id = $customer->operator_id;
                    $radacct->acctsessionid = Carbon::now()->timestamp;
                    $radacct->acctuniqueid = Carbon::now()->timestamp;
                    $radacct->username = $customer->username;
                    $radacct->nasipaddress = $router->nasname;
                    $radacct->nasidentifier = NasIdentifierController::getIdentifier($operator, $router);
                    $radacct->acctstarttime = Carbon::now();
                    $radacct->acctupdatetime = Carbon::now();
                    $radacct->acctsessiontime = 0;
                    $radacct->acctinputoctets = 0;
                    $radacct->acctoutputoctets = 0;
                    $radacct->callingstationid = $active_connection['caller-id'];
                    $radacct->acctterminatecause = "null";
                    $radacct->framedipaddress = $active_connection['address'];
                    $radacct->save();
                    $sync_count++;
                } catch (\Throwable $th) {
                    $this->info($th);
                }
            }
            // sync (online in radius but not in router)
            // handled with delete:rad_stale_sessions command
        }

        $this->info($sync_count . ' online clients has been synced with the router.');

        return Command::SUCCESS;
    }
}
