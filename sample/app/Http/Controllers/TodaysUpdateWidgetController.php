<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Customer\CustomerController;
use App\Models\customer_bill;
use App\Models\customer_payment;
use App\Models\Freeradius\customer;
use App\Models\sms_history;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TodaysUpdateWidgetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function willBeSuspended(Request $request)
    {
        $operator = $request->user();

        if ($operator->role == 'manager') {
            $operator_id = $operator->group_admin->id;
        } else {
            $operator_id = $operator->id;
        }

        $cache_key = 'will_be_suspended_' . $operator_id;

        $seconds = 200;

        return Cache::remember($cache_key, $seconds, function () use ($operator_id) {

            $customers = customer::where('operator_id', $operator_id)->get();

            $customers = $customers->filter(function ($customer) {
                return CustomerController::willBeSuspended($customer);
            });

            return $customers->count();
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function amountToBeCollected(Request $request)
    {
        $operator = $request->user();

        if ($operator->role == 'manager') {
            $operator_id = $operator->group_admin->id;
        } else {
            $operator_id = $operator->id;
        }

        $filter = [];
        $filter[] = ['due_date', '=', date(config('app.date_format'))];
        $filter[] = ['operator_id', '=', $operator_id];

        $cache_key = 'amount_to_be_collected_' . $operator_id;

        $seconds = 200;

        return Cache::remember($cache_key, $seconds, function () use ($filter) {
            return customer_bill::where($filter)->sum('amount');
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function collectedAmount(Request $request)
    {
        $operator = $request->user();

        if ($operator->role == 'manager') {
            $operator_id = $operator->group_admin->id;
        } else {
            $operator_id = $operator->id;
        }

        $filter = [];
        $filter[] = ['date', '=', date(config('app.date_format'))];
        $filter[] = ['pay_status', '=', 'Successful'];
        $filter[] = ['operator_id', '=', $operator_id];

        $cache_key = 'collected_amount_' . $operator_id;

        $seconds = 200;

        return Cache::remember($cache_key, $seconds, function () use ($filter) {
            return customer_payment::where($filter)->sum('amount_paid');
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function smsSent(Request $request)
    {
        $operator = $request->user();

        if ($operator->role == 'manager') {
            $operator_id = $operator->group_admin->id;
        } else {
            $operator_id = $operator->id;
        }

        $filter = [];
        $filter[] = ['date', '=', date(config('app.date_format'))];
        $filter[] = ['operator_id', '=', $operator_id];

        $cache_key = 'todays_sms_widget_' . $operator_id;

        $seconds = 200;

        return Cache::remember($cache_key, $seconds, function () use ($filter) {
            return sms_history::where($filter)->count();
        });
    }
}
