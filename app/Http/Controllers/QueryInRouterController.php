<?php

namespace App\Http\Controllers;

use App\Models\Freeradius\customer;
use Illuminate\Support\Facades\Log;
use RouterOS\Sohag\RouterosAPI;

class QueryInRouterController extends Controller
{
    /**
     * Get Online Status
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return int
     */
    public static function getOnlineStatus(customer $customer)
    {
        $backup_setting = CacheController::getBackupSettings($customer->operator_id);
        if (!$backup_setting) {
            return 0;
        }

        $nas = CacheController::getNas($customer->operator_id, $backup_setting->nas_id);
        if (!$nas) {
            return 0;
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
            return 0;
        }

        $active_connections = [];
        if ($customer->connection_type == 'PPPoE') {
            $active_connections = $api->getMktRows('ppp_active', ['name' => $customer->username]);
        }
        if ($customer->connection_type == 'Hotspot') {
            $active_connections = $api->getMktRows('hotspot_active', ['user' => $customer->username]);
        }

        if (is_array($active_connections)) {
            return count($active_connections);
        }

        return 0;
    }
}
