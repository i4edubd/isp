<?php

namespace App\Http\Controllers;

use App\Jobs\ReAllocateIPv4ForProfile;
use App\Models\pppoe_profile;
use Illuminate\Http\Request;

class PPPoeProfileReplaceController extends Controller
{

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\pppoe_profile  $pppoe_profile
     * @return \Illuminate\Http\Response
     */
    public function edit(pppoe_profile $pppoe_profile)
    {
        $pppoe_profiles = pppoe_profile::where('mgid', $pppoe_profile->mgid)->get();

        $profiles = $pppoe_profiles->except($pppoe_profile->id);

        return view('admins.group_admin.pppoe-profiles-replace', [
            'profiles' => $profiles,
            'pppoe_profile' => $pppoe_profile,
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
            'pppoe_profile_id' => 'required'
        ]);


        if ($request->pppoe_profile_id == $pppoe_profile->id) {
            return redirect()->route('pppoe_profiles.index');
        }

        $new_profile = pppoe_profile::findOrFail($request->pppoe_profile_id);

        $ipv4pool = $pppoe_profile->ipv4pool;

        $new_pool = $new_profile->ipv4pool;

        if ($ipv4pool->id !== $new_pool->id) {

            $required_space = $ipv4pool->used_space;

            $free_space = $new_pool->broadcast - $new_pool->gateway - $new_pool->used_space;

            if ($required_space > $free_space) {
                return redirect()->route('pppoe_profiles.index')->with('error', 'Not Enough IPv4 Address to replace');
            }
        }

        //update packages
        foreach ($pppoe_profile->master_packages as $package) {
            $package->pppoe_profile_id = $new_profile->id;
            $package->save();
        }

        // update customers && ipv4address
        ReAllocateIPv4ForProfile::dispatch($new_profile)
            ->onConnection('database')
            ->onQueue('re_allocate_ipv4');

        return redirect()->route('pppoe_profiles.index')->with('success', 'PPP Profile has been replaced successfully!');
    }
}
