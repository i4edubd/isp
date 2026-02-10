<?php

namespace App\Http\Controllers;

use App\Models\Freeradius\customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SuspendCustomerWidgetController extends Controller
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

        $cache_key = 'suspended_customer_widget_' . $operator_id;

        $seconds = 300;

        return Cache::remember($cache_key, $seconds, function () use ($operator_id) {
            $customers = customer::where('operator_id', $operator_id)->get();
            return $customers->where('status', 'suspended')->count();
        });
    }
}
