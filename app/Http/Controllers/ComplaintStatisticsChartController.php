<?php

namespace App\Http\Controllers;

use App\Models\customer_complain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ComplaintStatisticsChartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
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
        $filter[] = ['operator_id', '=', $operator_id];
        $filter[] = ['is_active', '=', 1];

        $cache_key = 'complaint_statistics_chart_' . $operator_id;

        $seconds = 300;

        return Cache::remember($cache_key, $seconds, function () use ($filter) {
            return customer_complain::where($filter)->count();
        });
    }
}
