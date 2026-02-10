<?php

namespace App\Http\Controllers;

use App\Models\fair_usage_policy;
use App\Models\Freeradius\customer;
use App\Models\ipv4address;
use App\Models\ipv4pool;
use App\Models\operator;
use App\Models\package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class Ipv4addressController extends Controller
{
    /**
     * Return and save the First Free IP Address of the IPv4pool
     *
     * @param \App\Models\Freeradius\customer $customer
     * @param  \App\Models\ipv4pool  $ipv4pool
     * @return int
     */
    public static function firstFree(customer $customer, ipv4pool $ipv4pool)
    {
        $ipv4addresses = ipv4address::where('ipv4pool_id', $ipv4pool->id)->get();

        if ($ipv4addresses->count()) {
            $first_free = ($ipv4pool->broadcast - $ipv4addresses->last()->ip_address) > 10 ? $ipv4addresses->last()->ip_address + 1 : $ipv4pool->gateway + 1;
        } else {
            $first_free = $ipv4pool->gateway + 1;
        }

        for ($i = $first_free; $i < $ipv4pool->broadcast; $i++) {

            if ($ipv4addresses->where('ip_address', $i)->count()) {
                continue;
            } else {
                //Create ipv4 address
                $ipv4address = new ipv4address();
                $ipv4address->customer_id = $customer->id;
                $ipv4address->operator_id = $customer->operator_id;
                $ipv4address->ipv4pool_id = $ipv4pool->id;
                $ipv4address->ip_address = $i;
                $ipv4address->save();
                return $i;
            }
        }

        // error
        return 0;
    }


    /**
     * Return the First Free IP Address of the IPv4pool
     *
     * @param \App\Models\Freeradius\customer $customer
     * @return int
     */
    public static function getCustomersIpv4Address(customer $customer)
    {

        // <<Free IPv4 Address from Suspended pool
        $key = 'suspended_users_pool_' . $customer->mgid;
        $suspended_pool = Cache::remember($key, 300, function () use ($customer) {
            return ipv4pool::where('mgid', $customer->mgid)->where('name', 'suspended_users_pool')->firstOr(function ()  use ($customer) {
                $madmin = operator::find($customer->mgid);
                $controller = new SuspendedUsersPoolController();
                return $controller->get($madmin);
            });
        });

        if ($customer->status !== 'suspended') {
            $where = [
                ['customer_id', '=', $customer->id],
                ['ipv4pool_id', '=', $suspended_pool->id],
            ];
            ipv4address::where($where)->delete();
        }
        // Free IPv4 Address from Suspended pool>>

        switch ($customer->status) {
            case 'suspended':
                // DELETE & UPDATE (Done in Free IPv4 Address from Suspended pool)
                // READ
                $where_exists = [
                    ['customer_id', '=', $customer->id],
                    ['ipv4pool_id', '=', $suspended_pool->id],
                ];
                if (ipv4address::where($where_exists)->count()) {
                    $ipv4address = ipv4address::where($where_exists)->first();
                    return $ipv4address->ip_address;
                }
                // CREATE
                return self::firstFree($customer, $suspended_pool);
                break;
            case 'fup':
                // identify pool or return error code
                $package = package::find($customer->package_id);
                $master_package = $package->master_package;
                $fair_usage_policy = fair_usage_policy::where('master_package_id', $master_package->id)->firstOr(function () {
                    return fair_usage_policy::make([
                        'id' => 0,
                        'ipv4pool_id' => 0,
                    ]);
                });
                if ($fair_usage_policy->ipv4pool_id > 0) {
                    $fup_pool = ipv4pool::findOrFail($fair_usage_policy->ipv4pool_id);
                } else {
                    return 0;
                }
                // DELETE & UPDATE (delete previous, update new)
                if ($customer->fup_pool_id !== $fup_pool->id) {
                    $delete_where = [
                        ['customer_id', '=', $customer->id],
                        ['ipv4pool_id', '=', $customer->fup_pool_id],
                    ];
                    ipv4address::where($delete_where)->delete();
                    $customer->fup_pool_id = $fup_pool->id;
                    $customer->save();
                }
                // READ
                $where_exists = [
                    ['customer_id', '=', $customer->id],
                    ['ipv4pool_id', '=', $fup_pool->id],
                ];
                if (ipv4address::where($where_exists)->count()) {
                    $ipv4address = ipv4address::where($where_exists)->first();
                    return $ipv4address->ip_address;
                }
                // CREATE
                return self::firstFree($customer, $fup_pool);

            default:
                // identify pool or return error code
                $package = package::with('master_package.pppoe_profile')->where('id', $customer->package_id)->firstOr(function () {
                    return 0;
                });

                // error
                if (!$package) {
                    return 0;
                }

                // pool error
                if (!$package->master_package->pppoe_profile->ipv4pool->id) {
                    $profile_name = $package->master_package->pppoe_profile->name;
                    $profile_id = $package->master_package->pppoe_profile->id;
                    $error = "IPv4 Pool for profile $profile_name not found. Please change the IPv4 pool of the profile of id $profile_id";
                    abort(500, $error);
                }

                $ipv4pool = $package->master_package->pppoe_profile->ipv4pool;

                // DELETE & UPDATE (delete previous, update new|previously active pool is not equal to current pool)
                if ($customer->package_pool_id !== $ipv4pool->id) {
                    $delete_where = [
                        ['customer_id', '=', $customer->id],
                        ['ipv4pool_id', '=', $customer->package_pool_id],
                    ];
                    ipv4address::where($delete_where)->delete();
                    $customer->package_pool_id = $ipv4pool->id;
                    $customer->save();
                }
                // READ
                $where_exists = [
                    ['customer_id', '=', $customer->id],
                    ['ipv4pool_id', '=', $ipv4pool->id],
                ];
                if (ipv4address::where($where_exists)->count() == 1) {
                    $ipv4address = ipv4address::where($where_exists)->first();
                    return $ipv4address->ip_address;
                }
                // CREATE
                $first_free = self::firstFree($customer, $ipv4pool);
                $is_unique = [
                    ['ipv4pool_id', '=', $ipv4pool->id],
                    ['ip_address', '=', $first_free],
                ];
                if (ipv4address::where($is_unique)->count() == 1) {
                    return $first_free;
                } else {
                    $delete_where = [
                        ['customer_id', '=', $customer->id],
                        ['ipv4pool_id', '=', $ipv4pool->id],
                        ['ip_address', '=', $first_free],
                    ];
                    ipv4address::where($delete_where)->delete();
                    return self::firstFree($customer, $ipv4pool);
                }
                break;
        }
    }
}
