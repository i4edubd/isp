<?php

namespace App\Http\Controllers;

use App\Models\vpn_pool;
use Illuminate\Http\Request;
use Net_IPv4;

class VpnPoolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize(vpn_pool::class);

        $pools = vpn_pool::all();

        return view('admins.developer.vpn-pools', [
            'pools' => $pools,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize(vpn_pool::class);

        return view('admins.developer.vpn-pool-create');
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
            'type' => 'required',
            'subnet' => 'required',
        ]);

        $subnet = $request->subnet;

        //Separating network address and bitmask
        $input_subnet = explode('/', $subnet);
        if (count($input_subnet) !== 2) {
            return redirect()->route('vpn-pools.create')->with('error', 'Invalid Network');
        }
        $input_network = $input_subnet['0'];
        $input_bitmask = $input_subnet['1'];

        $ipv4lib = new Net_IPv4();

        //checking Invalid Network
        $valid_network = $ipv4lib->validateIP($input_network);
        if ($valid_network == false) {
            return redirect()->route('vpn-pools.create')->with('error', 'Invalid Network');
        }

        //checking Invalid bitmask
        if ($input_bitmask > 30) {
            return redirect()->route('vpn-pools.create')->with('error', 'Invalid bitmask');
        }

        $net = $ipv4lib->parseAddress($subnet);
        $network = $net->network;
        $broadcast = $net->broadcast;

        //checking Invalid Subnet
        if ($network !== $input_network) {
            return redirect()->route('vpn-pools.create')->with('error', 'Invalid Subnet');
        }

        //save vpn_pool
        $vpn_pool = new vpn_pool();
        $vpn_pool->type = $request->type;
        $vpn_pool->subnet = $ipv4lib->ip2double($network);
        $vpn_pool->mask = $input_bitmask;
        $vpn_pool->gateway = $ipv4lib->ip2double($network) + 1;
        $vpn_pool->broadcast = $ipv4lib->ip2double($broadcast);
        $vpn_pool->save();

        return redirect()->route('vpn-pools.index')->with('success', 'VPN Pool Added Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\vpn_pool  $vpn_pool
     * @return \Illuminate\Http\Response
     */
    public function destroy(vpn_pool $vpn_pool)
    {
        $vpn_pool->delete();
        return redirect()->route('vpn-pools.index')->with('success', 'Pool Deleted Successfully!');
    }
}
