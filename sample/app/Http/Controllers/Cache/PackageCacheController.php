<?php

namespace App\Http\Controllers\Cache;

use App\Http\Controllers\Controller;
use App\Models\operator;
use App\Models\package;
use Illuminate\Support\Facades\Cache;

class PackageCacheController extends Controller
{
    /**
     * Get list from cache
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Support\Collection
     */
    public static function getAllPackages(operator $operator)
    {
        $key = 'app_models_package_all_packages_' . $operator->id;
        $ttl = 300;
        return Cache::remember($key, $ttl, function () use ($operator) {
            return $operator->allPackages()->with('operator')->get();
        });
    }

    /**
     * Get list from cache
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Support\Collection
     */
    public static function getPackages(operator $operator)
    {
        $key = 'app_models_package_packages_' . $operator->id;
        $ttl = 300;
        return Cache::remember($key, $ttl, function () use ($operator) {
            return $operator->packages;
        });
    }

    /**
     * Retrieving Package From The Cache
     *
     * @param  int $package_id
     * @return \App\Models\package
     */
    public static function getPackage(int $package_id)
    {
        $key = 'app_models_package_' . $package_id;
        $ttl = 300;
        return Cache::remember($key, $ttl, function () use ($package_id) {
            return package::with(['parent_package', 'master_package', 'master_package.fair_usage_policy'])->find($package_id);
        });
    }
}
