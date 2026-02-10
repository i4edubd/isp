<?php

namespace App\Http\Controllers\Freeradius;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use RouterOS\Sohag\RouterosAPI;
use Carbon\Carbon;
use App\Models\Freeradius\nas;

class RadreplyController extends Controller
{
    protected $api;

    public function __construct()
    {
        $this->initializeConnection();
    }

    private function initializeConnection()
    {
        $router = nas::where('mgid', '>', 0)->first(); // Modify query as needed
        if ($router && strlen($router->api_username) && strlen($router->api_password)) {
            $config  = [
                'host' => $router->nasname,
                'user' => $router->api_username,
                'pass' => $router->api_password,
                'port' => $router->api_port,
                'attempts' => 1,
                'debug' => 0, // Set to 1 if you need debugging
            ];

            $api = new RouterosAPI($config);

            if (!$api->connect($config['host'], $config['user'], $config['pass'])) {
                $router->api_status = 'Failed';
                $router->api_last_check = Carbon::now();
                $router->identity_status = 'incorrect';
                $router->save();
                throw new \Exception('Failed to connect to RouterOS API');
            } else {
                $router->api_status = 'OK';
                $router->api_last_check = Carbon::now();
                $this->api = $api;
            }
        } else {
            throw new \Exception('Invalid router configuration');
        }
    }

    public function viewInterfaces()
    {
        $this->api->write('/interface/print');
        $interfaces = $this->api->read();

        return view('mikrotik.interfaces', ['interfaces' => $interfaces]);
    }

    public function addIp(Request $request)
    {
        $request->validate([
            'address' => 'required|ip',
            'interface' => 'required|string',
        ]);

        $this->api->write('/ip/address/add', false);
        $this->api->write('=address=' . $request->address, false);
        $this->api->write('=interface=' . $request->interface);
        $this->api->read();

        return redirect()->back()->with('success', 'IP address added successfully.');
    }

    public function editIp(Request $request, $id)
    {
        $request->validate([
            'address' => 'required|ip',
            'interface' => 'required|string',
        ]);

        $this->api->write('/ip/address/set', false);
        $this->api->write('=.id=' . $id, false);
        $this->api->write('=address=' . $request->address, false);
        $this->api->write('=interface=' . $request->interface);
        $this->api->read();

        return redirect()->back()->with('success', 'IP address updated successfully.');
    }

    public function deleteIp($id)
    {
        $this->api->write('/ip/address/remove', false);
        $this->api->write('=.id=' . $id);
        $this->api->read();

        return redirect()->back()->with('success', 'IP address deleted successfully.');
    }

    public function addVlan(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'vlan-id' => 'required|numeric',
            'interface' => 'required|string',
        ]);

        $this->api->write('/interface/vlan/add', false);
        $this->api->write('=name=' . $request->name, false);
        $this->api->write('=vlan-id=' . $request->input('vlan-id'), false);
        $this->api->write('=interface=' . $request->interface);
        $this->api->read();

        return redirect()->back()->with('success', 'VLAN added successfully.');
    }

    public function viewVlans()
    {
        $this->api->write('/interface/vlan/print');
        $vlans = $this->api->read();

        return view('mikrotik.vlans', ['vlans' => $vlans]);
    }

    public function deleteVlan($id)
    {
        $this->api->write('/interface/vlan/remove', false);
        $this->api->write('=.id=' . $id);
        $this->api->read();

        return redirect()->back()->with('success', 'VLAN deleted successfully.');
    }

    public function viewArp()
    {
        $this->api->write('/ip/arp/print');
        $arps = $this->api->read();

        return view('mikrotik.arp', ['arps' => $arps]);
    }

    public function exportConfig()
    {
        $this->api->write('/export');
        $config = $this->api->read();

        return response()->json($config);
    }

    public function viewBridges()
    {
        $this->api->write('/interface/bridge/print');
        $bridges = $this->api->read();

        return view('mikrotik.bridges', ['bridges' => $bridges]);
    }

    public function addBridge(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $this->api->write('/interface/bridge/add', false);
        $this->api->write('=name=' . $request->name);
        $this->api->read();

        return redirect()->back()->with('success', 'Bridge added successfully.');
    }

    public function viewHosts()
   {
    $this->api->write('/ip/hotspot/host/print');
    $hosts = $this->api->read();

    return view('admins.components.hosts', ['hosts' => $hosts]);
   }

    public function viewTraffic()
 {
    $this->api->write('/interface/monitor-traffic', false);
    $this->api->write('=interface=all', false);
    $this->api->write('=once=');
    $traffic = $this->api->read();

    // Debugging: log or inspect the traffic array to understand its structure
    // Log::debug($traffic);

    return view('mikrotik.traffic', ['traffic' => $traffic]);
 }
}
