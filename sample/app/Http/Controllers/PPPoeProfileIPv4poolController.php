<?php

namespace App\Http\Controllers;

use App\Jobs\ReAllocateIPv4ForProfile;
use App\Models\pppoe_profile;
use App\Models\ipv4pool;
use Illuminate\Http\Request;

class PPPoeProfileIPv4poolController extends Controller
{

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\pppoe_profile  $pppoe_profile
     * @return \Illuminate\Http\Response
     */
    public function edit(pppoe_profile $pppoe_profile)
    {
        $pools = ipv4pool::where('mgid', $pppoe_profile->mgid)->get();

        $ipv4pools = $pools->except($pppoe_profile->ipv4pool_id);

        return view('admins.group_admin.pppoe-profiles-ipv4pool-edit', [
            'pppoe_profile' => $pppoe_profile,
            'ipv4pools' => $ipv4pools,
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\pppoe_profile  $pppoe_profile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, pppoe_profile $pppoe_profile)
    {
        $request->validate([
            'ipv4pool_id' => 'required',
        ]);

        if ($request->ipv4pool_id == $pppoe_profile->ipv4pool_id) {
            return redirect()->route('pppoe_profiles.index');
        }

        $ipv4pool = ipv4pool::findOrFail($pppoe_profile->ipv4pool_id);

        $new_pool = ipv4pool::findOrFail($request->ipv4pool_id);

        $required_space = $ipv4pool->used_space;

        $free_space = $new_pool->broadcast - $new_pool->gateway - $new_pool->used_space;

        if ($required_space > $free_space) {
            return redirect()->route('pppoe_profiles.index')->with('error', 'New IPv4 Pool has no enough capacity');
        }

        //update pppoe profiles in database
        $pppoe_profile->ipv4pool_id = $request->ipv4pool_id;
        $pppoe_profile->save();

        // update customers && ipv4address
        ReAllocateIPv4ForProfile::dispatch($pppoe_profile)
            ->onConnection('database')
            ->onQueue('re_allocate_ipv4');

        return redirect()->route('pppoe_profiles.index')->with('success', 'PPPoE Profile IPv4pool updated!');
    }
}
