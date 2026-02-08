<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\AllCustomerController;
use App\Http\Controllers\BillingProfileController;
use App\Http\Controllers\BlackListRemoveController;
use App\Http\Controllers\Cache\BillingProfileCacheController;
use App\Http\Controllers\Cache\CustomerZoneCacheController;
use App\Http\Controllers\Cache\DeviceCacheController;
use App\Http\Controllers\Cache\OperatorCacheController;
use App\Http\Controllers\Cache\PackageCacheController;
use App\Http\Controllers\CacheController;
use App\Http\Controllers\ChildCustomerAccountController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DisabledFilterController;
use App\Http\Controllers\PgsqlCustomerController;
use App\Http\Controllers\RrdGraphApiController;
use App\Models\all_customer;
use App\Models\billing_profile;
use App\Models\custom_field;
use App\Models\customer_bill;
use App\Models\customer_payment;
use App\Models\deleted_customer;
use App\Models\Freeradius\customer;
use App\Models\Freeradius\customer_custom_attribute;
use App\Models\Freeradius\nas;
use App\Models\Freeradius\radacct;
use App\Models\ipv4address;
use App\Models\package;
use App\Models\pgsql\pgsql_radacct_history;
use App\Models\pgsql_activity_log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CustomerController extends Controller
{

    /**
     * Display a listing of the resource.
     * For Filter customers of the same operator, we do not go to the database.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', customer::class);

        $request->validate([
            'connection_type' => 'nullable|in:PPPoE,Hotspot,StaticIp,Other',
            'billing_type' => 'nullable|in:Daily,Monthly,Free',
            'status' => 'nullable|in:active,suspended,disabled',
            'payment_status' => 'nullable|in:billed,paid',
            'zone_id' => 'nullable|numeric',
            'package_id' => 'nullable|numeric',
            'mac_bind' => 'nullable|numeric',
            'operator_id' => 'nullable|numeric',
            'length' => 'nullable|numeric',
            'sortby' => 'nullable|in:id,username,exptimestamp',
        ]);

        // requester
        $operator = $request->user();

        $filter = [];

        $ttl = 300;

        // default filter || mgid
        $filter[] = ['mgid', '=', $operator->mgid];

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

        $cache_key = CacheController::getCustomersListKey($operator_id);

        if ($request->filled('refresh')) {
            if (Cache::has($cache_key)) {
                Cache::forget($cache_key);
            }
        }

        $customers = Cache::remember($cache_key, $ttl, function () use ($filter, $operator_id) {
            $online_customers = radacct::where('operator_id', $operator_id)
                ->whereNull('acctstoptime')
                ->select('username')
                ->get();
            $customers = customer::where($filter)->get();
            $customers = $customers->mapWithKeys(function ($item, $key) use ($online_customers) {
                $item->is_online = $online_customers->where('username', $item->username)->count();
                return [$item->id => $item];
            });
            return $customers;
        });

        if ($request->filled('connection_type')) {
            $connection_type = $request->connection_type;
            $customers = $customers->filter(function ($customer) use ($connection_type) {
                return $customer->connection_type == $connection_type;
            });
        }

        if ($request->filled('billing_type')) {
            $billing_type = $request->billing_type;
            $customers = $customers->filter(function ($customer) use ($billing_type) {
                return $customer->billing_type == $billing_type;
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

        if ($request->filled('billing_profile_id')) {
            $billing_profile_id = $request->billing_profile_id;
            $customers = $customers->filter(function ($customer) use ($billing_profile_id) {
                return $customer->billing_profile_id == $billing_profile_id;
            });
        }

        if ($request->filled('will_be_suspended')) {
            $customers = $customers->filter(function ($customer) {
                return self::willBeSuspended($customer);
            });
        }

        if ($request->filled('ip')) {
            $login_ip = $request->ip;
            $customers = $customers->filter(function ($customer) use ($login_ip) {
                return $customer->login_ip == $login_ip;
            });
        }

        if ($request->filled('mac_bind')) {
            $mac_bind = $request->mac_bind;
            $customers = $customers->filter(function ($customer) use ($mac_bind) {
                return $customer->mac_bind == $mac_bind;
            });
        }

        if ($request->filled('advance_payment')) {
            if ($request->advance_payment == 0) {
                $customers = $customers->filter(function ($customer) {
                    return $customer->advance_payment == 0;
                });
            } else {
                $customers = $customers->filter(function ($customer) {
                    return $customer->advance_payment > 0;
                });
            }
        }

        if ($request->filled('year')) {
            $registration_year = $request->year;
            $customers = $customers->filter(function ($customer) use ($registration_year) {
                return $customer->registration_year == $registration_year;
            });
        }

        if ($request->filled('month')) {
            $registration_month = $request->month;
            $customers = $customers->filter(function ($customer) use ($registration_month) {
                return $customer->registration_month == $registration_month;
            });
        }

        if ($request->filled('username')) {
            $username = getUserName($request->username);
            $customers = $customers->filter(function ($customer) use ($username) {
                return false !== stristr($customer->username, $username);
            });
        }

        if ($request->filled('comment')) {
            $comment = $request->comment;
            $customers = $customers->filter(function ($customer) use ($comment) {
                return false !== stristr($customer->comment, $comment);
            });
        }

        if ($request->filled('sortby')) {
            $sortby = $request->sortby;
            $customers = $customers->sortBy($sortby);
        }

        // length
        $length = config('consumer.datatables_row_size');

        if ($request->filled('length')) {
            $length = $request->length;
        }

        $current_page = $request->input("page") ?? 1;

        $view_customers = new LengthAwarePaginator($customers->forPage($current_page, $length), $customers->count(), $length, $current_page, [
            'path' => $request->url(),
            'query' => $request->except('refresh'),
        ]);

        $devices = DeviceCacheController::getDevices(Auth::user());
        $zones = CustomerZoneCacheController::getCustomerZones(Auth::user());
        $all_packages =  PackageCacheController::getAllPackages(Auth::user());
        $billing_profiles = BillingProfileCacheController::getBillingProfiles(Auth::user());
        $operators = OperatorCacheController::getOperators(Auth::user());
        $filters =  DisabledFilterController::getFilters($request->user(), 'customer')->get('enabled');

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.customers', [
                    'customers' => $view_customers,
                    'length' => $length,
                    'devices' => $devices,
                    'zones' => $zones,
                    'all_packages' => $all_packages,
                    'billing_profiles' => $billing_profiles,
                    'operators' => $operators,
                    'filters' => $filters,
                ]);
                break;

            case 'operator':
                return view('admins.operator.customers', [
                    'customers' => $view_customers,
                    'length' => $length,
                    'devices' => $devices,
                    'zones' => $zones,
                    'all_packages' => $all_packages,
                    'billing_profiles' => $billing_profiles,
                    'operators' => $operators,
                    'filters' => $filters,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.customers', [
                    'customers' => $view_customers,
                    'length' => $length,
                    'devices' => $devices,
                    'zones' => $zones,
                    'all_packages' => $all_packages,
                    'billing_profiles' => $billing_profiles,
                    'operators' => $operators,
                    'filters' => $filters,
                ]);
                break;

            case 'manager':
                return view('admins.manager.customers', [
                    'customers' => $view_customers,
                    'length' => $length,
                    'devices' => $devices,
                    'zones' => $zones,
                    'all_packages' => $all_packages,
                    'billing_profiles' => $billing_profiles,
                    'operators' => $operators,
                    'filters' => $filters,
                ]);
                break;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function getRow(customer $customer)
    {
        return view('admins.components.customer-get-row', [
            'customer' => $customer,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function newCustomers(Request $request)
    {
        $operator = $request->user();

        $customers = customer::where('operator_id', $operator->id)->latest()->take(3)->get();

        return view('admins.components.new-customers', [
            'customers' => $customers,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, customer $customer)
    {

        $operator = $request->user();

        $seconds = 200;

        $radaccts_history_key = 'radaccts_history_' . $operator->id . '_' . $customer->id;

        $radaccts_history = Cache::remember($radaccts_history_key, $seconds, function () use ($customer) {
            return pgsql_radacct_history::where('username', $customer->username)->get();
        });

        $cache_key = 'customer_graph_' . $customer->operator_id . '_' . $customer->id;

        $seconds = 200;

        $graph = Cache::remember($cache_key, $seconds, function () use ($customer) {
            return RrdGraphApiController::getImage($customer);
        });

        if ($customer->payment_status == 'billed') {
            $bills = customer_bill::where('operator_id', $customer->operator_id)
                ->where('customer_id', $customer->id)
                ->get();
        } else {
            $bills = [];
        }

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.customers-show', [
                    'customer' => $customer,
                    'radaccts_history' => $radaccts_history,
                    'graph' => $graph,
                    'bills' => $bills,
                ]);
                break;

            case 'operator':
                return view('admins.operator.customers-show', [
                    'customer' => $customer,
                    'radaccts_history' => $radaccts_history,
                    'graph' => $graph,
                    'bills' => $bills,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.customers-show', [
                    'customer' => $customer,
                    'radaccts_history' => $radaccts_history,
                    'graph' => $graph,
                    'bills' => $bills,
                ]);
                break;

            case 'manager':
                return view('admins.manager.customers-show', [
                    'customer' => $customer,
                    'radaccts_history' => $radaccts_history,
                    'graph' => $graph,
                    'bills' => $bills,
                ]);
                break;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, customer $customer)
    {
        $this->authorize('update', $customer);

        if ($request->has('page')) {
            $page = $request->page;
        } else {
            $page = 1;
        }

        $operator = $request->user();

        if ($operator->role == 'manager') {
            $custom_fields = $operator->group_admin->custom_fields;
        } else {
            $custom_fields = $operator->custom_fields;
        }

        $collections = [];

        foreach ($custom_fields as $custom_field) {

            $value = '';

            $collection = [];
            $collection['id'] = $custom_field->id;
            $collection['operator_id'] = $custom_field->operator_id;
            $collection['name'] = $custom_field->name;

            $where = [
                ['customer_id', '=', $customer->id],
                ['custom_field_id', '=', $custom_field->id],
            ];
            if (customer_custom_attribute::where($where)->count()) {
                $value = customer_custom_attribute::where($where)->first()->value;
            }

            $collection['value'] = $value;
            $collections[] = custom_field::make($collection);
        }

        switch ($operator->role) {
            case 'group_admin':
                $customer_zones = $operator->customer_zones->sortBy('name');
                $devices = $operator->devices->sortBy('name');
                $billing_profiles = $operator->billing_profiles;
                $routers = nas::where('mgid', $operator->id)->get();
                return view('admins.group_admin.customers-edit', [
                    'customer' => $customer,
                    'customer_zones' => $customer_zones,
                    'devices' => $devices,
                    'billing_profiles' => $billing_profiles,
                    'routers' => $routers,
                    'page' => $page,
                    'custom_fields' => $collections,
                ]);
                break;

            case 'operator':
                $customer_zones = $operator->customer_zones->sortBy('name');
                $devices = $operator->devices->sortBy('name');
                $billing_profiles = $operator->billing_profiles;
                return view('admins.operator.customers-edit', [
                    'customer' => $customer,
                    'customer_zones' => $customer_zones,
                    'devices' => $devices,
                    'billing_profiles' => $billing_profiles,
                    'page' => $page,
                    'custom_fields' => $collections,
                ]);
                break;

            case 'sub_operator':
                $customer_zones = $operator->customer_zones->sortBy('name');
                $devices = $operator->devices->sortBy('name');
                $billing_profiles = $operator->billing_profiles;
                return view('admins.sub_operator.customers-edit', [
                    'customer' => $customer,
                    'customer_zones' => $customer_zones,
                    'devices' => $devices,
                    'billing_profiles' => $billing_profiles,
                    'page' => $page,
                    'custom_fields' => $collections,
                ]);
                break;

            case 'manager':
                $customer_zones = $operator->group_admin->customer_zones->sortBy('name');
                $devices = $operator->group_admin->devices->sortBy('name');
                $billing_profiles = $operator->group_admin->billing_profiles;
                return view('admins.manager.customers-edit', [
                    'customer' => $customer,
                    'customer_zones' => $customer_zones,
                    'devices' => $devices,
                    'billing_profiles' => $billing_profiles,
                    'page' => $page,
                    'custom_fields' => $collections,
                ]);
                break;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, customer $customer)
    {
        $this->authorize('update', $customer);

        $request->validate([
            'comment' => 'nullable|string|max:255',
        ]);

        $mobile = validate_mobile($request->mobile);

        //Invalid Mobile
        if ($mobile == 0) {
            return redirect()->route('customers.edit', ['customer' => $customer->id])->with('error', 'Invalid Mobile');
        }

        //Mobile Changed
        if ($customer->mobile !== $mobile) {
            $duplicate_mobile = all_customer::where('mobile', $mobile)->count();

            //Duplicate Mobile
            if ($duplicate_mobile) {
                return redirect()->route('customers.edit', ['customer' => $customer->id])->with('error', 'Duplicate Mobile');
            }
        }

        //update customer
        $customer->zone_id = $request->zone_id;
        $customer->device_id = $request->device_id;
        $customer->name = $request->name;
        $customer->mobile = $mobile;
        $customer->email = $request->email;
        $customer->login_mac_address = $request->login_mac_address;

        if ($request->filled('username')) {

            if (trim($request->username) !== getUserName($request->username)) {
                return redirect()->route('customers.edit', ['customer' => $customer->id])->with('error', 'Rejected: User-Name contains white space or Invalid characters');
            }

            if (trim($customer->username) !== trim($request->username)) {

                $duplicate = customer::where('username', trim($request->username))->count();

                if ($duplicate) {
                    return redirect()->route('customers.edit', ['customer' => $customer->id])->with('error', 'Duplicate Username');
                }

                $old_username = $customer->username;

                $customer->username = trim($request->username);
            }

            $customer->password = trim($request->password);
        }

        if ($request->filled('router_id')) {
            $customer->router_id = $request->router_id;
            $customer->login_ip = $request->login_ip;
        }

        $customer->house_no = $request->house_no;
        $customer->road_no = $request->road_no;
        $customer->thana = $request->thana;
        $customer->district = $request->district;
        $customer->invalid_username = 0;
        $customer->comment = $request->comment;
        $customer->save();

        //update Central Database
        AllCustomerController::updateOrCreate($customer);

        if ($customer->connection_type !== 'StaticIp') {
            if ($customer->wasChanged('username')) {
                PgsqlCustomerController::updateOrCreate($customer);
            }
            if ($customer->wasChanged('password')) {
                CustomersRadPasswordController::updateOrCreate($customer);
            }
        }

        if ($customer->connection_type === 'PPPoE') {
            if ($customer->wasChanged('username')) {
                PPPoECustomersUsernameUpdateController::update($customer, $old_username);
                BlackListRemoveController::update($customer);
            }
        }

        if ($customer->wasChanged('login_mac_address') && $customer->mac_bind == 1) {
            RadCallingStationIdController::updateOrCreate($customer);
        }

        // update bills
        $bills_where = [
            ['operator_id', '=', $customer->operator_id],
            ['customer_id', '=', $customer->id],
        ];
        customer_bill::where($bills_where)->update([
            'customer_zone_id' => $customer->zone_id,
            'mobile' => $customer->mobile,
            'name' => $customer->name,
        ]);

        //custom_attributes
        if ($request->user()->role == 'manager') {
            $custom_fields = $request->user()->group_admin->custom_fields;
        } else {
            $custom_fields = $request->user()->custom_fields;
        }
        if ($custom_fields->count()) {
            foreach ($custom_fields as $custom_field) {
                if ($request->filled($custom_field->id)) {
                    customer_custom_attribute::updateOrCreate(
                        ['customer_id' => $customer->id, 'custom_field_id' => $custom_field->id],
                        ['value' => $request->input($custom_field->id)]
                    );
                }
            }
        }

        if ($request->filled('page')) {
            $page = $request->page;
        } else {
            $page = 1;
        }
        //return customer's list
        return redirect()->route('customers.index', ['page' => $page])->with('success', 'The customer has been updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(customer $customer)
    {
        if (Auth::user()) {
            $this->authorize('delete', $customer);
            pgsql_activity_log::create([
                'gid' => Auth::user()->gid,
                'operator_id' => Auth::user()->id,
                'customer_id' => $customer->id,
                'topic' => 'destroy_customer',
                'year' => date(config('app.year_format')),
                'month' => date(config('app.month_format')),
                'week' => date(config('app.week_format')),
                'log' => Auth::user()->name . ' has deleted customer: ' . $customer->username,
            ]);
        }

        // deleted customer
        $operator = CacheController::getOperator($customer->operator_id);
        $deleted_customer = new deleted_customer();
        $deleted_customer->setConnection($operator->node_connection);
        $deleted_customer->mgid = $customer->mgid;
        $deleted_customer->gid = $customer->gid;
        $deleted_customer->operator_id = $customer->operator_id;
        $deleted_customer->connection_type = $customer->connection_type;
        $deleted_customer->zone_id = $customer->zone_id;
        $deleted_customer->device_id = $customer->device_id;
        $deleted_customer->name = $customer->name;
        $deleted_customer->mobile = $customer->mobile;
        $deleted_customer->email = $customer->email;
        $deleted_customer->billing_profile_id = $customer->billing_profile_id;
        $deleted_customer->username = $customer->username;
        $deleted_customer->password = $customer->password;
        $deleted_customer->package_id = $customer->package_id;
        $deleted_customer->package_name = $customer->package_name;
        $deleted_customer->package_started_at = $customer->package_started_at;
        $deleted_customer->package_expired_at = $customer->package_expired_at;
        $deleted_customer->status = $customer->status;
        $deleted_customer->house_no = $customer->house_no;
        $deleted_customer->road_no = $customer->road_no;
        $deleted_customer->thana = $customer->thana;
        $deleted_customer->district = $customer->district;
        $deleted_customer->save();

        $where = [
            ['operator_id', '=', $customer->operator_id],
            ['customer_id', '=', $customer->id],
        ];

        all_customer::where($where)->delete();

        customer_bill::where($where)->delete();

        customer_payment::where($where)->delete();

        ipv4address::where($where)->delete();

        PgsqlCustomerController::destroy($customer);

        ChildCustomerAccountController::makeChildsParentOnDeleteParent($customer);

        $customer->delete();

        if (url()->previous() == route('customers.index')) {
            $url = route('customers.index') . '?refresh=1';
        } else {
            $url = url()->previous() . '&refresh=1';
        }

        return redirect($url)->with('success', 'Customer Has been Deleted successfully');
    }


    /**
     * capable of being suspended?
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return boolean
     */
    public static function willBeSuspended(customer $customer)
    {
        // << Early Returns
        if ($customer->status === 'suspended') {
            return false;
        }

        if ($customer->status === 'disabled') {
            return false;
        }
        // >>

        // billing profile
        if ($customer->connection_type !== 'Hotspot') {

            $cache_key = 'billing_profile_' . $customer->billing_profile_id;

            $seconds = 200;

            $billing_profile = Cache::remember($cache_key, $seconds, function () use ($customer) {
                return billing_profile::find($customer->billing_profile_id);
            });

            if (!$billing_profile) {
                return false;
            }

            if ($billing_profile->auto_lock == 'no') {
                return false;
            }
        }

        // <<suspended due to Time Limit>>
        $expiration = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($customer->operator_id), 'en')->format(config('app.date_format'));
        $today = Carbon::now(getTimeZone($customer->operator_id))->format(config('app.date_format'));
        if ($expiration == $today) {
            if ($customer->connection_type === 'Hotspot') {
                return true;
            } else {
                // Monthly Billing
                if ($billing_profile->billing_type === 'Monthly') {

                    $cache_key = 'package_' . $customer->package_id;

                    $seconds = 200;

                    $package = Cache::remember($cache_key, $seconds, function () use ($customer) {
                        return package::find($customer->package_id);
                    });

                    if (!$package) {
                        return false;
                    }

                    if ($package->validity != 30) {
                        return true;
                    } else {
                        $bill_where = [
                            ['gid', '=', $customer->gid],
                            ['operator_id', '=', $customer->operator_id],
                            ['customer_id', '=', $customer->id],
                            ['year', '=', date(config('app.year_format'))],
                            ['month', '=', date(config('app.month_format'))]
                        ];
                        if (customer_bill::where($bill_where)->whereNull('remark')->count()) {
                            $bill = customer_bill::where($bill_where)->whereNull('remark')->first();
                            $last_due_date = date_format(date_create($bill->due_date), config('app.date_format'));
                            if ($last_due_date == $today) {
                                return true;
                            }
                        }
                    }
                }
                // Daily Billing
                if ($billing_profile->billing_type === 'Daily') {
                    return true;
                }
                // Free Customer >> Do Nothing
                return false;
            }
        }
    }
}
