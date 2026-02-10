<?php

namespace App\Http\Controllers;

use App\Models\Freeradius\customer;
use App\Models\Freeradius\nas;
use Illuminate\Http\Request;
use RouterOS\Sohag\RouterosAPI;

class RouterToRadiusController extends Controller
{

    /**
     * Transfer Online Customers from Router to Radius.
     *
     * @param  \App\Models\Freeradius\nas $router
     * @param  \App\Models\Freeradius\customer $customer
     * @return  int
     */
    public static function transfer(nas $router, customer $customer)
    {
        //API
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

        // <<disable customer
        $edit = ["disabled" => "yes"];

        $router_customers = $api->getMktRows('ppp_secret', ['name' => $customer->username]);

        while ($router_customer = array_shift($router_customers)) {

            $api->editMktRow('ppp_secret', $router_customer, $edit);
        }
        // disable customer>>

        // <<delete customer
        // $get_rows = $api->getMktRows('ppp_secret', ['name' => $customer->username]);
        // $api->removeMktRows('ppp_secret', $get_rows);
        // delete customer >>

        // <<disconnect customer
        $rows = $api->getMktRows('ppp_active', ['name' => $customer->username]);

        $api->removeMktRows('ppp_active', $rows);
        // disconnect customer>>

        return 1;
    }
}
