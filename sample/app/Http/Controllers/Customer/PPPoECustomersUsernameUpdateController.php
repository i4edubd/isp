<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\CacheController;
use App\Http\Controllers\Controller;
use App\Models\Freeradius\customer;
use Illuminate\Http\Request;
use RouterOS\Sohag\RouterosAPI;

class PPPoECustomersUsernameUpdateController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @param  string  $old_username
     * @return  int
     */

    public static function update(customer $customer, string $old_username)
    {

        # In Radius Username updated by foreign key

        if ($customer->connection_type !== 'PPPoE') {
            return 0;
        }

        if ($customer->username == $old_username) {
            return 0;
        }

        $operator = CacheController::getOperator($customer->operator_id);

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
        $exist_rows = $api->getMktRows('ppp_secret', ["name" => $old_username]);

        if (count($exist_rows)) {

            $exist_row = array_shift($exist_rows);

            $api->editMktRow('ppp_secret', $exist_row, ['name' => $customer->username]);

            return 0;
        }
    }
}
