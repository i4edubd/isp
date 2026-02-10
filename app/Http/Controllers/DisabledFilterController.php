<?php

namespace App\Http\Controllers;

use App\Models\disabled_filter;
use App\Models\operator;
use Illuminate\Http\Request;

class DisabledFilterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static function index(string $model)
    {
        return match ($model) {
            'customer' => [
                'connection_type' => 'connection type',
                'billing_type' => 'billing type',
                'status' => 'status',
                'payment_status' => 'payment status',
                'zone_id' => 'zone',
                'device_id' => 'device',
                'package_id' => 'package',
                'billing_profile_id' => 'billing profile',
                'ip' => 'ip',
                'mac_bind' => 'mac bind',
                'advance_payment' => 'advance payment',
                'year' => 'Registration Year',
                'month' => 'Registration Month',
                'username' => 'username',
                'comment' => 'comment',
                'operator_id' => 'operator',
            ],
            default => [],
        };
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $request->validate([
            'model' => 'required|in:customer',
        ]);

        $operator = $request->user();
        $model = $request->model;

        $filters = self::getFilters($operator, $model);

        return view('admins.components.disabled-filters', [
            'filters' => $filters,
            'model' => $model,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'model' => 'required',
        ]);

        $model = $request->model;

        disabled_filter::where('operator_id', $request->user()->id)->where('model', $model)->delete();

        $all_filters = self::index($model);

        foreach ($all_filters as $key => $value) {
            if ($request->filled($key)) {
                continue;
            } else {
                $disabled_filter = new disabled_filter();
                $disabled_filter->operator_id = $request->user()->id;
                $disabled_filter->model = $model;
                $disabled_filter->filter = $key;
                $disabled_filter->save();
            }
        }

        return match ($model) {
            'customer' => redirect()->route('customers.index'),
        };
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\operator  $operator
     * @param string $model
     * @return Illuminate\Support\Collection
     */
    public static function getFilters(operator $operator, string $model)
    {
        $collections = [
            'disabled' => [],
            'enabled' => [],
        ];

        $disabled_filters = disabled_filter::where('operator_id', $operator->id)
            ->where('model', $model)->get();

        if (count($disabled_filters) == 0) {
            $collections['enabled'] = self::index($model);
            return collect($collections);
        }

        $all_filters = self::index($model);

        $disabled = [];
        $enabled = [];

        foreach ($all_filters as $key => $value) {
            if ($disabled_filters->where('filter', $key)->count()) {
                $disabled[$key] = $value;
            } else {
                $enabled[$key] = $value;
            }
        }

        $collections['disabled'] = $disabled;
        $collections['enabled'] = $enabled;

        return collect($collections);
    }
}
