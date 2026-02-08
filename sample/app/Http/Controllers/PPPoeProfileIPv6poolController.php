<?php

namespace App\Http\Controllers;

use App\Models\ipv6pool;
use App\Models\pppoe_profile;
use Illuminate\Http\Request;

class PPPoeProfileIPv6poolController extends Controller
{

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\pppoe_profile  $pppoe_profile
     * @return \Illuminate\Http\Response
     */
    public function edit(pppoe_profile $pppoe_profile)
    {
        $pools = ipv6pool::where('mgid', $pppoe_profile->mgid)->get();

        $ipv6pools = $pools->except($pppoe_profile->ipv6pool_id);

        return view('admins.group_admin.pppoe-profiles-ipv6pool-edit', [
            'pppoe_profile' => $pppoe_profile,
            'ipv6pools' => $ipv6pools,
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
            'ipv6pool_id' => 'required',
        ]);

        if ($request->ipv6pool_id == $pppoe_profile->ipv6pool_id) {
            return redirect()->route('pppoe_profiles.index');
        }

        $new_pool = ipv6pool::findOrFail($request->ipv6pool_id);

        //update pppoe profiles in database
        $pppoe_profile->ipv6pool_id = $new_pool->id;
        $pppoe_profile->save();

        return redirect()->route('pppoe_profiles.index')->with('success', 'PPPoE Profile IPv6pool updated!');
    }
}
