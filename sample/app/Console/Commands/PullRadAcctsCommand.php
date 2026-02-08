<?php

namespace App\Console\Commands;

use App\Http\Controllers\CacheController;
use App\Http\Controllers\NasIdentifierController;
use App\Models\backup_setting;
use App\Models\Freeradius\customer;
use App\Models\Freeradius\nas;
use App\Models\Freeradius\radacct;
use Carbon\Carbon;
use Illuminate\Console\Command;
use RouterOS\Sohag\RouterosAPI;

class PullRadAcctsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pull:radaccts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull Radaccts From MikroTik | will run both in central and node machines';

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
        // get master admins using this machine
        $nas_all = nas::all();
        $routers_group = $nas_all->filter(function ($router) {
            return $router->mgid > 0;
        })->groupBy('mgid');

        // for each master admin
        foreach ($routers_group as $mgid => $routers) {

            $backup_settings = backup_setting::where('mgid', $mgid)->get();

            // for each backup setting
            foreach ($backup_settings as $backup_setting) {

                if ($backup_setting->primary_authenticator === 'Radius') {
                    continue;
                }

                $operator = CacheController::getOperator($backup_setting->operator_id);

                if (!$operator) {
                    continue;
                }

                $nas = CacheController::getNas($operator->id, $backup_setting->nas_id);

                if (!$nas) {
                    continue;
                }

                $config  = [
                    'host' => $nas->nasname,
                    'user' => $nas->api_username,
                    'pass' => $nas->api_password,
                    'port' => $nas->api_port,
                    'attempts' => 1
                ];

                $api = new RouterosAPI($config);

                if (!$api->connect($config['host'], $config['user'], $config['pass'])) {
                    continue;
                }

                $customers = customer::where('operator_id', $operator->id)
                    ->where('connection_type', 'PPPoE')
                    ->get();

                $online_customers = radacct::where('operator_id', $operator->id)
                    ->whereNull('acctstoptime')
                    ->get();

                $active_connections = $api->getMktRows('ppp_active');

                // process active connections
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

                    // R (get customer info)
                    if ($customers->where('username', $active_connection['name'])->count() == 0) {
                        continue;
                    }

                    $customer = $customers->where('username', $active_connection['name'])->first();

                    // C (Accounting start)
                    if ($online_customers->where('username', $active_connection['name'])->count() == 0) {
                        $radacct = new radacct();
                        $radacct->mgid = $customer->mgid;
                        $radacct->operator_id = $customer->operator_id;
                        $radacct->acctsessionid = Carbon::now()->timestamp;
                        $radacct->acctuniqueid = Carbon::now()->timestamp;
                        $radacct->username = $customer->username;
                        $radacct->nasipaddress = $nas->nasname;
                        $radacct->nasidentifier = NasIdentifierController::getIdentifier($operator, $nas);
                        $radacct->acctstarttime = Carbon::now();
                        $radacct->acctupdatetime = Carbon::now();
                        $radacct->acctsessiontime = 0;
                        $radacct->acctinputoctets = 0;
                        $radacct->acctoutputoctets = 0;
                        $radacct->callingstationid = $active_connection['caller-id'];
                        $radacct->acctterminatecause = "null";
                        $radacct->framedipaddress = $active_connection['address'];
                        $radacct->save();
                    }

                    // U (Intrim update)
                    if ($online_customers->where('username', $active_connection['name'])->count() == 1) {
                        $radacct = $online_customers->where('username', $active_connection['name'])->first();
                        $radacct->acctupdatetime = Carbon::now();
                        $radacct->acctsessiontime = Carbon::now()->diffInSeconds(Carbon::createFromFormat('Y-m-d H:i:s', $radacct->acctstarttime));
                        $radacct->nasidentifier = NasIdentifierController::getIdentifier($operator, $nas);
                        $radacct->save();
                        $online_customers =  $online_customers->except([$radacct->id]);
                    }
                }

                // D (Accounting stop)
                foreach ($online_customers as $online_customer) {
                    $online_customer->acctstoptime = Carbon::now();
                    $online_customer->acctsessiontime = Carbon::now()->diffInSeconds(Carbon::createFromFormat('Y-m-d H:i:s', $online_customer->acctstarttime));
                    $online_customer->save();
                }
            }
        }
    }
}
