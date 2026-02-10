<?php

namespace App\Http\Controllers;

use App\Jobs\ReAllocateIPv4;
use App\Models\ipv4pool;
use App\Models\ipv4address;
use App\Models\operator;
use Illuminate\Http\Request;
use Net_IPv4;


class Ipv4poolSubnetController extends Controller
{

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ipv4pool  $ipv4pool
     * @return \Illuminate\Http\Response
     */
    public function edit(ipv4pool $ipv4pool)
    {
        return view('admins.group_admin.ipv4pool-subnet-edit', [
            'ipv4pool' => $ipv4pool,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ipv4pool  $ipv4pool
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ipv4pool $ipv4pool)
    {
        // validate
        $request->validate([
            'subnet' => 'required',
        ]);

        $subnet = $request->subnet;

        $input_subnet = explode('/', $subnet);

        if (count($input_subnet) !== 2) {
            return redirect()->route('ipv4pools.index')->with('error', 'Invalid Network');
        }

        $input_network = $input_subnet['0'];

        $input_bitmask = $input_subnet['1'];

        $ipv4lib = new Net_IPv4();

        //checking Invalid Network
        $valid_network = $ipv4lib->validateIP($input_network);
        if ($valid_network == false) {
            return redirect()->route('ipv4pools.index')->with('error', 'Invalid Network');
        }

        //checking Invalid bitmask
        if ($input_bitmask > 30) {
            return redirect()->route('ipv4pools.index')->with('error', 'Invalid bitmask');
        }

        $net = $ipv4lib->parseAddress($subnet);
        $network = $net->network;
        $broadcast = $net->broadcast;

        //checking Invalid Subnet
        if ($network !== $input_network) {
            return redirect()->route('ipv4pools.index')->with('error', 'Invalid Subnet');
        }

        //checking overlapped
        $overlapped = $this->isOverlapped($request->user(), $network, $broadcast);

        //Do not count overlapped with the replacing network
        if ($overlapped == long2ip($ipv4pool->subnet) . '/' . $ipv4pool->mask) {
            $overlapped = 0;
        }

        if ($overlapped) {
            return redirect()->route('ipv4pools.index')->with('error', 'Subnet overlapped with: ' . $overlapped);
        }

        //calculate capacity
        $capacity_used = $ipv4pool->ipv4address->count() - 2;

        $new_capacity = $ipv4lib->ip2double($broadcast) - ($ipv4lib->ip2double($network) + 1);

        if ($new_capacity < $capacity_used) {
            return redirect()->route('ipv4pools.index')->with('error', 'Not Enough Capacity to replace');
        }

        //delete ipv4 addresses
        $delete_where = [
            ['ipv4pool_id', '=', $ipv4pool->id],
        ];
        ipv4address::where($delete_where)->delete();

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

        //save ipv4pool
        $ipv4pool->subnet = $ipv4lib->ip2double($network);
        $ipv4pool->mask = $input_bitmask;
        $ipv4pool->gateway = $ipv4lib->ip2double($network) + 1;
        $ipv4pool->broadcast = $ipv4lib->ip2double($broadcast);
        $ipv4pool->save();

        //reallocate ipv4 addressses
        ReAllocateIPv4::dispatch($ipv4pool)
            ->onConnection('database')
            ->onQueue('re_allocate_ipv4');

        return redirect()->route('ipv4pools.index')->with('success', 'IPv4 Pool subnet updated Successfully');
    }


    /**
     * Check IPv4 Subnet Overlapping.
     *
     * @param  \App\Models\operator  $operator
     * @param  string  $network
     * @param  string  $broadcast
     * @return  string subnet || bool 0
     */
    public function isOverlapped(operator $operator, string $network, string $broadcast)
    {

        $ipv4lib = new Net_IPv4();
        $pools = ipv4pool::where('mgid', $operator->mgid)->get();
        foreach ($pools as $pool) {
            $subnet = long2ip($pool->subnet) . '/' . $pool->mask;
            if ($ipv4lib->ipInNetwork($network, $subnet) || $ipv4lib->ipInNetwork($broadcast, $subnet)) {
                return $subnet;
            }
        }
        return 0;
    }



    public function checkError(Request $request, ipv4pool $ipv4pool)
    {

        $request->validate([
            'subnet' => 'required',
        ]);

        $subnet = $request->subnet;

        $input_subnet = explode('/', $subnet);

        if (count($input_subnet) !== 2) {
            return '<span class="text-danger">Invalid Network</span>';
        }

        $input_network = $input_subnet['0'];

        $input_bitmask = $input_subnet['1'];

        $ipv4lib = new Net_IPv4();

        //checking Invalid Network
        $valid_network = $ipv4lib->validateIP($input_network);
        if ($valid_network == false) {
            return '<span class="text-danger">Invalid Network</span>';
        }

        //checking Invalid bitmask
        if ($input_bitmask > 30) {
            return '<span class="text-danger">Invalid bitmask</span>';
        }

        $net = $ipv4lib->parseAddress($subnet);
        $network = $net->network;
        $broadcast = $net->broadcast;

        //checking Invalid Subnet
        if ($network !== $input_network) {
            return '<span class="text-danger">Invalid Subnet</span>';
        }

        //checking overlapped
        $overlapped = $this->isOverlapped($request->user(), $network, $broadcast);

        //Do not count overlapped with the replacing network
        if ($overlapped == long2ip($ipv4pool->subnet) . '/' . $ipv4pool->mask) {
            $overlapped = 0;
        }

        if ($overlapped) {
            return '<span class="text-danger">Subnet overlapped with: </span>' . $overlapped;
        }

        //calculate capacity
        $capacity_used = $ipv4pool->used_space;

        $new_capacity = $ipv4lib->ip2double($broadcast) - ($ipv4lib->ip2double($network) + 1);

        if ($new_capacity < $capacity_used) {
            return '<span class="text-danger">Not Enough Capacity to replace</span>';
        }

        return view('laraview.layouts.duplicate-check-response', [
            'duplicate' => 0,
        ]);
    }
}
