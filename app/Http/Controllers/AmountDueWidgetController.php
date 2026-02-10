<?php

namespace App\Http\Controllers;

use App\Models\customer_bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AmountDueWidgetController extends Controller
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
        $filter[] = ['month', '=', date(config('app.month_format'))];
        $filter[] = ['year', '=', date(config('app.year_format'))];
        $filter[] = ['operator_id', '=', $operator_id];

        $cache_key = 'amount_due_widget_' . $operator_id;

        $seconds = 200;

        return Cache::remember($cache_key, $seconds, function () use ($filter) {
            return customer_bill::where($filter)->sum('amount');
        });
    }
}
