<?php

namespace App\Http\Controllers;

use App\Models\customer_payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AmountPaidWidgetController extends Controller
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
        $filter[] = ['pay_status', '=', 'Successful'];
        $filter[] = ['operator_id', '=', $operator_id];

        $cache_key = 'amount_paid_widget_' . $operator_id;

        $seconds = 200;

        return Cache::remember($cache_key, $seconds, function () use ($filter) {
            return customer_payment::where($filter)->sum('amount_paid');
        });
    }
}
