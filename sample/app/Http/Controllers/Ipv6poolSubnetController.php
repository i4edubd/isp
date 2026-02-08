<?php

namespace App\Http\Controllers;

use App\Models\ipv6pool;
use Illuminate\Http\Request;
use Net_IPv6;

class Ipv6poolSubnetController extends Controller
{

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ipv6pool  $ipv6pool
     * @return \Illuminate\Http\Response
     */
    public function edit(ipv6pool $ipv6pool)
    {
        return view('admins.group_admin.ipv6pools-prefix-edit', [
            'ipv6pool' => $ipv6pool,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ipv6pool  $ipv6pool
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ipv6pool $ipv6pool)
    {

        $request->validate([
            'prefix' => 'required',
        ]);

        if ($request->prefix == $ipv6pool->prefix) {
            return redirect()->route('ipv6pools.index');
        }

        $prefix = $request->prefix;

        $ipv6lib = new Net_IPv6();

        //checkIPv6
        $validIPv6 = $ipv6lib->checkIPv6($prefix);

        if ($validIPv6 == false) {
            return redirect()->route('ipv6pools.index')->with('error', 'Invalid Prefix');
        }

        //check prefix length
        $prefix_length = $ipv6lib->getNetmaskSpec($prefix);

        if ($prefix_length > 48 || $prefix_length < 32) {
            return redirect()->route('ipv6pools.index')->with('error', 'Please use prefix length/netmask between 32 and 48');
        }

        //check dumplicate
        $address = $ipv6lib->parseAddress($prefix);

        $lowest_address = $address['start'];

        $highest_address = $address['end'];

        $overlapped = Ipv6poolController::isOverlapped($request->user(), $lowest_address, $highest_address);

        if ($overlapped == $ipv6pool->prefix) {
            $overlapped = 0;
        }

        if ($overlapped) {
            return redirect()->route('ipv6pools.index')->with('error', 'Prefix overlapped with: ' . $overlapped);
        }

        //save IPv6Pool
        $ipv6pool->prefix = $ipv6lib->compress($ipv6lib->getNetmask($prefix) . '/' . $ipv6lib->getNetmaskSpec($prefix));
        $ipv6pool->lowest_address = $lowest_address;
        $ipv6pool->highest_address = $highest_address;
        $ipv6pool->save();

        return redirect()->route('ipv6pools.index')->with('success', 'IPv6 Pool Prefix updated successfully');
    }

    /**
     * Check Prefix Error
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ipv6pool  $ipv6pool
     * @return \Illuminate\Http\Response
     */
    public function checkError(Request $request, ipv6pool $ipv6pool)
    {
        $request->validate([
            'prefix' => 'required',
        ]);

        $prefix = $request->prefix;

        $ipv6lib = new Net_IPv6();

        //checkIPv6
        $validIPv6 = $ipv6lib->checkIPv6($prefix);

        if ($validIPv6 == false) {
            return '<span class="text-danger">Invalid Prefix</span>';
        }

        //check prefix length
        $prefix_length = $ipv6lib->getNetmaskSpec($prefix);

        if ($prefix_length > 48 || $prefix_length < 32) {
            return '<span class="text-danger">Please use prefix length/netmask between 32 and 48</span>';
        }

        //check dumplicate
        $address = $ipv6lib->parseAddress($prefix);

        $lowest_address = $address['start'];

        $highest_address = $address['end'];

        $overlapped = Ipv6poolController::isOverlapped($request->user(), $lowest_address, $highest_address);

        if ($overlapped == $ipv6pool->prefix) {
            $overlapped = 0;
        }

        if ($overlapped) {
            return '<span class="text-danger">Subnet overlapped with: </span>' . $overlapped;
        }

        return view('laraview.layouts.duplicate-check-response', [
            'duplicate' => 0,
        ]);
    }
}
