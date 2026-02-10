<?php

namespace App\Http\Controllers;

use App\Models\all_customer;
use App\Models\customer_count;
use App\Models\operator;
use Illuminate\Http\Request;

class CustomerCountController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     */
    public static function store()
    {
        # 1 Only Group Admin
        $operator_where[0] = ['role', '=', 'group_admin'];

        # 2 Paid Operator
        $operator_where[1] = ['subscription_type', '=', 'Paid'];

        # get group admins
        $operators = operator::where($operator_where)->get();

        foreach ($operators as $operator) {

            # count customers
            $user_count = all_customer::where('mgid', $operator->id)->count();

            //save count
            $active_customers = new customer_count();
            $active_customers->mgid = $operator->id;
            $active_customers->customer_count = $user_count;
            $active_customers->date = date(config('app.date_format'));
            $active_customers->week = date(config('app.week_format'));
            $active_customers->month = date(config('app.month_format'));
            $active_customers->year = date(config('app.year_format'));
            $active_customers->save();
        }
    }
}
