<?php

namespace App\Http\Controllers;

use App\Models\Freeradius\customer;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class OfflineCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $operator = $request->user();

        $request->validate([
            'operator_id' => 'nullable|numeric',
            'connection_type' => 'nullable|in:PPPoE,Hotspot,StaticIp',
            'status' => 'nullable|in:active,suspended,disabled',
            'payment_status' => 'nullable|in:billed,paid',
            'zone_id' => 'nullable|numeric',
            'sortby' => 'nullable|in:id,username,last_seen_timestamp',
        ]);

        $filter = [];

        // operator_id
        if ($request->filled('operator_id')) {
            $filter[] = ['operator_id', '=', $request->operator_id];
            $operator_id = $request->operator_id;
            $viewing_operator = CacheController::getOperator($operator_id);
            $this->authorize('view', [$viewing_operator]);
        } else {
            if ($operator->role == 'manager') {
                $filter[] = ['operator_id', '=', $operator->gid];
                $operator_id = $operator->gid;
            } else {
                $filter[] = ['operator_id', '=', $operator->id];
                $operator_id = $operator->id;
            }
        }

        $cache_key = 'offline_customers_' . $operator_id;

        $ttl = 300;

        if ($request->filled('refresh')) {
            if (Cache::has($cache_key)) {
                Cache::forget($cache_key);
            }
        }

        $customers = Cache::remember($cache_key, $ttl, function () use ($filter) {
            $customers = customer::with('radaccts')->where($filter)->get();
            return $customers->filter(function ($customer) {
                return $customer->radaccts->whereNull('acctstoptime')->count() == 0;
            });
        });

        if ($request->filled('connection_type')) {
            $connection_type = $request->connection_type;
            $customers = $customers->filter(function ($customer) use ($connection_type) {
                return $customer->connection_type == $connection_type;
            });
        }

        if ($request->filled('status')) {
            $status = $request->status;
            $customers = $customers->filter(function ($customer) use ($status) {
                return $customer->status == $status;
            });
        }

        if ($request->filled('payment_status')) {
            $payment_status = $request->payment_status;
            $customers = $customers->filter(function ($customer) use ($payment_status) {
                return $customer->payment_status == $payment_status;
            });
        }

        if ($request->filled('zone_id')) {
            $zone_id = $request->zone_id;
            $customers = $customers->filter(function ($customer) use ($zone_id) {
                return $customer->zone_id == $zone_id;
            });
        }

        if ($request->filled('device_id')) {
            $device_id = $request->device_id;
            $customers = $customers->filter(function ($customer) use ($device_id) {
                return $customer->device_id == $device_id;
            });
        }

        if ($request->filled('package_id')) {
            $package_id = $request->package_id;
            $customers = $customers->filter(function ($customer) use ($package_id) {
                return $customer->package_id == $package_id;
            });
        }

        if ($request->filled('username')) {
            $username = getUserName($request->username);
            $customers = $customers->filter(function ($customer) use ($username) {
                return false !== stristr($customer->username, $username);
            });
        }

        if ($request->filled('sortby')) {
            $sortby = $request->sortby;
            $customers = $customers->sortBy($sortby);
        }

        // default length
        $length = 50;

        if ($request->filled('length')) {
            $length = $request->length;
        }

        $current_page = $request->input("page") ?? 1;

        $view_customers = new LengthAwarePaginator($customers->forPage($current_page, $length), $customers->count(), $length, $current_page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.offline-customers', [
                    'customers' => $view_customers,
                    'length' => $length,
                ]);
                break;

            case 'operator':
                return view('admins.operator.offline-customers', [
                    'customers' => $view_customers,
                    'length' => $length,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.offline-customers', [
                    'customers' => $view_customers,
                    'length' => $length,
                ]);
                break;

            case 'manager':
                return view('admins.manager.offline-customers', [
                    'customers' => $view_customers,
                    'length' => $length,
                ]);
                break;
        }
    }
}
