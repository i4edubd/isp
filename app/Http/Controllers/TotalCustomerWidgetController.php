<?php

namespace App\Http\Controllers;

use App\Models\Freeradius\customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TotalCustomerWidgetController extends Controller
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

        $cache_key = 'total_customer_widget_' . $operator->id;

        $seconds = 200;

        return Cache::remember($cache_key, $seconds, function () use ($operator) {
            switch ($operator->role) {
                case 'group_admin':
                    return customer::where('mgid', $operator->id)->count();
                    break;
                case 'operator':
                    return customer::where('operator_id', $operator->id)->count();
                    break;
                case 'sub_operator':
                    return customer::where('operator_id', $operator->id)->count();
                    break;
                case 'manager':
                    return customer::where('operator_id', $operator->gid)->count();
                    break;
                default:
                    return 0;
                    break;
            }
        });
    }
}
