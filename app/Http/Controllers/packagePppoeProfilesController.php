<?php

namespace App\Http\Controllers;

use App\Jobs\ReAllocateIPv4ForPackage;
use App\Models\master_package;
use App\Models\pppoe_profile;
use Illuminate\Http\Request;

class packagePppoeProfilesController extends Controller
{

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\master_package  $master_package
     * @param  \App\Models\pppoe_profile  $pppoe_profile
     * @return \Illuminate\Http\Response
     */
    public function edit(master_package $master_package, pppoe_profile $pppoe_profile)
    {
        $all_profiles = pppoe_profile::where('mgid', $pppoe_profile->mgid)->get();

        $profiles = $all_profiles->except($pppoe_profile->id);

        return view('admins.group_admin.package-pppoe-profile-edit', [
            'pppoe_profile' => $pppoe_profile,
            'master_package' => $master_package,
            'profiles' => $profiles,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\master_package  $master_package
     * @param  \App\Models\pppoe_profile  $pppoe_profile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, master_package $master_package, pppoe_profile $pppoe_profile)
    {
        $request->validate([
            'pppoe_profile_id' => 'required'
        ]);

        if ($request->pppoe_profile_id == $pppoe_profile->id) {
            return redirect()->route('master_packages.index');
        }

        $new_profile = pppoe_profile::findOrFail($request->pppoe_profile_id);

        $ipv4pool = $pppoe_profile->ipv4pool;

        $new_pool = $new_profile->ipv4pool;

        if ($ipv4pool->id !== $new_pool->id) {

            $required_space = $ipv4pool->used_space;

            $free_space = $new_pool->broadcast - $new_pool->gateway - $new_pool->used_space;

            if ($required_space > $free_space) {
                return redirect()->route('master_packages.index')->with('error', 'Not Enough IPv4 Address to replace');
            }
        }

        //update package
        $master_package->pppoe_profile_id = $new_profile->id;
        $master_package->save();

        //update customer's ip addresses
        ReAllocateIPv4ForPackage::dispatch($master_package)
            ->onConnection('database')
            ->onQueue('re_allocate_ipv4');

        return redirect()->route('master_packages.index')->with('success', 'PPP Profile has been replaced successfully!');
    }
}
