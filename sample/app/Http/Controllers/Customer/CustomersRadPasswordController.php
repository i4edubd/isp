<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\CacheController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PppProfilePushController;
use App\Models\Freeradius\customer;
use App\Models\pgsql\pgsql_radcheck;
use Illuminate\Http\Request;
use RouterOS\Sohag\RouterosAPI;

class CustomersRadPasswordController extends Controller
{

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return  int
     */
    public static function updateOrCreate(customer $customer)
    {

        // << radius

        # Username updated by foreign key

        $operator = CacheController::getOperator($customer->operator_id);

        $radcheck = new pgsql_radcheck();

        $radcheck->setConnection($operator->pgsql_connection);

        if ($customer->status == 'disabled') {
            $radcheck->updateOrCreate(
                [
                    'mgid' => $customer->mgid,
                    'customer_id' => $customer->id,
                    'username' => $customer->username,
                    'attribute' => 'Cleartext-Password',
                ],
                [
                    'value' => rand(1000, 9999),
                ]
            );
        } else {
            $radcheck->updateOrCreate(
                [
                    'mgid' => $customer->mgid,
                    'customer_id' => $customer->id,
                    'username' => $customer->username,
                    'attribute' => 'Cleartext-Password',
                ],
                [
                    'value' => $customer->password,
                ]
            );
        }

        // radius >>

        // << mikrotik
        if ($customer->connection_type !== 'PPPoE') {
            return 0;
        }

        $backup_setting = CacheController::getBackupSettings($operator->id);

        if (!$backup_setting) {
            return 0;
        }

        if ($backup_setting->primary_authenticator !== 'Router') {
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

        // update
        $exist_rows = $api->getMktRows('ppp_secret', ["name" => $customer->username]);

        if (count($exist_rows)) {

            $exist_row = array_shift($exist_rows);

            $api->editMktRow('ppp_secret', $exist_row, ['password' => $customer->password]);

            return 0;
        }

        // create

        $package = CacheController::getPackage($customer->package_id);

        // << push ppp profile || dependency
        $router_rows = $api->getMktRows('ppp_profile', ["name" => $package->master_package->pppoe_profile->name]);

        if (count($router_rows)) {
            // nothing to do
        } else {
            PppProfilePushController::store($package->master_package->pppoe_profile, $router);
        }
        // push ppp profile || dependency >>

        $ppp_secret = [];
        $ppp_secret['name'] = $customer->username;
        $ppp_secret['password'] = $customer->password;
        $ppp_secret['profile'] = $package->master_package->pppoe_profile->name;
        $ppp_secret['disabled'] = 'no';
        $new_secrets = [];
        $new_secrets[] = $ppp_secret;
        $api->addMktRows('ppp_secret', $new_secrets);
        return 0;
        // mikrotik >>
    }
}
