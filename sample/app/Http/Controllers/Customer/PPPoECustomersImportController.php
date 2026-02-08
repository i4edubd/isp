<?php

namespace App\Http\Controllers\Customer;

use App\Events\ImportPppCustomersRequested;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MinimumConfigurationController;
use App\Models\billing_profile;
use App\Models\customer_import_request;
use App\Models\Freeradius\nas;
use App\Models\operator;
use Illuminate\Http\Request;
use RouterOS\Sohag\RouterosAPI;

class PPPoECustomersImportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $mgid = $request->user()->id;

        $requests = customer_import_request::where('mgid', $mgid)->get();

        return view('admins.group_admin.customer-import-requests', [
            'requests' => $requests,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $operator = $request->user();

        $operators = $operator->operators->where('role', '!=', 'manager');

        $routers = nas::where('mgid', $request->user()->id)->get();

        $billing_profiles = billing_profile::where('mgid', $request->user()->id)->get();

        return view('admins.group_admin.pppoe-customers-import-create', [
            'routers' => $routers,
            'operators' => $operators,
            'billing_profiles' => $billing_profiles,
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
            'nas_id' => 'required|integer',
            'operator_id' => 'required|integer',
            'billing_profile_id' => 'required|integer',
            'import_disabled_user' => 'required',
            'generate_bill' => 'required',
        ]);

        $router = nas::findOrFail($request->nas_id);

        $config  = [
            'host' => $router->nasname,
            'user' => $router->api_username,
            'pass' => $router->api_password,
            'port' => $router->api_port,
            'attempts' => 2
        ];

        $api = new RouterosAPI($config);

        if (!$api->connect($config['host'], $config['user'], $config['pass'])) {

            return redirect()->route('pppoe_customers_import.create')->with('error', 'Could not connect to the router!');
        }

        // check permission

        $user_rows = $api->getMktRows('user', ['name' => $config['user']]);

        $api_user = array_shift($user_rows);

        if ($api_user['group'] != 'full') {
            return redirect()->route('pppoe_customers_import.create')->with('error', 'API User Need Full Permission');
        }

        $duplicate_where = [
            ['operator_id', '=', $request->operator_id],
            ['nas_id', '=', $request->nas_id],
            ['date', '=', date(config('app.date_format'))],
        ];

        if (customer_import_request::where($duplicate_where)->count()) {
            return redirect()->route('pppoe_customers_import.index');
        }

        $customer_import_request = new customer_import_request();
        $customer_import_request->connection_type = 'PPPoE';
        $customer_import_request->import_disabled_user = $request->import_disabled_user;
        $customer_import_request->mgid = $request->user()->id;
        $customer_import_request->operator_id = $request->operator_id;
        $customer_import_request->nas_id = $request->nas_id;
        $customer_import_request->billing_profile_id = $request->billing_profile_id;
        $customer_import_request->generate_bill = $request->generate_bill;
        $customer_import_request->date = date(config('app.date_format'));
        $customer_import_request->save();

        //dispatch event
        ImportPppCustomersRequested::dispatch($customer_import_request);

        if (MinimumConfigurationController::hasPendingConfig($request->user())) {
            return redirect()->route('configuration.next', ['operator' => $request->user()->id]);
        } else {
            return redirect()->route('pppoe_customers_import.index')->with('success', 'Event Created Successfully');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, customer_import_request $pppoe_customers_import)
    {
        $request->validate([
            'status' => 'nullable|in:success,failed',
        ]);

        $reports = $pppoe_customers_import->reports;

        if ($request->filled('status')) {
            $status = $request->status;
            $reports = $reports->filter(function ($report) use ($status) {
                return $report->status == $status;
            });
        }

        return view('admins.group_admin.pppoe-customers-import-reports', [
            'reports' => $reports,
            'pppoe_customers_import' => $pppoe_customers_import,

        ]);
    }
}
