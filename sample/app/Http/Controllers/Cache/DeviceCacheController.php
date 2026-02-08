<?php

namespace App\Http\Controllers\Cache;

use App\Http\Controllers\Controller;
use App\Models\operator;
use Illuminate\Support\Facades\Cache;

class DeviceCacheController extends Controller
{
    /**
     * Get devices from cache
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Support\Collection
     */
    public static function getDevices(operator $operator)
    {
        $key = 'app_models_device_list_' . $operator->id;
        $ttl = 300;
        return Cache::remember($key, $ttl, function () use ($operator) {
            return $operator->devices;
        });
    }
}
