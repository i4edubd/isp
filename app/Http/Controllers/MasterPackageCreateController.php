<?php

namespace App\Http\Controllers;

use App\Models\master_package;
use App\Models\temp_package;
use Illuminate\Http\Request;

class MasterPackageCreateController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\temp_package  $temp_package
     * @return \Illuminate\Http\Response
     */
    public function create(temp_package $temp_package)
    {
        switch ($temp_package->connection_type) {
            case 'StaticIp':
                return view('admins.group_admin.static-master-package-create', [
                    'temp_package' => $temp_package,
                ]);
                break;

            case 'Other':
                return view('admins.group_admin.other-master-package-create', [
                    'temp_package' => $temp_package,
                ]);
                break;

            case 'PPPoE':
            case 'Hotspot':
                return view('admins.group_admin.master-package-create', [
                    'temp_package' => $temp_package,
                ]);
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\temp_package  $temp_package
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, temp_package $temp_package)
    {
        //validate
        if ($request->name == 'Trial') {
            return redirect()->route('master_packages.index')->with('error', 'Trial package cannot be created!');
        }

        switch ($temp_package->connection_type) {
            case 'StaticIp':
                $request->validate([
                    'name' => ['required', 'string', 'max:255'],
                    'price' => ['required', 'integer'],
                ]);
                break;
            case 'Other':
                $request->validate([
                    'name' => ['required', 'string', 'max:255'],
                    'price' => ['required', 'integer'],
                    'operator_price' => ['required', 'integer'],
                ]);
                break;
            case 'PPPoE':
            case 'Hotspot':
                $request->validate([
                    'name' => ['required', 'string', 'max:255'],
                    'rate_limit' => ['required', 'integer'],
                    'speed_controller' => ['required', 'in:Router,Radius_Server'],
                    'rate_unit' => ['nullable', 'in:M,k'],
                    'validity' => ['required', 'integer', 'min:1'],
                    'volume_limit' => ['required', 'integer'],
                    'volume_unit' => ['required'],
                ]);
                break;
        }

        $master_package = new master_package();
        $master_package->mgid = $request->user()->id;
        $master_package->pppoe_profile_id = $temp_package->pppoe_profile_id;
        $master_package->connection_type = $temp_package->connection_type;
        $master_package->name = $request->name;

        if ($request->filled('rate_limit')) {
            $master_package->rate_limit = $request->rate_limit;
        }

        if ($request->filled('rate_unit')) {
            $master_package->rate_unit = $request->rate_unit;
        }

        if ($request->filled('speed_controller')) {
            $master_package->speed_controller = $request->speed_controller;
        }

        if ($request->filled('volume_limit')) {
            $master_package->volume_limit = $request->volume_limit;
        }

        if ($request->filled('volume_unit')) {
            $master_package->volume_unit = $request->volume_unit;
        }

        if ($request->filled('validity')) {
            $master_package->validity = $request->validity;
        } else {
            $master_package->validity = 30;
        }

        if ($request->filled('validity_unit')) {
            $master_package->validity_unit = $request->validity_unit;
        }

        if ($request->filled('price')) {
            $master_package->price = $request->price;
        }

        if ($request->filled('operator_price')) {
            $master_package->operator_price = $request->operator_price;
        }

        if ($request->filled('visibility')) {
            $master_package->visibility = $request->visibility;
        }

        $master_package->save();

        $temp_package->delete();

        return redirect()->route('master_packages.index')->with('success', 'Package has been added successfully');
    }
}
