<?php

namespace App\Http\Controllers;

use App\Models\ipv6pool;
use Illuminate\Http\Request;

class Ipv6poolNameController extends Controller
{

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ipv6pool  $ipv6pool
     * @return \Illuminate\Http\Response
     */
    public function edit(ipv6pool $ipv6pool)
    {
        return view('admins.group_admin.ipv6pools-name-edit', [
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
        // validate request
        $request->validate([
            'name' => 'required',
        ]);

        // Nothing to change
        if ($request->name == $ipv6pool->name) {
            return redirect()->route('ipv6pools.index');
        }

        //Duplicate
        $name = getVarName($request->name);

        $where = [
            ['mgid', '=', $request->user()->id],
            ['name', '=', $name],
        ];

        if (ipv6pool::where($where)->count()) {
            return redirect()->route('ipv6pools.index')->with('error', 'Duplicate IPv6pool Name');
        }

        //update ipv6pool
        $ipv6pool->name = $name;
        $ipv6pool->save();

        return redirect()->route('ipv6pools.index')->with('success', 'IPv6Pool Name has been updated successfully');
    }
}
