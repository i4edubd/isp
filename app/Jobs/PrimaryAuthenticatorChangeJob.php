<?php

namespace App\Jobs;

use App\Http\Controllers\CacheController;
use App\Models\backup_setting;
use App\Models\Freeradius\customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use RouterOS\Sohag\RouterosAPI;

class PrimaryAuthenticatorChangeJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The backup_setting instance.
     *
     * @var \App\Models\backup_setting
     */
    public $backup_setting;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public $uniqueFor = 3600;

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->backup_setting->id;
    }

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(backup_setting $backup_setting)
    {
        $this->backup_setting = $backup_setting;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $backup_setting = $this->backup_setting;

        $operator = CacheController::getOperator($backup_setting->operator_id);

        if (!$operator) {
            return 0;
        }

        $router = CacheController::getNas($operator->id, $backup_setting->nas_id);

        if (!$router) {
            return 0;
        }

        $config  = [
            'host' => $router->nasname,
            'user' => $router->api_username,
            'pass' => $router->api_password,
            'port' => $router->api_port,
            'attempts' => 1
        ];

        $api = new RouterosAPI($config);

        if (!$api->connect($config['host'], $config['user'], $config['pass'])) {
            return 0;
        }

        $model = new customer();
        $model->setConnection($operator->node_connection);
        $customers = $model->where('operator_id', $operator->id)
            ->where('connection_type', 'PPPoE')
            ->get();

        $new_secrets = [];

        foreach ($customers as $customer) {
            $exist_rows = $api->getMktRows('ppp_secret', ["name" => $customer->username]);
            if (count($exist_rows)) {
                // update 
                $exist_row = array_shift($exist_rows);
                if ($backup_setting->primary_authenticator === 'Radius') {
                    $api->editMktRow('ppp_secret', $exist_row, ['disabled' => 'yes']);
                } else {
                    $api->editMktRow('ppp_secret', $exist_row, ['disabled' => 'no']);
                }
            } else {
                // create
                $package = CacheController::getPackage($customer->package_id);
                if (!$package) {
                    continue;
                }
                $master_package = $package->master_package;
                $pppoe_profile = $master_package->pppoe_profile;
                $ppp_secret = [];
                $ppp_secret['name'] = $customer->username;
                $ppp_secret['password'] = $customer->password;
                $ppp_secret['profile'] = $pppoe_profile->name;
                if ($pppoe_profile->ip_allocation_mode === 'static') {
                    $ppp_secret['remote-address'] = $customer->login_ip;
                }
                if ($backup_setting->primary_authenticator === 'Radius') {
                    $ppp_secret['disabled'] = 'yes';
                } else {
                    $ppp_secret['disabled'] = 'no';
                }
                $new_secrets[] = $ppp_secret;
            }
        }

        if (count($new_secrets)) {
            $api->addMktRows('ppp_secret', $new_secrets);
        }

        return 1;
    }
}
