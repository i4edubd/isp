<?php

namespace App\Http\Controllers;

use App\Models\customer_zone;
use Illuminate\Http\Request;

class CustomerZoneController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();

        $customer_zones = customer_zone::where('operator_id', $operator->id)->orderBy('name')->get();

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.customer-zones', [
                    'customer_zones' => $customer_zones,
                ]);
                break;

            case 'operator':
                return view('admins.operator.customer-zones', [
                    'customer_zones' => $customer_zones,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.customer-zones', [
                    'customer_zones' => $customer_zones,
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
                return view('admins.group_admin.customer-zone-create');
                break;

            case 'operator':
                return view('admins.operator.customer-zone-create');
                break;

            case 'sub_operator':
                return view('admins.sub_operator.customer-zone-create');
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
        ]);

        $customer_zone = new customer_zone();
        $customer_zone->operator_id = $request->user()->id;
        $customer_zone->name = $request->name;
        $customer_zone->save();

        return redirect()->route('customer_zones.index')->with('success', 'Zone has been added successfully!');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\customer_zone  $customer_zone
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, customer_zone $customer_zone)
    {
        $operator = $request->user();

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.customer-zone-edit', [
                    'customer_zone' => $customer_zone,
                ]);
                break;

            case 'operator':
                return view('admins.operator.customer-zone-edit', [
                    'customer_zone' => $customer_zone,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.customer-zone-edit', [
                    'customer_zone' => $customer_zone,
                ]);
                break;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\customer_zone  $customer_zone
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, customer_zone $customer_zone)
    {
        if ($request->user()->id !== $customer_zone->operator_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required',
        ]);

        $customer_zone->name = $request->name;
        $customer_zone->save();

        return redirect()->route('customer_zones.index')->with('success', 'Zone edited successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\customer_zone  $customer_zone
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, customer_zone $customer_zone)
    {
        if ($request->user()->id !== $customer_zone->operator_id) {
            abort(403);
        }
        $customer_zone->delete();
        return redirect()->route('customer_zones.index')->with('success', 'Zone deleted successfully!');
    }
}
