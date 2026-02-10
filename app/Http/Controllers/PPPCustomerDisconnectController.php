<?php

namespace App\Http\Controllers;

use App\Jobs\DisconnectPPPCustomer;
use App\Models\Freeradius\customer;
use App\Models\Freeradius\nas;
use App\Models\Freeradius\radacct;
use Illuminate\Support\Facades\Log;
use RouterOS\Sohag\RouterosAPI;

class PPPCustomerDisconnectController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function create(customer $customer)
    {
        $this->authorize('disconnect', $customer);

        $connection = config('app.env') == 'production' ? 'redis' : 'database';
        DisconnectPPPCustomer::dispatch($customer)
            ->onConnection($connection)
            ->onQueue('disconnect');

        return 'Success';
    }

    /**
     * Disconnect PPPoE Customer
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return int
     */

    public static function disconnect(customer $customer)
    {
        $connection = config('app.env') == 'production' ? 'redis' : 'database';
        DisconnectPPPCustomer::dispatch($customer)
            ->onConnection($connection)
            ->onQueue('disconnect');
        return 0;
    }

    /**
     * Disconnect PPPoE Customer
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return int
     */

    public static function handle(customer $customer)
    {
        $radaccts_count = $customer->radaccts->whereNull('acctstoptime')->count();

        // offline
        if ($radaccts_count == 0) {
            self::logDisconnect($customer, 'offline');
            return 0;
        }

        // get nasidentifier
        $radacct = $customer->radaccts->whereNull('acctstoptime')->first();
        $nasidentifier = $radacct->nasidentifier;

        // nasidentifier not set
        if (strlen($nasidentifier) == 0) {
            self::logDisconnect($customer, 'Null Nasidentifier, Using Rad Client');
            return self::useRadclient($radacct);
        }

        $router = NasIdentifierController::getRouter($nasidentifier);
        if ($router) {
            // use Api #1 (formatted nasidentifier)
            $api_response = self::useApi($customer, $router);
            if ($api_response == 0) {
                self::logDisconnect($customer, 'API Failed');
                // use Rad client #2 (formatted nasidentifier)
                $nasipaddress = NasIdentifierController::getNasIpAddress($nasidentifier);
                if ($nasipaddress == $radacct->nasipaddress) {
                    self::useRadclient($radacct);
                } else {
                    self::logDisconnect($customer, 'Request from NATTED IP, Not using Rad Client, Status: Failed');
                }
            }
        } else {
            // unformatted nasidentifier
            self::logDisconnect($customer, 'Router Not Found, Using Rad Client');
            return self::useRadclient($radacct);
        }

        return 0;
    }

    /**
     * Disconnect using Rad Client
     *
     * @param  \App\Models\Freeradius\radacct  $radacct
     * @return int
     */
    public static function useRadclient(radacct $radacct)
    {
        $discon_cmd = 'echo \'Framed-IP-Address=' . $radacct->framedipaddress . '\' | /usr/bin/radclient ' . $radacct->nasipaddress . ':3799 disconnect \'' . "5903963829" . '\'';
        $output = exec($discon_cmd);
        Log::channel('customer_disconnect')->debug($radacct->mgid . '::' . $radacct->username . '::' . 'rad client output::' . $output);
        return 0;
    }

    /**
     * Disconnect using API
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @param  \App\Models\Freeradius\nas  $router
     * @return int
     */
    public static function useApi(customer $customer, nas $router)
    {
        if (!strlen($router->api_username) || !strlen($router->api_password)) {
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

        $rows = $api->getMktRows('ppp_active', ['name' => $customer->username]);

        $api->removeMktRows('ppp_active', $rows);

        self::logDisconnect($customer, 'API Success');

        return 1;
    }

    /**
     * Logging disconnect process
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @param  string $string
     * @return int
     */
    public static function logDisconnect(customer $customer,  string $string)
    {
        Log::channel('customer_disconnect')->debug($customer->mgid . '::' . $customer->username . '::' . $string);

        return 1;
    }
}
