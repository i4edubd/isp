<?php

namespace App\Http\Controllers;


use App\Models\ipv4address;
use App\Models\ipv4pool;
use App\Models\operator;
use Illuminate\Http\Request;
use Net_IPv4;

class Ipv4poolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $madmin = $request->user();

        $pools = ipv4pool::with('pppoe_profiles')->where('mgid', $request->user()->id)->get();

        if ($request->filled('unused')) {
            $pools = $pools->filter(function ($pool) use ($madmin) {
                return $madmin->can('delete', $pool);
            });
        }

        return view('admins.group_admin.ipv4pools', [
            'pools' => $pools,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return view('admins.group_admin.ipv4pools-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'subnet' => 'required',
        ]);

        $name = getVarName($request->name);

        $where = [
            ['mgid', '=', $request->user()->id],
            ['name', '=', $name],
        ];

        if (ipv4pool::where($where)->count()) {
            return redirect()->route('ipv4pools.create')->with('error', 'Duplicate IPv4pool Name');
        }

        $subnet = $request->subnet;

        //Separating network address and bitmask
        $input_subnet = explode('/', $subnet);
        if (count($input_subnet) !== 2) {
            return redirect()->route('ipv4pools.create')->with('error', 'Invalid Network');
        }
        $input_network = $input_subnet['0'];
        $input_bitmask = $input_subnet['1'];

        $ipv4lib = new Net_IPv4();

        //checking Invalid Network
        $valid_network = $ipv4lib->validateIP($input_network);
        if ($valid_network == false) {
            return redirect()->route('ipv4pools.create')->with('error', 'Invalid Network');
        }

        //checking Invalid bitmask
        if ($input_bitmask > 30) {
            return redirect()->route('ipv4pools.create')->with('error', 'Invalid bitmask');
        }

        $net = $ipv4lib->parseAddress($subnet);
        $network = $net->network;
        $broadcast = $net->broadcast;

        //checking Invalid Subnet
        if ($network !== $input_network) {
            return redirect()->route('ipv4pools.create')->with('error', 'Invalid Subnet');
        }

        //checking overlapped
        $overlapped = self::isOverlapped($request->user(), $network, $broadcast);

        if ($overlapped) {
            return redirect()->route('ipv4pools.create')->with('error', 'Subnet overlapped with: ' . $overlapped);
        }

        //save ipv4pool
        $ipv4pool = new ipv4pool();
        $ipv4pool->mgid = $request->user()->id;
        $ipv4pool->name = $name;
        $ipv4pool->subnet = $ipv4lib->ip2double($network);
        $ipv4pool->mask = $input_bitmask;
        $ipv4pool->gateway = $ipv4lib->ip2double($network) + 1;
        $ipv4pool->broadcast = $ipv4lib->ip2double($broadcast);
        $ipv4pool->save();

        //save network address
        $ipv4address = new ipv4address();
        $ipv4address->customer_id = 0;
        $ipv4address->operator_id = $request->user()->id;
        $ipv4address->ipv4pool_id = $ipv4pool->id;
        $ipv4address->ip_address = $ipv4lib->ip2double($network);
        $ipv4address->description = 'Network Address';
        $ipv4address->save();

        //save gateway address
        $ipv4address = new ipv4address();
        $ipv4address->customer_id = 0;
        $ipv4address->operator_id = $request->user()->id;
        $ipv4address->ipv4pool_id = $ipv4pool->id;
        $ipv4address->ip_address = $ipv4lib->ip2double($network) + 1;
        $ipv4address->is_gateway = 1;
        $ipv4address->description = 'Gateway Address';
        $ipv4address->save();

        //save broadcast address
        $ipv4address = new ipv4address();
        $ipv4address->customer_id = 0;
        $ipv4address->operator_id = $request->user()->id;
        $ipv4address->ipv4pool_id = $ipv4pool->id;
        $ipv4address->ip_address = $ipv4lib->ip2double($broadcast);
        $ipv4address->description = 'Broadcast Address';
        $ipv4address->save();

        return redirect()->route('ipv4pools.index')->with('success', 'IPv4 Pool Added Successfully');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ipv4pool  $ipv4pool
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, ipv4pool $ipv4pool)
    {

        $this->authorize('delete', $ipv4pool);

        //delete network, gateway and broadcast address
        $delete_where = [
            ['customer_id', '=', 0],
            ['ipv4pool_id', '=', $ipv4pool->id],
            ['operator_id', '=', $request->user()->id],
        ];

        ipv4address::where($delete_where)->delete();

        $ipv4pool->delete();

        return redirect(url()->previous())->with('success', 'The IPv4 Pool has been deleted successfully!');
    }


    /**
     * Check duplicate IPv4Poll Name.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $name
     * @return \Illuminate\View\View
     */
    public function checkDuplicateName(Request $request, string $name)
    {
        $name = getVarName($name);

        $where = [
            ['mgid', '=', $request->user()->id],
            ['name', '=', $name],
        ];

        if (ipv4pool::where($where)->count()) {
            $duplicate = 1;
        } else {
            $duplicate = 0;
        }

        return view('laraview.layouts.duplicate-check-response', [
            'duplicate' => $duplicate,
        ]);
    }


    /**
     * Check IPv4 Subnet Error.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $name
     * @return \Illuminate\View\View
     */
    public function checkSubnetError(Request $request)
    {
        $subnet = $request->subnet;

        $input_subnet = explode('/', $subnet);

        if (count($input_subnet) !== 2) {
            return '<span class="text-danger">Invalid Network</span>';
        }

        $input_network = $input_subnet['0'];

        $input_bitmask = $input_subnet['1'];

        $ipv4lib = new Net_IPv4();

        $valid_network = $ipv4lib->validateIP($input_network);
        if ($valid_network == false) {
            return '<span class="text-danger">Invalid Network</span>';
        }


        if ($input_bitmask > 30) {
            return '<span class="text-danger">Invalid bitmask</span>';
        }

        $net = $ipv4lib->parseAddress($subnet);
        $network = $net->network;
        $broadcast = $net->broadcast;

        if ($network !== $input_network) {
            return '<span class="text-danger">Invalid Subnet</span> <br> <span class="text-success">Suggested Subnet: ' . $network . '/' . $input_bitmask . '</span>';
        }

        $overlapped = self::isOverlapped($request->user(), $network, $broadcast);

        if ($overlapped) {
            return '<span class="text-danger">Subnet overlapped with: </span>' . $overlapped;
        }

        return view('laraview.layouts.duplicate-check-response', [
            'duplicate' => 0,
        ]);
    }


    /**
     * Check IPv4 Subnet Overlapping.
     *
     * @param  \App\Models\operator  $operator
     * @param  string  $network
     * @param  string  $broadcast
     * @return  string subnet || bool 0
     */
    public static function isOverlapped(operator $operator, string $network, string $broadcast)
    {
        $ipv4lib = new Net_IPv4();
        $pools = ipv4pool::where('mgid', $operator->id)->get();
        foreach ($pools as $pool) {
            $subnet = long2ip($pool->subnet) . '/' . $pool->mask;
            if ($ipv4lib->ipInNetwork($network, $subnet) || $ipv4lib->ipInNetwork($broadcast, $subnet)) {
                return $subnet;
            }
        }
        return 0;
    }


    /**
     * Count IPv4 Pool used space
     *
     * @param  \App\Models\ipv4pool  $ipv4pool
     * @return  int
     */
    public static function usedSpace(ipv4pool $ipv4pool)
    {
        $total_customer = 0;

        $where = [
            ['ipv4pool_id', '=', $ipv4pool->id],
            ['customer_id', '!=', 0],
        ];

        $total_customer = ipv4address::where($where)->count();

        return $total_customer;
    }
}
