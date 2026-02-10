<?php

namespace App\Http\Controllers;

use App\Models\Freeradius\customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CustomerIdSearchController extends Controller
{

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $operator = $request->user();

        if (!$operator) {
            return 0;
        }

        if ($operator->role == 'manager') {
            $operator_id = $operator->group_admin->id;
        } else {
            $operator_id = $operator->id;
        }

        $cache_key = 'customer_' . $operator_id . '_' . $id;

        $seconds = 300;

        $customer = Cache::remember($cache_key, $seconds, function () use ($id) {
            return customer::where('id', $id)->first();
        });

        if ($customer) {
            if ($request->user()->can('viewDetails', $customer)) {
                return view('admins.components.customers-search-result', [
                    'customer' => $customer,
                ]);
            } else {
                return 'Unauthorized Request';
            }
        } else {
            return 'Customer Not Found';
        }
    }
}
