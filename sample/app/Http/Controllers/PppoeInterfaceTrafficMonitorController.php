<?php

namespace App\Http\Controllers;

use App\Models\Freeradius\radacct;
use RouterOS\Sohag\RouterosAPI;

class PppoeInterfaceTrafficMonitorController extends Controller
{

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Freeradius\radacct $radacct
     * @return \Illuminate\Http\Response
     */
    public function show(radacct $radacct)
    {
        $customer = $radacct->customer;

        $data = [];
        $data['name'] = $customer->name;
        $data['username'] = $customer->username;
        $data['package_name'] = $customer->package_name;

        // Offline
        if (strlen($radacct->acctstoptime)) {
            $data["status"] = "Offline";
            $data["upload"] = 0;
            $data["download"] = 0;
            return json_encode($data);
        }

        $nasidentifier = $radacct->nasidentifier;

        if (strlen($nasidentifier) == 0) {
            $data["status"] = "Nas Identifier Not Found";
            $data["upload"] = 0;
            $data["download"] = 0;
            return json_encode($data);
        }

        $router = NasIdentifierController::getRouter($nasidentifier);

        // Bad System Identity
        if (!$router) {
            $data["status"] = "Bad System Identity Or NAS Not Found";
            $data["upload"] = 0;
            $data["download"] = 0;
            return json_encode($data);
        }

        // unknown router
        if ($router->mgid == 0) {
            $data["status"] = "Unknown NAS";
            $data["upload"] = 0;
            $data["download"] = 0;
            return json_encode($data);
        }

        $config  = [
            'host' => $router->nasname,
            'user' => $router->api_username,
            'pass' => $router->api_password,
            'port' => $router->api_port,
            'attempts' => 1,
            'debug' => false,
        ];

        $api = new RouterosAPI($config);

        // Auth Failed
        if (!$api->connect($router->nasname, $router->api_username, $router->api_password)) {
            $data["status"] = "Auth Failed";
            $data["upload"] = 0;
            $data["download"] = 0;
            return json_encode($data);
        }

        // read traffic
        $interface = "<pppoe-$customer->username>";
        $api->write("/interface/monitor-traffic", false);
        $api->write("=interface=" . $interface, false);
        $api->write("=once=", true);
        $ret = $api->read();
        $msg = array_shift($ret);
        $msg_count = count($msg);

        // status
        if ($msg_count == 1) {
            $data["status"] = "Offline";
            $data["upload"] = 0;
            $data["download"] = 0;
            return json_encode($data);
        } else {
            $data["status"] = "Online";
            $data["upload"] = array_key_exists('rx-bits-per-second', $msg) ? $msg["rx-bits-per-second"] : 0;
            $data["download"] = array_key_exists('tx-bits-per-second', $msg) ? $msg["tx-bits-per-second"] : 0;
            $data['readable_upload'] = self::getReadable($data["upload"]);
            $data['readable_download'] = self::getReadable($data["download"]);
            return json_encode($data);
        }
    }

    /**
     * Get Readable Upload Or Download
     *
     * @param  int $bps
     * @return string
     */
    public static function getReadable(int $bps)
    {
        if ($bps >= 1000000) {
            return $bps / 1000000 . ' Mbps';
        }

        if ($bps >= 1000) {
            return $bps / 1000 . ' Kbps';
        }

        return $bps . ' bps';
    }
}
