<?php

namespace App\Http\Controllers;

use App\Models\device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();

        $devices = device::where('operator_id', $operator->id)->orderBy('name')->get();

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.devices', [
                    'devices' => $devices,
                ]);
                break;

            case 'operator':
                return view('admins.operator.devices', [
                    'devices' => $devices,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.devices', [
                    'devices' => $devices,
                ]);
                break;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $operator = $request->user();

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.devices-create');
                break;

            case 'operator':
                return view('admins.operator.devices-create');
                break;

            case 'sub_operator':
                return view('admins.sub_operator.devices-create');
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'location' => 'required',
        ]);

        $device = new device();
        $device->operator_id = $request->user()->id;
        $device->name = $request->name;
        $device->location = $request->location;
        $device->save();

        return redirect()->route('devices.index')->with('success', 'Device added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\device  $device
     * @return \Illuminate\Http\Response
     */
    public function show(device $device)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\device  $device
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, device $device)
    {
        $operator = $request->user();

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.devices-edit', [
                    'device' => $device,
                ]);
                break;

            case 'operator':
                return view('admins.operator.devices-edit', [
                    'device' => $device,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.devices-edit', [
                    'device' => $device,
                ]);
                break;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\device  $device
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, device $device)
    {
        if ($request->user()->id !== $device->operator_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required',
            'location' => 'required',
        ]);

        $device->name = $request->name;
        $device->location = $request->location;
        $device->save();

        return redirect()->route('devices.index')->with('success', 'Device Edited successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\device  $device
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, device $device)
    {
        if ($request->user()->id !== $device->operator_id) {
            abort(403);
        }

        $device->delete();

        return redirect()->route('devices.index')->with('success', 'Device deleted successfully');
    }
}
