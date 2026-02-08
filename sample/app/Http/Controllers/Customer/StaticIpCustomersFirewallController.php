<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Freeradius\customer;
use RouterOS\Sohag\RouterosAPI;

class StaticIpCustomersFirewallController extends Controller
{

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public static function updateOrCreate(customer $customer)
    {

        $router = $customer->router;

        if ($router->id == 0) {
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

        $menu = 'queue';

        if ($customer->status == 'suspended') {

            $rows = [
                [
                    'name' => 'cus_' . $customer->id . '_' . getVarName($customer->name),
                    'target' => $customer->login_ip,
                    'max-limit' => '64k/64k',
                    'comment' => 'cus_' . $customer->id,
                    'place-before' => 0,
                ]
            ];

            $api->addMktRows($menu, $rows);
        } else {

            $rows = $api->getMktRows($menu, ["comment" => 'cus_' . $customer->id]);

            if (count($rows)) {
                $api->removeMktRows($menu, $rows);
            }
        }
    }
}
