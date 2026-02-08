<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\all_customer;
use App\Models\Freeradius\customer;
use App\Models\temp_customer;
use Illuminate\Http\Request;

class TempCustomerController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', customer::class);

        $operator = $request->user();

        $customer_zones = $operator->customer_zones->sortBy('name');

        $devices = $operator->devices->sortBy('name');

        if ($request->filled('parent_id')) {
            $parent_id = $request->parent_id;
        } else {
            $parent_id = 0;
        }

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.temp-customer-create', [
                    'customer_zones' => $customer_zones,
                    'devices' => $devices,
                    'parent_id' => $parent_id,
                ]);
                break;

            case 'operator':
                return view('admins.operator.temp-customer-create', [
                    'customer_zones' => $customer_zones,
                    'devices' => $devices,
                    'parent_id' => $parent_id,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.temp-customer-create', [
                    'customer_zones' => $customer_zones,
                    'devices' => $devices,
                    'parent_id' => $parent_id,
                ]);
                break;

            case 'manager':
                return view('admins.manager.temp-customer-create', [
                    'customer_zones' => $customer_zones,
                    'devices' => $devices,
                    'parent_id' => $parent_id,
                ]);
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
            'connection_type' => 'required',
            'name' => 'required',
            'mobile' => 'nullable|numeric',
            'nid' => 'nullable|numeric',
            'parent_id' => 'numeric|required',
        ]);

        $mobile = validate_mobile($request->mobile, getCountryCode($request->user()->id));

        if ($mobile !== 0) {
            // <<Duplicate Mobile || all_customer
            $duplicate_mobile = all_customer::where('mobile', $mobile)->count();

            if ($duplicate_mobile) {
                return redirect()->route('temp_customers.create')->with('error', 'Duplicate Mobile');
            }
            // Duplicate Mobile || all_customer>>

            // <<Duplicate Mobile || customer
            $duplicate_mobile = customer::where('mobile', $mobile)->count();

            if ($duplicate_mobile) {
                return redirect()->route('temp_customers.create')->with('error', 'Duplicate Mobile');
            }
            // Duplicate Mobile || customer>>

            // Duplicate username for hotspot
            if ($request->connection_type == 'Hotspot') {
                $duplicate_username = customer::where('username', $mobile)->count();
                if ($duplicate_username) {
                    return redirect()->route('temp_customers.create')->with('error', 'Duplicate Mobile/Username');
                }
            }
        } else {
            if ($request->parent_id == 0) {
                return redirect()->route('temp_customers.create')->with('error', 'Invalid Mobile');
            } else {
                $mobile = null;
            }
        }

        if ($request->user()->role == 'manager') {
            $operator = $request->user()->group_admin;
        } else {
            $operator = $request->user();
        }

        // delete previous attempts
        temp_customer::where('created_at', '<=', now()->subHour())->delete();

        $temp_customer = new temp_customer();
        $temp_customer->parent_id = $request->parent_id;
        $temp_customer->mgid = $operator->mgid;
        $temp_customer->gid = $operator->gid;
        $temp_customer->operator_id = $operator->id;
        $temp_customer->company = $operator->company;
        $temp_customer->connection_type = $request->connection_type;
        $temp_customer->zone_id = $request->zone_id;
        if ($request->filled('device_id')) {
            $temp_customer->device_id = $request->device_id;
        }
        $temp_customer->name = $request->name;
        $temp_customer->mobile = $mobile;
        $temp_customer->email = $request->email;
        $temp_customer->nid = $request->nid;
        if ($request->filled('date_of_birth')) {
            $temp_customer->date_of_birth = date_format(date_create($request->date_of_birth), config('app.date_format'));
        }
        $temp_customer->save();

        return redirect()->route('temp_customer.billing_profile.create', ['temp_customer' => $temp_customer->id]);
    }
}
