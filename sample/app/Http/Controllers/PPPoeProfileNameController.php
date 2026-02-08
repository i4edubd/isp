<?php

namespace App\Http\Controllers;

use App\Models\pgsql\pgsql_radreply;
use App\Models\pppoe_profile;
use Illuminate\Http\Request;

class PPPoeProfileNameController extends Controller
{

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\pppoe_profile  $pppoe_profile
     * @return \Illuminate\Http\Response
     */
    public function edit(pppoe_profile $pppoe_profile)
    {
        return view('admins.group_admin.pppoe-profiles-name-edit', [
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
        // validate request
        $request->validate([
            'name' => 'required',
        ]);

        $name = getVarName($request->name);

        // Nothing to change
        if ($name == $pppoe_profile->name) {
            return redirect()->route('pppoe_profiles.index');
        }

        // Duplicate
        $where = [
            ['mgid', '=', $request->user()->id],
            ['name', '=', $name],
        ];

        if (pppoe_profile::where($where)->count()) {
            return redirect()->route('pppoe_profiles.index')->with('error', 'Duplicate Profile Name');
        }

        //save old name
        $old_name = $pppoe_profile->name;

        //save new name
        $pppoe_profile->name =  $name;
        $pppoe_profile->save();

        // update Mikrotik-Group
        $update_where = [
            ['mgid', '=', $pppoe_profile->mgid],
            ['attribute', '=', 'Mikrotik-Group'],
            ['value', '=', $old_name],
        ];
        pgsql_radreply::where($update_where)->update(['value' => $name]);

        return redirect()->route('pppoe_profiles.index')->with('success', 'PPP Profile Name has been updated successfully');
    }
}
