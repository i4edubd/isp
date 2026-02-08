<?php

namespace App\Http\Controllers;

use App\Models\country;
use App\Models\Freeradius\customer;
use App\Models\language;
use App\Models\operator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class GroupAdminController extends Controller
{

    /**
     * Assign Database connection
     *
     * @return database connection name
     */
    public static function assignDatabaseConnection()
    {
        $connections = [];

        if (config()->has('database.nodes')) {

            $nodes = explode(",", config('database.nodes'));
        } else {

            abort(500);
        }

        foreach ($nodes as $node) {
            $model = new customer();
            $model->setConnection($node);
            $customer_count = $model->count();
            $connections[$node] = $customer_count;
        }

        asort($connections);

        return array_key_first($connections);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $where = [
            ['role', '=', 'group_admin'],
        ];


        if ($request->filled('subscription_type')) {
            $where[] = ['subscription_type', '=', $request->subscription_type];
        }

        if ($request->filled('status')) {
            $where[] = ['subscription_status', '=', $request->status];
        }

        $group_admins = operator::where($where)
            ->orderBy('provisioning_status', 'asc')
            ->get();

        return view('admins.super_admin.group-admins', [
            'group_admins' => $group_admins,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admins.super_admin.group-admins-create', [
            'countries' => country::all(),
            'languages' => language::all(),
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
            'country_id' => 'numeric|exists:countries,id|required',
            'lang_code' => 'string|exists:languages,code|required',
            'timezone' => 'string|exists:timezones,name|required',
            'company' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:operators',
            'password' => 'required|string|min:8',
        ]);

        $country = country::findOrFail($request->country_id);

        $mobile = validate_mobile($request->mobile, $country->iso2);

        $group_admin = new operator();
        $group_admin->sid = $request->user()->id;
        $group_admin->country_id = $country->id;
        $group_admin->timezone = $request->timezone;
        $group_admin->lang_code = $request->lang_code;
        $group_admin->name = $request->name;
        $group_admin->email = $request->email;
        $group_admin->email_verified_at = Carbon::now(config('app.timezone'));
        $group_admin->password = Hash::make($request->password);
        $group_admin->company = $request->company;
        $group_admin->radius_db_connection = $this->assignDatabaseConnection();
        $group_admin->mobile = $mobile;
        $group_admin->helpline = $mobile;
        $group_admin->role = 'group_admin';
        $group_admin->subscription_type = 'Paid';
        $group_admin->provisioning_status = 2;
        $group_admin->save();
        $group_admin->mgid = $group_admin->id;
        $group_admin->gid = $group_admin->id;
        $group_admin->save();

        // suspended_users_pool
        SuspendedUsersPoolController::store($group_admin);

        return redirect()->route('group_admins.index')->with('success', 'Group Admin Has been Created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\operator  $group_admin
     * @return \Illuminate\Http\Response
     */
    public function show(operator $group_admin)
    {
        $membership_time = Carbon::createFromFormat('Y-m-d H:i:s', $group_admin->created_at)->diffForHumans(Carbon::now());
        return view('admins.super_admin.group-admins-show', [
            'group_admin' => $group_admin,
            'membership_time' => $membership_time,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\operator  $group_admin
     * @return \Illuminate\Http\Response
     */
    public function edit(operator $group_admin)
    {
        return view('admins.super_admin.group-admins-edit', [
            'group_admin' => $group_admin,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\operator  $group_admin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, operator $group_admin)
    {
        $this->authorize('update', $group_admin);

        $request->validate([
            'email' => 'required|email',
        ]);

        $country = country::findOrFail($group_admin->country_id);

        $mobile = validate_mobile($request->mobile, $country->iso2);

        if (!$mobile) {
            return redirect()->route('group_admins.index')->with('error', 'Invalid Mobile Number!');
        }

        if ($group_admin->email !== $request->email) {

            $duplicate = operator::where('email', $request->email)->get()->count();

            if ($duplicate) {
                return redirect()->route('group_admins.index')->with('error', 'Duplicate Email');
            }

            $group_admin->email = $request->email;
        }

        $group_admin->company = $request->company;
        $group_admin->name = $request->name;
        $group_admin->mobile = $request->mobile;
        $group_admin->using_payment_gateway = $request->using_payment_gateway;
        if ($request->filled('password')) {
            $group_admin->password = Hash::make($request->password);
        }
        $group_admin->save();

        return redirect()->route('group_admins.index')->with('success', 'Group Admin updated successfully');
    }

    /**
     * Suspend Subscription
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function suspendSubscription(operator $operator)
    {
        $this->authorize('suspendSubscription', $operator);
        SubscriptionStatusController::suspend($operator);
        return redirect()->route('group_admins.index')->with('success', 'Subscription Suspened');
    }

    /**
     * Activate Subscription
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function activateSubscription(operator $operator)
    {
        $this->authorize('activateSubscription', $operator);
        SubscriptionStatusController::activate($operator);
        return redirect()->route('group_admins.index')->with('success', 'Subscription Activated');
    }
}
