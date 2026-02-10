<?php

namespace App\Http\Controllers;

use App\Jobs\PPPoEProfilesIpAllocationModeChangeJob;
use App\Models\pppoe_profile;
use Illuminate\Http\Request;

class PPPoeProfileIpAllocationModeController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\pppoe_profile  $pppoe_profile
     * @return \Illuminate\Http\Response
     */
    public function edit(pppoe_profile $pppoe_profile)
    {
        $this->authorize('update', $pppoe_profile);

        $new_mode = $pppoe_profile->ip_allocation_mode == 'static' ? 'dynamic' : 'static';

        return view('admins.group_admin.pppoe-profiles-IpAllocationMode-edit', [
            'pppoe_profile' => $pppoe_profile,
            'new_mode' => $new_mode,
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
            'ip_allocation_mode' => 'in:static,dynamic|required',
        ]);

        $pppoe_profile->ip_allocation_mode = $request->ip_allocation_mode;
        $pppoe_profile->save();

        if ($pppoe_profile->wasChanged('ip_allocation_mode')) {
            PPPoEProfilesIpAllocationModeChangeJob::dispatch($pppoe_profile)
                ->onConnection('database')
                ->onQueue('default');
        }

        return redirect()->route('pppoe_profiles.index')->with('success', 'IPv4 Allocation Mode Updated Successfully');
    }
}
