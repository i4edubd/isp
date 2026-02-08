<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\CacheController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Ipv4addressController;
use App\Models\Freeradius\customer;
use App\Models\ipv4address;
use App\Models\pgsql\pgsql_radreply;
use RouterOS\Sohag\RouterosAPI;

class PPPoECustomersFramedIPAddressController extends Controller
{

    /**
     * Update the specified resource in storage or Store a newly created resource in storage.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return  int
     */
    public static function updateOrCreate(customer $customer)
    {
        if ($customer->connection_type !== 'PPPoE') {
            return 0;
        }

        // pppoe_profile
        $package = CacheController::getPackage($customer->package_id);
        if (!$package) {
            return 0;
        }
        $master_package = $package->master_package;
        $pppoe_profile = $master_package->pppoe_profile;

        //updateOrCreate Framed-IP-Address Attribute
        $operator = CacheController::getOperator($customer->operator_id);
        $radreply = new pgsql_radreply();
        $radreply->setConnection($operator->pgsql_connection);
        if ($pppoe_profile->ip_allocation_mode === 'dynamic' && $customer->status === 'active') {
            $radreply->where('username', $customer->username)->where('attribute', 'Framed-IP-Address')->delete();
            ipv4address::where('operator_id', $customer->operator_id)->where('customer_id', $customer->id)->delete();
            $customer->login_ip = null;
            $customer->save();
        } else {
            // acquire ipv4 address
            $ip_address = Ipv4addressController::getCustomersIpv4Address($customer);
            // error
            if ($ip_address == 0) {
                return 2;
            }
            $radreply->updateOrCreate(
                [
                    'mgid' => $customer->mgid,
                    'customer_id' => $customer->id,
                    'username' => $customer->username,
                    'attribute' => 'Framed-IP-Address',
                ],
                [
                    'value' => long2ip($ip_address),
                ]
            );
            //update customer's login_ip Attribute
            $customer->login_ip = long2ip($ip_address);
            $customer->save();
        }

        // << mikrotik
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
            if ($pppoe_profile->ip_allocation_mode === 'dynamic' && $customer->status === 'active') {
                $api->unsetMktRows('ppp_secret', $exist_rows, 'remote-address');
            } else {
                $exist_row = array_shift($exist_rows);
                $api->editMktRow('ppp_secret', $exist_row, ['remote-address' => $customer->login_ip]);
            }
        }

        return 1;
    }
}
