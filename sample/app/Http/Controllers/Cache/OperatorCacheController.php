<?php

namespace App\Http\Controllers\Cache;

use App\Http\Controllers\Controller;
use App\Models\operator;
use Illuminate\Support\Facades\Cache;

class OperatorCacheController extends Controller
{
    /**
     * Get list from cache
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Support\Collection
     */
    public static function getOperators(operator $operator)
    {
        $key = 'app_models_operator_list_' . $operator->id;
        $ttl = 300;
        return Cache::remember($key, $ttl, function () use ($operator) {
            return $operator->operators;
        });
    }
}
