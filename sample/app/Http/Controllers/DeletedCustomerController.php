<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Customer\HotspotCustomersRadAttributesController;
use App\Http\Controllers\Customer\PPPoECustomersRadAttributesController;
use App\Models\all_customer;
use App\Models\deleted_customer;
use App\Models\Freeradius\customer;
use App\Models\package;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class DeletedCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $request->validate([
            'connection_type' => 'nullable|in:PPPoE,Hotspot,StaticIp',
            'zone_id' => 'nullable|numeric',
            'package_id' => 'nullable|numeric',
            'operator_id' => 'nullable|numeric',
            'length' => 'nullable|numeric',
        ]);

        // requester
        $operator = $request->user();
        $filter = [];
        $cache_key = 'deleted_customers_';
        $ttl = 200;

        // default filter || mgid
        $filter[] = ['mgid', '=', $operator->mgid];

        // operator_id
        if ($request->filled('operator_id')) {
            $filter[] = ['operator_id', '=', $request->operator_id];
            $cache_key .= $request->operator_id;
        } else {
            if ($operator->role == 'manager') {
                $filter[] = ['operator_id', '=', $operator->gid];
                $cache_key .= $operator->gid;
            } else {
                $filter[] = ['operator_id', '=', $operator->id];
                $cache_key .= $operator->id;
            }
        }

        if ($request->filled('refresh')) {
            if (Cache::has($cache_key)) {
                Cache::forget($cache_key);
            }
        }

        $customers = Cache::remember($cache_key, $ttl, function () use ($filter) {
            return deleted_customer::where($filter)->get();
        });

        if ($request->filled('connection_type')) {
            $connection_type = $request->connection_type;
            $customers = $customers->filter(function ($customer) use ($connection_type) {
                return $customer->connection_type == $connection_type;
            });
        }

        if ($request->filled('zone_id')) {
            $zone_id = $request->zone_id;
            $customers = $customers->filter(function ($customer) use ($zone_id) {
                return $customer->zone_id == $zone_id;
            });
        }

        if ($request->filled('package_id')) {
            $package_id = $request->package_id;
            $customers = $customers->filter(function ($customer) use ($package_id) {
                return $customer->package_id == $package_id;
            });
        }

        if ($request->filled('billing_profile_id')) {
            $billing_profile_id = $request->billing_profile_id;
            $customers = $customers->filter(function ($customer) use ($billing_profile_id) {
                return $customer->billing_profile_id == $billing_profile_id;
            });
        }

        if ($request->filled('username')) {
            $username = getUserName($request->username);
            $customers = $customers->filter(function ($customer) use ($username) {
                return false !== stristr($customer->username, $username);
            });
        }

        $length = 50;

        if ($request->filled('length')) {
            $length = $request->length;
        }

        $current_page = $request->input("page") ?? 1;

        $view_customers = new LengthAwarePaginator($customers->forPage($current_page, $length), $customers->count(), $length, $current_page, [
            'path' => $request->url(),
            'query' => $request->except('refresh'),
        ]);

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.deleted-customers', [
                    'customers' => $view_customers,
                    'length' => $length,
                ]);
                break;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\deleted_customer  $deleted_customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(deleted_customer $deleted_customer)
    {

        $package =  package::where('id', $deleted_customer->package_id)->firstOr(function () {
            abort(500, 'Package Not Found');
        });
        $master_package = $package->master_package;

        $customer = new customer();
        //General Information
        $customer->house_no = $deleted_customer->house_no;
        $customer->road_no = $deleted_customer->road_no;
        $customer->thana = $deleted_customer->thana;
        $customer->district = $deleted_customer->district;
        //Registration timestamp
        $customer->registration_date = date(config('app.date_format'));
        $customer->registration_week = date(config('app.week_format'));
        $customer->registration_month = date(config('app.month_format'));
        $customer->registration_year = date(config('app.year_format'));
        //Import from deleted_customer
        $customer->mgid = $deleted_customer->mgid;
        $customer->gid = $deleted_customer->gid;
        $customer->operator_id = $deleted_customer->operator_id;
        $customer->connection_type = $deleted_customer->connection_type;
        $customer->zone_id = $deleted_customer->zone_id;
        $customer->device_id = $deleted_customer->device_id;
        $customer->name = $deleted_customer->name;
        $customer->mobile = $deleted_customer->mobile;
        $customer->email = $deleted_customer->email;
        $customer->billing_profile_id = $deleted_customer->billing_profile_id;
        $customer->username = trim($deleted_customer->username);
        $customer->password = trim($deleted_customer->password);
        $customer->package_id = $package->id;
        $customer->package_name = $package->name;
        $customer->package_started_at = $deleted_customer->package_started_at;
        $customer->package_expired_at = $deleted_customer->package_expired_at;
        $customer->exptimestamp =  Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($customer->operator_id), 'en')->timestamp;
        $customer->rate_limit = $master_package->rate_limit;
        $customer->total_octet_limit = $master_package->total_octet_limit;
        $customer->status = $deleted_customer->status;
        $customer->save();

        //Central customer information
        AllCustomerController::updateOrCreate($customer);

        //radcheck and radreply information
        if ($customer->connection_type == 'PPPoE') {
            PPPoECustomersRadAttributesController::updateOrCreate($customer);
        }

        if ($customer->connection_type == 'Hotspot') {
            HotspotCustomersRadAttributesController::updateOrCreate($customer);
        }

        $deleted_customer->delete();

        return redirect()->route('deleted_customers.index', ['refresh' => 1])->with('success', 'Customer restored successfully!');
    }
}
