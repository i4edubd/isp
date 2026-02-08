<?php

namespace App\Http\Controllers;

use App\Models\Freeradius\customer;
use App\Models\custom_price;
use App\Models\customer_bill;
use App\Models\package;
use Illuminate\Http\Request;

class CustomPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function index(customer $customer)
    {
        $where = [
            ['operator_id', '=', $customer->operator_id],
            ['customer_id', '=', $customer->id],
            ['package_id', '=', $customer->package_id]
        ];

        $custom_price = custom_price::where($where)->firstOr(function () {
            return custom_price::make([
                'id' => 0,
                'price' => 0,
            ]);
        });

        return view('admins.group_admin.special-price', [
            'custom_price' => $custom_price,
            'customer' => $customer,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, customer $customer)
    {
        $operator = $request->user();

        $package = package::find($customer->package_id);

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.special-price-create', [
                    'customer' => $customer,
                    'package' => $package,
                ]);
                break;

            case 'operator':
                return view('admins.operator.special-price-create', [
                    'customer' => $customer,
                    'package' => $package,
                ]);
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, customer $customer)
    {
        $request->validate([
            'special_price' => 'required|numeric',
        ]);

        $custom_price = new custom_price();
        $custom_price->mgid = $customer->mgid;
        $custom_price->operator_id = $customer->operator_id;
        $custom_price->customer_id = $customer->id;
        $custom_price->package_id = $customer->package_id;
        $custom_price->price = $request->special_price;
        $custom_price->save();

        customer_bill::where('operator_id', $customer->operator_id)
            ->where('customer_id', $customer->id)
            ->where('package_id', $customer->package_id)
            ->update(['amount' => $request->special_price]);

        return redirect()->route('customers.index')->with('success', 'Special Price has been saved successfully!');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @param  \App\Models\custom_price  $custom_price
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, customer $customer, custom_price $custom_price)
    {
        $operator = $request->user();

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.special-price-edit', [
                    'customer' => $customer,
                    'custom_price' => $custom_price,
                ]);
                break;

            case 'operator':
                return view('admins.operator.special-price-edit', [
                    'customer' => $customer,
                    'custom_price' => $custom_price,
                ]);
                break;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Freeradius\customer  $customer
     * @param  \App\Models\custom_price  $custom_price
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, customer $customer, custom_price $custom_price)
    {
        $request->validate([
            'special_price' => 'required|numeric',
        ]);

        $custom_price->price = $request->special_price;
        $custom_price->save();

        customer_bill::where('operator_id', $customer->operator_id)
            ->where('customer_id', $customer->id)
            ->where('package_id', $customer->package_id)
            ->update(['amount' => $request->special_price]);

        return redirect()->route('customers.index')->with('success', 'Special Price has been saved successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @param  \App\Models\custom_price  $custom_price
     * @return \Illuminate\Http\Response
     */
    public function destroy(customer $customer, custom_price $custom_price)
    {
        $package = package::findOrFail($customer->package_id);

        customer_bill::where('operator_id', $customer->operator_id)
            ->where('customer_id', $customer->id)
            ->where('package_id', $customer->package_id)
            ->update(['amount' => $package->price]);

        $custom_price->delete();

        return redirect()->route('customers.index')->with('success', 'Special Price has been deleted successfully!');
    }
}
