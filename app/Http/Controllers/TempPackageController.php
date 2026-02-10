<?php

namespace App\Http\Controllers;

use App\Models\pppoe_profile;
use App\Models\temp_package;
use Illuminate\Http\Request;

class TempPackageController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'connection_type' => 'required',
        ]);

        $temp_package = new temp_package();
        $temp_package->mgid = $request->user()->id;
        $temp_package->connection_type = $request->connection_type;
        $temp_package->save();

        if ($request->connection_type == 'PPPoE') {
            return redirect()->route('temp_packages.edit', ['temp_package' => $temp_package->id]);
        } else {
            return redirect()->route('temp_packages.master_packages.create', ['temp_package' => $temp_package->id]);
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\temp_package  $temp_package
     * @return \Illuminate\Http\Response
     */
    public function edit(temp_package $temp_package)
    {
        $profiles = pppoe_profile::where('mgid', $temp_package->mgid)->get();

        return view('admins.group_admin.temp-packages-edit', [
            'temp_package' => $temp_package,
            'profiles' => $profiles,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\temp_package  $temp_package
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, temp_package $temp_package)
    {
        $request->validate([
            'pppoe_profile_id' => 'required',
        ]);

        $temp_package->pppoe_profile_id = $request->pppoe_profile_id;
        $temp_package->save();
        return redirect()->route('temp_packages.master_packages.create', ['temp_package' => $temp_package->id]);
    }
}
