<?php

namespace App\Http\Controllers;

use App\Models\Freeradius\nas;
use App\Models\operator;
use App\Models\vpn_pool;
use RouterOS\Sohag\RouterosAPI;
use Illuminate\Http\Request;

class RouterConfigurationController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\Freeradius\nas  $router
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, nas $router)
    {
        $operators = operator::where('mgid', $request->user()->id)
            ->whereIn('role', ['group_admin', 'operator', 'sub_operator', 'developer'])
            ->get()->sortBy('role');

        $requester = $request->user();

        switch ($requester->role) {
            case 'group_admin':
                return view('admins.group_admin.router-configuration-create', [
                    'router' => $router,
                    'operators' => $operators,
                ]);
                break;

            case 'developer':
                return view('admins.developer.router-configuration-create', [
                    'router' => $router,
                    'operators' => $operators,
                ]);
                break;
        }
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
        //configure for
        $operator = operator::findOrFail($request->operator_id);

        //radius server
        $radius_server = config('database.connections.' . $request->user()->radius_db_connection . '.public_ip');

        if ($request->user()->role === 'developer') {
            $radius_server = config('database.connections.central.public_ip');
        }

        //central server
        $central_server = config('database.connections.central.public_ip');

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

        //Add radius information #hotspot,ppp
        $menu = 'radius';
        $rows = [
            [
                'accounting-port' => 3613,
                'address' => $radius_server,
                'authentication-port' => 3612,
                'secret' => $router['secret'],
                'service' => 'hotspot,ppp',
                'timeout' => '3s',
                'require-message-auth' => 'no',
            ]
        ];
        $router_rows = $api->getMktRows($menu);
        $api->removeMktRows($menu, $router_rows);
        $api->addMktRows($menu, $rows);

        //system identity #Hotspot
        if ($request->filled('change_system_identity')) {
            NasIdentifierController::setIdentifier($operator, $router);
        }

        //Add Nat Rule #Hotspot
        $menu = 'ip_firewall_nat';
        $rows = [
            [
                'chain' => 'pre-hotspot',
                'dst-address-type' => '!local',
                'hotspot' => 'auth',
                'action' => 'accept',
                'comment' => 'bypassed auth',
            ]
        ];
        $api->addMktRows($menu, $rows);

        //walled-garden ip #Hotspot
        $walled_garden_ips = [
            ['action' => 'accept', 'dst-address' => $central_server, 'comment' => "Radius Server"],
        ];
        $api->addMktRows('walled_garden_ip', $walled_garden_ips);

        // Hotspot Servers
        /* Note:
            In the HotSpot server settings idle-timeout is the inactivity period for unauthorised customers.
            When idle-timeout is configured, keepalive-timeout or login-timeout is redundant and no need to configure.
        */
        $hs_servers_extra_settings = [
            'idle-timeout' => '5m',
            'keepalive-timeout' => 'none',
            'login-timeout' => 'none',
        ];
        $hs_servers = $api->getMktRows('ip_hotspot');
        while ($hs_server = array_shift($hs_servers)) {
            $api->editMktRow('ip_hotspot', $hs_server, $hs_servers_extra_settings);
        }

        //Set IP Hotspot Profile #Hotspot
        $hotspot_profile = [
            'login-by' => 'mac,cookie,http-chap,http-pap,mac-cookie',
            'mac-auth-mode' => 'mac-as-username-and-password',
            'http-cookie-lifetime' => '6h',
            'split-user-domain' => 'no',
            'use-radius' => 'yes',
            'radius-accounting' => 'yes',
            'radius-interim-update' => '5m',
            'nas-port-type' => 'wireless-802.11',
            'radius-mac-format' => 'XX:XX:XX:XX:XX:XX',
        ];
        $profiles = $api->getMktRows('ip_hotspot_profile');
        while ($profile = array_shift($profiles)) {
            $api->editMktRow('ip_hotspot_profile', $profile, $hotspot_profile);
        }

        // hotspot_user_profile <<priority GGC, FB, BDIX queues>>
        /* Note:
         mac cookie is removed in following cases:
            user-request - user clicked on logout button.
            admin-reset - disconnected from radius server or user is removed from hotspot active menu
            nas-request - traffic limit reached
            session-timeout
        so using login-by=mac-cookie, add-mac-cookie=yes is not a problem and default setting is fine.
        In the user profile settings idle-timeout is the maximum period of inactivity for authorized HotSpot clients.
        We do not want to remove authorized HotSpot customers when they are alive.
        But Remove HotSpot customers when they are not alive. If not, there will be several entries in the/IP/hotspot/host table (new DHCP lease once on-line) and clients will not get internet.
        */
        $user_profiles_extra_settings = [
            'idle-timeout' => 'none',
            'keepalive-timeout' => '2m',
            'queue-type' => 'hotspot-default',
            'on-login' => ':foreach n in=[/queue simple find comment=priority_1] do={ /queue simple move $n [:pick [/queue simple find] 0] }',
            'on-logout' => '/ip hotspot host remove [find where address=$address and !authorized and !bypassed]',
        ];
        $hotspot_user_profiles = $api->getMktRows('hotspot_user_profile');
        while ($user_profile = array_shift($hotspot_user_profiles)) {
            $api->editMktRow('hotspot_user_profile', $user_profile, $user_profiles_extra_settings);
        }

        // Set lease-script #Hotspot (replaced by hotspot mac & mac-cookie login)
        /*
        $lease_script = [
            'lease-script' => ':delay 500ms; /ip hotspot active login mac-address=$leaseActMAC ip=$leaseActIP user=$leaseActMAC password=$leaseActMAC',
        ];
        $dhcp_servers = $api->getMktRows('ip_dhcp_server');
        while ($dhcp_server = array_shift($dhcp_servers)) {
            $api->editMktRow('ip_dhcp_server', $dhcp_server, $lease_script);
        }
        */

        //Set default-profile to default #ppp
        $pppoe_server_profile = [
            'authentication' => 'pap,chap',
            'one-session-per-host' => 'yes',
            'default-profile' => 'default',
        ];

        $pppoe_servers = $api->getMktRows('pppoe_server_server');
        while ($pppoe_server = array_shift($pppoe_servers)) {
            $api->editMktRow('pppoe_server_server', $pppoe_server, $pppoe_server_profile);
        }

        //Set default profile local-address #ppp
        if ($request->user()->role === 'developer') {
            $vpn_pool = vpn_pool::where('type', 'client')->firstOr(function () {
                abort(500, 'VPN Pool Not Found');
            });

            $la = long2ip($vpn_pool->gateway);
        } else {
            $la = '10.0.0.1';
        }
        $local_address = [
            'local-address' => $la,
        ];

        $ppp_profiles = $api->getMktRows('ppp_profile', ['default' => 'yes']);
        while ($ppp_profile = array_shift($ppp_profiles)) {
            $api->editMktRow('ppp_profile', $ppp_profile, $local_address);
        }

        //Enable radius use #ppp
        $api->ttyWirte('/ppp/aaa/set', ['interim-update' => '5m', 'use-radius' => 'yes', 'accounting' => 'yes']);

        // Set the on-up script for /ppp profile
        $on_up_script = ':local sessions [/ppp active print count-only where name=$user];'.
                ':if ( $sessions > 1) do={ '.
                ':log info ("disconnecting " . $user  ." duplicate" ); '.
                '/ppp active remove [find where (name=$user && uptime<00:00:30 )]; '.
                '}';

        $ppp_profiles_extra_settings = [
            'on-up' => $on_up_script,
        ];

        $ppp_profiles = $api->getMktRows('ppp_profile');
        while ($ppp_profile = array_shift($ppp_profiles)) {
            $api->editMktRow('ppp_profile', $ppp_profile, $ppp_profiles_extra_settings);
        }
                
        // suspended_users_pool #ppp
        $controller = new SuspendedUsersPoolController();
        $pool = $controller->get($request->user());
        $rows = [
            [
                "name" => $pool->name,
                "ranges" => long2ip($pool->subnet) . '/' . $pool->mask,
            ]
        ];
        $api->addMktRows('ip_pool', $rows);

        //Enable radius Incoming
        $api->ttyWirte('/radius/incoming/set', ['accept' => 'yes']);

        //Enable SNMP
        $api->ttyWirte('/snmp/set', ['enabled' => 'yes']);
        $api->ttyWirte('/snmp/community/add', ['name' => 'billing']);
        $snmp_rules = [
            [
                'chain' => 'input',
                'protocol' => 'udp',
                'dst-port' => '161',
                'src-address' => $radius_server,
                'action' => 'accept',
                'comment' => 'snmp allowed for radius',
            ],
        ];
        // $api->addMktRows('ip_firewall_filter', $snmp_rules);

        //Forward Suspended pool
        $forward_rules = [
            [
                'chain' => 'forward',
                'src-address' => '100.65.96.0/20',
                'action' => 'drop',
                'comment' => 'drop suspended pool',
            ],
        ];
         $api->addMktRows('ip_firewall_filter', $forward_rules);

        //Drop Suspended pool
        $drop_rules = [
            [
                'chain' => 'input',
                'src-address' => '100.65.96.0/20',
                'action' => 'drop',
                'comment' => 'drop suspended pool',
            ],
        ];
         $api->addMktRows('ip_firewall_filter', $drop_rules);

        if (MinimumConfigurationController::hasPendingConfig($request->user())) {
            return redirect()->route('configuration.next', ['operator' => $request->user()->id]);
        } else {
            return redirect()->route('routers.index')->with('success', 'Router has been configured successfully!');
        }
    }
}
