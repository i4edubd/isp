<?php

namespace App\Http\Controllers;

use App\Models\customer_zone;
use Illuminate\Http\Request;

class ProfileUpdateByCustomerController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $all_customer = $request->user('customer');
        $customer = CacheController::getCustomerUseAllCustomer($all_customer);
        $operator = CacheController::getOperator($customer->operator_id);
        $customer_zones = customer_zone::where('operator_id', $customer->operator_id)->get();
        return view('customers.customer-edit-profile', [
            'customer' => $customer,
            'operator' => $operator,
            'customer_zones' => $customer_zones,
        ]);
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
            'name' => 'required|string',
            'zone_id' => 'numeric|nullable',
        ]);

        $all_customer = $request->user('customer');
        $customer = CacheController::getCustomerUseAllCustomer($all_customer);
        $customer->name = $request->name;
        if ($request->filled('zone_id')) {
            $customer->zone_id = $request->zone_id;
        }
        $customer->save();

        CacheController::forgetCustomer($customer);

        return redirect()->route('customers.profile')->with('info', 'Profile Updated!');
    }
}
