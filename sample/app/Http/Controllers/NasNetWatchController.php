<?php

namespace App\Http\Controllers;

use App\Models\Freeradius\nas;
use Illuminate\Http\Request;
use RouterOS\Sohag\RouterosAPI;

class NasNetWatchController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\Freeradius\nas  $router
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, nas $router)
    {
        return match ($request->user()->role) {
            'group_admin' => view('admins.group_admin.router-netwatch', ['router' => $router])
        };
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Freeradius\nas  $router
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, nas $router)
    {
        //radius server
        $radius_server = config('database.connections.' . $request->user()->radius_db_connection . '.public_ip');

        //API
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
            return redirect()->route('routers.index')->with('error', 'Could not connect to the router!');
        }

        // check user group
        $system_users = $api->getMktRows('user', ['name' => $config['user']]);
        $api_user = array_shift($system_users);
        $api_user_permission = $api_user['group'];
        $required_permissions = ['full', 'write'];
        if (in_array($api_user_permission, $required_permissions) == false) {
            return redirect()->route('routers.index')->with('error', 'Not enough permission to setup your router. The API user requires full/write permission.');
        }

        $menu = 'tool_netwatch';
        $rows = [
            [
                'host' => $radius_server,
                'interval' => '1m',
                'timeout' => '1s',
                'up-script' => "/ppp secret disable [find disabled=no];/ppp active remove [find radius=no];",
                'down-script' => "/ppp secret enable [find disabled=yes];",
                'comment' => 'radius',
            ]
        ];
        $router_rows = $api->getMktRows($menu, ['host' => $radius_server]);
        $api->removeMktRows($menu, $router_rows);
        $api->addMktRows($menu, $rows);
        return redirect()->route('routers.index')->with('info', 'Radius monitoring script added successfully');
    }
}
