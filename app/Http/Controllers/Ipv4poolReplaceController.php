<?php

namespace App\Http\Controllers;

use App\Jobs\ReAllocateIPv4;
use App\Models\ipv4pool;
use Illuminate\Http\Request;


class Ipv4poolReplaceController extends Controller
{

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ipv4pool  $ipv4pool
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, ipv4pool $ipv4pool)
    {
        $pools = ipv4pool::where('mgid', $request->user()->id)->get();

        $ipv4pools = $pools->except($ipv4pool->id);

        return view('admins.group_admin.ipv4pool-replace', [
            'ipv4pool' => $ipv4pool,
            'ipv4pools' => $ipv4pools,
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
        $request->validate([
            'ipv4pool_id' => 'required'
        ]);

        $new_pool = ipv4pool::findOrFail($request->ipv4pool_id);

        //calculate capacity
        $required_space = $ipv4pool->used_space;

        $free_space = $new_pool->broadcast - $new_pool->gateway - $new_pool->used_space;

        if ($required_space > $free_space) {
            return redirect()->route('ipv4pools.index')->with('error', 'Not Enough Capacity to replace');
        }

        //get the PPPoE profiles using this IP Pool
        $pppoe_profiles = $ipv4pool->pppoe_profiles;

        //update pppoe profiles in database
        foreach ($pppoe_profiles as $pppoe_profile) {
            $pppoe_profile->ipv4pool_id = $new_pool->id;
            $pppoe_profile->save();
        }

        //reallocate ipv4 addressses
        ReAllocateIPv4::dispatch($new_pool)
            ->onConnection('database')
            ->onQueue('re_allocate_ipv4');

        return redirect()->route('ipv4pools.index')->with('success', 'IPv4 Pool subnet Replaced Successfully');
    }
}
