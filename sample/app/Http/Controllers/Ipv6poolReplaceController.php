<?php

namespace App\Http\Controllers;

use App\Models\ipv6pool;
use Illuminate\Http\Request;

class Ipv6poolReplaceController extends Controller
{

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ipv6pool  $ipv6pool
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, ipv6pool $ipv6pool)
    {

        $pools = ipv6pool::where('mgid', $request->user()->id)->get();

        $ipv6pools = $pools->except($ipv6pool->id);

        return view('admins.group_admin.ipv6pools-replace', [
            'ipv6pool' => $ipv6pool,
            'ipv6pools' => $ipv6pools,
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
            'ipv6pool_id' => 'required',
        ]);

        $new_pool = ipv6pool::findOrFail($request->ipv6pool_id);

        //get the PPPoE profiles using this IPv6 Pool
        $pppoe_profiles = $ipv6pool->pppoe_profiles;

        //update pppoe profiles in database
        foreach ($pppoe_profiles as $pppoe_profile) {
            $pppoe_profile->ipv6pool_id = $new_pool->id;
            $pppoe_profile->save();
        }

        return redirect()->route('ipv6pools.index')->with('success', 'IPv6 Pool has been replaced successfully');
    }
}
