<?php

namespace App\Http\Controllers;

use App\Models\Freeradius\customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PaidCustomerWidgetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();

        if ($operator->role == 'manager') {
            $operator_id = $operator->group_admin->id;
        } else {
            $operator_id = $operator->id;
        }

        $filter = [];
        $filter[] = ['payment_status', '=', 'paid'];
        $filter[] = ['operator_id', '=', $operator_id];

        $cache_key = 'paid_customer_widget_' . $operator_id;

        $seconds = 200;

        return Cache::remember($cache_key, $seconds, function () use ($filter) {
            return customer::where($filter)->count();
        });
    }
}
