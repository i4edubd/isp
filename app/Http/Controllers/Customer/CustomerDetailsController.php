<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\QueryInRouterController;
use App\Http\Controllers\RrdGraphApiController;
use App\Models\customer_bill;
use App\Models\Freeradius\customer;
use App\Models\pgsql\pgsql_radacct_history;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CustomerDetailsController extends Controller
{

    /**
     * Display the specified resource.
     *
     * @param  int  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, int $customer)
    {

        $operator_id = $request->user()->id;

        $cache_key = 'customer_' . $operator_id . '_' . $customer;

        $seconds = 300;

        $customer = Cache::remember($cache_key, $seconds, function () use ($customer) {
            return customer::with(['custom_attributes', 'radaccts'])->where('id', $customer)->firstOrFail();
        });

        return self::getDetailedCustomerView($customer);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Freeradius\customer $customer
     * @return \Illuminate\Http\Response
     */
    public static function getDetailedCustomerView(customer $customer)
    {
        $radaccts_history_key = 'radaccts_history_' . $customer->operator_id . '_' . $customer->id;

        $seconds = 300;

        $radaccts_history = Cache::remember($radaccts_history_key, $seconds, function () use ($customer) {
            return pgsql_radacct_history::where('username', $customer->username)->get();
        });

        $cache_key = 'customer_graph_' . $customer->operator_id . '_' . $customer->id;

        $seconds = 300;

        $graph = Cache::remember($cache_key, $seconds, function () use ($customer) {
            return RrdGraphApiController::getImage($customer);
        });

        if ($customer->payment_status == 'billed') {
            $bills = customer_bill::where('operator_id', $customer->operator_id)
                ->where('customer_id', $customer->id)
                ->get();
        } else {
            $bills = [];
        }

        $is_online = $customer->is_online;
        if (!$is_online) {
            $is_online = QueryInRouterController::getOnlineStatus($customer);
        }

        return view('admins.components.customer-details', [
            'is_online' => $is_online,
            'customer' => $customer,
            'radaccts_history' => $radaccts_history,
            'graph' => $graph,
            'bills' => $bills,
        ]);
    }
}
