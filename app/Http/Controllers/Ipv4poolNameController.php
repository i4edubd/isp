<?php

namespace App\Http\Controllers;

use App\Models\ipv4pool;
use Illuminate\Http\Request;

class Ipv4poolNameController extends Controller
{

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ipv4pool  $ipv4pool
     * @return \Illuminate\Http\Response
     */
    public function edit(ipv4pool $ipv4pool)
    {
        return view('admins.group_admin.ipv4pool-name-edit', [
            'ipv4pool' => $ipv4pool,
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
        // validate request
        $request->validate([
            'name' => 'required',
        ]);

        // Nothing to change
        if ($request->name == $ipv4pool->name) {
            return redirect()->route('ipv4pools.index');
        }

        //Duplicate
        $name = getVarName($request->name);

        $where = [
            ['mgid', '=', $request->user()->id],
            ['name', '=', $name],
        ];

        if (ipv4pool::where($where)->count()) {
            return redirect()->route('ipv4pools.index')->with('error', 'Duplicate IPv4pool Name');
        }

        //update ipv4pool
        $ipv4pool->name = $name;
        $ipv4pool->save();

        return redirect()->route('ipv4pools.index')->with('success', 'IPv4Pool Name has been updated successfully');
    }
}
