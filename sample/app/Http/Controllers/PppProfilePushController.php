<?php

namespace App\Http\Controllers;

use App\Models\Freeradius\nas;
use App\Models\ipv4pool;
use App\Models\ipv6pool;
use App\Models\pppoe_profile;
use Illuminate\Http\Request;
use RouterOS\Sohag\RouterosAPI;

class PppProfilePushController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Models\pppoe_profile $pppoe_profile
     * @param \App\Models\Freeradius\nas $router
     * @return int
     */
    public static function store(pppoe_profile $pppoe_profile, nas $router)
    {
        if ($pppoe_profile->ip_allocation_mode === 'dynamic') {
            return 0;
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

        if (!$api->connect($config['host'], $config['user'], $config['pass'])) {
            return 0;
        }

        // <<push ipv4pool || dependency
        $ipv4pool = ipv4pool::where('id', $pppoe_profile->ipv4pool_id)->firstOr(function () {
            return
                ipv4pool::make([
                    'id' => 0,
                    'name' => 'Not Found',
                ]);
        });

        if ($ipv4pool->id == 0) {
            return 0;
        }

        $ipv4pool_row = [];
        $ipv4pool_row['name'] = $ipv4pool->name;
        $ipv4pool_row['ranges'] = long2ip($ipv4pool->gateway + 1) . '-' . long2ip($ipv4pool->broadcast - 1);

        $router_rows = $api->getMktRows('ip_pool', ['name' => $ipv4pool->name]);

        if (count($router_rows)) {
            $router_row = array_shift($router_rows);
            $api->editMktRow('ip_pool', $router_row, $ipv4pool_row);
        } else {
            $new_ipv4pool_rows = [];
            $new_ipv4pool_rows[] = $ipv4pool_row;
            $api->addMktRows('ip_pool', $new_ipv4pool_rows);
        }
        // push ipv4pool || dependency>>

        // <<push ipv6pool || dependency
        if ($pppoe_profile->ipv6pool_id > 0) {

            $ipv6pool = ipv6pool::where('id', $pppoe_profile->ipv6pool_id)->firstOr(function () {
                return
                    ipv6pool::make([
                        'id' => 0,
                        'name' => 'Not Found',
                    ]);
            });

            if ($ipv6pool->id == 0) {
                return 0;
            }

            $ipv6pool_row = [];
            $ipv6pool_row['name'] = $ipv6pool->name;
            $ipv6pool_row['prefix'] = $ipv6pool->prefix;
            $ipv6pool_row['prefix-length'] = 64;

            $router_rows = $api->getMktRows('ipv6_pool', ['name' => $ipv6pool->name]);

            if (count($router_rows)) {
                $router_row = array_shift($router_rows);
                $api->editMktRow('ipv6_pool', $router_row, $ipv6pool_row);
            } else {
                $new_ipv6pool_rows = [];
                $new_ipv6pool_rows[] = $ipv6pool_row;
                $api->addMktRows('ipv6_pool', $new_ipv6pool_rows);
            }
        }
        // push ipv6pool || dependency>>

        // <<push ppp profile
        $ppp_profile = [];
        $ppp_profile['name'] = $pppoe_profile->name;
        $ppp_profile['local-address'] = long2ip($ipv4pool->gateway);
        $ppp_profile['remote-address'] = $ipv4pool->name;
        if ($pppoe_profile->ipv6pool_id > 0) {
            $ppp_profile['remote-ipv6-prefix-pool'] = $ipv6pool->name;
        }

        $router_rows = $api->getMktRows('ppp_profile', ["name" => $pppoe_profile->name]);

        if (count($router_rows)) {
            $router_row = array_shift($router_rows);
            $api->editMktRow('ppp_profile', $router_row, $ppp_profile);
        } else {
            $ppp_profiles = [];
            $ppp_profiles[] = $ppp_profile;
            $api->addMktRows('ppp_profile', $ppp_profiles);
        }
        // push ppp profile>>

        return 1;
    }
}
