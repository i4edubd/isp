<?php

namespace App\Http\Controllers;

use App\Models\Freeradius\nas;
use App\Models\operator;
use App\Models\pgsql\pgsql_customer;
use App\Models\pgsql\pgsql_radcheck;
use App\Models\pgsql\pgsql_radreply;
use App\Models\vpn_account;
use App\Models\vpn_pool;
use Carbon\Carbon;
use Illuminate\Http\Request;
use RouterOS\Sohag\RouterosAPI;

class VpnAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $this->authorize(vpn_account::class);

        $requester = $request->user();

        if ($requester->role == 'developer') {
            $vpn_accounts = vpn_account::with('operator')->get();
        } else {
            $vpn_accounts = vpn_account::with('operator')->where('mgid', $requester->id)->get();
        }

        switch ($requester->role) {
            case 'group_admin':
                return view('admins.group_admin.vpn_accounts', [
                    'vpn_accounts' => $vpn_accounts,
                ]);
                break;

            case 'developer':
                return view('admins.developer.vpn_accounts', [
                    'vpn_accounts' => $vpn_accounts,
                ]);
                break;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize(vpn_account::class);

        return view('admins.group_admin.vpn_account-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $requester = $request->user();

        $vpn_pool = vpn_pool::where('type', 'client')->firstOr(function () {
            abort(500, 'VPN Pool Not Found');
        });

        $developer = operator::where('role', 'developer')->firstOr(function () {
            abort(500, 'Developer Account Not Found!');
        });

        $model = new nas();
        $model->setConnection('central');
        $vpn_server = $model->where('mgid', $developer->id)->firstOr(function () {
            abort(500, 'VPN SERVER NOT FOUND!');
        });

        $vpn_account = new vpn_account();
        $vpn_account->mgid = $requester->id;
        $vpn_account->vpn_pool_id = $vpn_pool->id;
        $vpn_account->username = $requester->id . Carbon::now()->timestamp . bin2hex(random_bytes(4));
        $vpn_account->password = bin2hex(random_bytes(8));
        $vpn_account->ip_address = self::getFirstFreeIP();
        $vpn_account->server_ip = $vpn_server->nasname;
        $vpn_account->vpn_type = 'PPTP';
        $vpn_account->winbox_port = self::getFirstFreePort();
        $vpn_account->save();

        self::updateOrCreateRadAttributes($vpn_account);
        self::createWinboxPort($vpn_account);

        return redirect()->route('vpn_accounts.show', ['vpn_account' => $vpn_account->id])->with('success', 'VPN Account Created Successfully!, Please Configure VPN Account');

        return redirect()->route('vpn_accounts.index')->with('success', 'VPN Account Created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\vpn_account  $vpn_account
     * @return \Illuminate\Http\Response
     */
    public function show(vpn_account $vpn_account)
    {
        $this->authorize('view', $vpn_account);

        $developer = operator::where('role', 'developer')->firstOr(function () {
            abort(500, 'Developer Account Not Found!');
        });

        $model = new nas();
        $model->setConnection('central');
        $vpn_server = $model->where('mgid', $developer->id)->firstOr(function () {
            abort(500, 'VPN SERVER NOT FOUND!');
        });

        $server_pool = vpn_pool::where('type', 'server')->firstOr(function () {
            abort(500, 'Server Pool Not Found!');
        });

        $vpn_pool = vpn_pool::where('type', 'client')->firstOr(function () {
            abort(500, 'VPN Pool Not Found');
        });

        return view('admins.components.vpn_account-show', [
            'vpn_server' => $vpn_server,
            'vpn_account' => $vpn_account,
            'server_pool' => $server_pool,
            'vpn_pool' => $vpn_pool,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\vpn_account  $vpn_account
     * @return \Illuminate\Http\Response
     */
    public function destroy(vpn_account $vpn_account)
    {
        $this->authorize('delete', $vpn_account);

        self::destroyRadAttributes($vpn_account);

        self::destroyWinboxPort($vpn_account);

        $vpn_account->delete();

        return redirect()->route('vpn_accounts.index')->with('success', 'VPN Account Deleted Successfully!');
    }

    /**
     * Get First Free IP Address.
     *
     */
    public static function getFirstFreeIP()
    {

        $vpn_pool = vpn_pool::where('type', 'client')->firstOrFail();

        $first_free = $vpn_pool->gateway + 1;

        $vpn_accounts = vpn_account::all();

        for ($i = $first_free; $i < $vpn_pool->broadcast; $i++) {
            if ($vpn_accounts->where('ip_address', $i)->count()) {
                continue;
            } else {
                return $i;
            }
        }
    }


    /**
     * Get First Free Port Number.
     *
     */
    public static function getFirstFreePort()
    {
        $first_free = 5001;

        $vpn_accounts = vpn_account::all();

        for ($i = $first_free; $i < 5500; $i++) {
            if ($vpn_accounts->where('winbox_port', $i)->count()) {
                continue;
            } else {
                return $i;
            }
        }
    }


    /**
     * Write the specified resource.
     *
     * @param  \App\Models\vpn_account  $vpn_account
     * @return int
     */
    public static function updateOrCreateRadAttributes(vpn_account $vpn_account)
    {
        // pgsql_customer
        $model = new pgsql_customer();
        $model->setConnection('centralpgsql');
        if ($model->where('mgid', $vpn_account->mgid)->where('username', $vpn_account->username)->count() == 0) {
            $pgsql_customer = new pgsql_customer();
            $pgsql_customer->setConnection('centralpgsql');
            $pgsql_customer->mgid = $vpn_account->mgid;
            $pgsql_customer->operator_id = $vpn_account->mgid;
            $pgsql_customer->customer_id = $vpn_account->mgid;
            $pgsql_customer->username = $vpn_account->username;
            $pgsql_customer->save();
        }

        // radcheck
        $model = new pgsql_radcheck();
        $model->setConnection('centralpgsql');
        $model->updateOrCreate(
            [
                'mgid' => $vpn_account->mgid,
                'customer_id' => $vpn_account->mgid,
                'username' => $vpn_account->username,
                'attribute' => 'Cleartext-Password',
            ],
            [
                'value' => $vpn_account->password,
            ]
        );

        // Mikrotik-Rate-Limit
        $model = new pgsql_radreply();
        $model->setConnection('centralpgsql');
        $model->updateOrCreate(
            [
                'mgid' => $vpn_account->mgid,
                'customer_id' => $vpn_account->mgid,
                'username' => $vpn_account->username,
                'attribute' => 'Mikrotik-Rate-Limit',
            ],
            [
                'value' => '5M',
            ]
        );

        // Framed-IP-Address
        $model = new pgsql_radreply();
        $model->setConnection('centralpgsql');
        $model->updateOrCreate(
            [
                'mgid' => $vpn_account->mgid,
                'customer_id' => $vpn_account->mgid,
                'username' => $vpn_account->username,
                'attribute' => 'Framed-IP-Address',
            ],
            [
                'value' => long2ip($vpn_account->ip_address),
            ]
        );

        return 0;
    }

    /**
     * Create Firewall Rule for Winbox Port Forwarding.
     *
     * @param  \App\Models\vpn_account  $vpn_account
     * @return \Illuminate\Http\Response
     */
    public static function createWinboxPort(vpn_account $vpn_account)
    {
        $developer = operator::where('role', 'developer')->firstOr(function () {
            abort(500, 'Developer Account Not Found!');
        });

        $model = new nas();
        $model->setConnection('central');
        $vpn_server = $model->where('mgid', $developer->id)->firstOr(function () {
            abort(500, 'VPN SERVER NOT FOUND!');
        });

        $config  = [
            'host' => $vpn_server->nasname,
            'user' => $vpn_server->api_username,
            'pass' => $vpn_server->api_password,
            'port' => $vpn_server->api_port,
            'attempts' => 1
        ];

        $api = new RouterosAPI($config);

        $menu = 'ip_firewall_nat';
        $rows = [
            [
                'action' => 'dst-nat',
                'chain' => 'dstnat',
                'dst-port' => $vpn_account->winbox_port,
                'protocol' => 'tcp',
                'to-addresses' => long2ip($vpn_account->ip_address),
                'to-ports' => '8291',
                'comment' => $vpn_account->mgid,
            ]
        ];

        $api->addMktRows($menu, $rows);

        return 1;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\vpn_account  $vpn_account
     * @return \Illuminate\Http\Response
     */
    public static function destroyRadAttributes(vpn_account $vpn_account)
    {
        $model = new pgsql_customer();
        $model->setConnection('centralpgsql');
        $model->where('username', $vpn_account->username)->delete();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\vpn_account  $vpn_account
     * @return \Illuminate\Http\Response
     */
    public static function destroyWinboxPort(vpn_account $vpn_account)
    {
        $developer = operator::where('role', 'developer')->firstOr(function () {
            abort(500, 'Developer Account Not Found!');
        });

        $model = new nas();
        $model->setConnection('central');
        $vpn_server = $model->where('mgid', $developer->id)->firstOr(function () {
            abort(500, 'VPN SERVER NOT FOUND!');
        });

        $config  = [
            'host' => $vpn_server->nasname,
            'user' => $vpn_server->api_username,
            'pass' => $vpn_server->api_password,
            'port' => $vpn_server->api_port,
            'attempts' => 1
        ];

        $api = new RouterosAPI($config);
        $menu = 'ip_firewall_nat';
        $router_rows = $api->getMktRows($menu, ["comment" => $vpn_account->mgid]);
        $api->removeMktRows($menu, $router_rows);
        return 1;
    }
}
