<?php

namespace App\Http\Controllers;

use App\Models\ipv4address;
use App\Models\ipv4pool;
use App\Models\operator;
use Illuminate\Http\Request;

class SuspendedUsersPoolController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Models\operator  $operator
     * @return \App\Models\ipv4pool
     */
    public static function store(operator $operator)
    {
        //save ipv4pool for 203.0.113.0/24
        $ipv4pool = new ipv4pool();
        $ipv4pool->mgid = $operator->mgid;
        $ipv4pool->name = "suspended_users_pool";
        $ipv4pool->subnet = "3405804288";      // 203.0.113.0
        $ipv4pool->mask = "24";
        $ipv4pool->gateway = "3405804289";     // 203.0.113.1
        $ipv4pool->broadcast = "3405804543";   // 203.0.113.255
        $ipv4pool->save();

        //save network address
        $ipv4address = new ipv4address();
        $ipv4address->customer_id = 0;
        $ipv4address->operator_id = $operator->mgid;
        $ipv4address->ipv4pool_id = $ipv4pool->id;
        $ipv4address->ip_address = "3405804288"; // 203.0.113.0
        $ipv4address->description = 'Network Address';
        $ipv4address->save();

        //save gateway address
        $ipv4address = new ipv4address();
        $ipv4address->customer_id = 0;
        $ipv4address->operator_id = $operator->mgid;
        $ipv4address->ipv4pool_id = $ipv4pool->id;
        $ipv4address->ip_address = "3405804289"; // 203.0.113.1
        $ipv4address->is_gateway = 1;
        $ipv4address->description = 'Gateway Address';
        $ipv4address->save();

        //save broadcast address
        $ipv4address = new ipv4address();
        $ipv4address->customer_id = 0;
        $ipv4address->operator_id = $operator->mgid;
        $ipv4address->ipv4pool_id = $ipv4pool->id;
        $ipv4address->ip_address = "3405804543"; // 203.0.113.255
        $ipv4address->description = 'Broadcast Address';
        $ipv4address->save();

        return $ipv4pool;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\operator  $operator
     * @return \App\Models\ipv4pool
     */
    public function get(operator $operator)
    {
        $where = [
            ['mgid', '=', $operator->mgid],
            ['name', '=', 'suspended_users_pool'],
        ];

        return ipv4pool::where($where)->firstOr(function () use ($operator) {
            return self::store($operator);
        });
    }
}
