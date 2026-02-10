<?php

namespace App\Http\Controllers;

use App\Models\Freeradius\customer;
use App\Models\operator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class BlackListRemoveController extends Controller
{

    /**
     * Update the specified resource in storage.
     *
     * @param  string  $username
     * @return \Illuminate\Http\Response
     */
    public static function update(customer $customer)
    {
        $operator = operator::find($customer->operator_id);

        $ip = config('database.connections.' . $operator->node_connection . '.host');

        $url = 'http://' . $ip . '/api/black-lists/' . $customer->username;

        return Http::asForm()->get($url);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $username
     * @return \Illuminate\Http\Response
     */
    public function destroy($username)
    {
        if (!strlen($username)) {
            return 2;
        }

        $cache_store = config('local.radius_cache_store');

        config(['cache.prefix' => '']);

        if (Cache::store($cache_store)->has($username)) {

            $value = Cache::store($cache_store)->get($username, 'default');

            Cache::store($cache_store)->forget($username);

            return $value;
        } else {
            return 'Not Listed';
        }
    }
}
