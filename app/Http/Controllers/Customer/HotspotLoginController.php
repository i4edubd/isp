<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\AllCustomerController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NasIdentifierController;
use App\Models\all_customer;
use App\Models\Freeradius\customer;
use App\Models\Freeradius\nas;
use App\Models\operator;
use App\Models\pgsql\pgsql_radacct_history;
use App\Models\temp_customer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HotspotLoginController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // <<validate request>>
        $request->validate([
            'operator_id' => 'numeric|nullable',
            'router_ip' => 'string|nullable',
            'system_identity' => 'required|string',
            'login_ip' => 'required|string',
            'login_mac_address' => 'required|string',
        ]);

        if ($request->filled('operator_id') && $request->filled('router_ip')) {
            $operator = operator::findOrFail($request->operator_id);
            $router_ip = $request->router_ip;
            $model = new nas();
            $model->setConnection($operator->node_connection);
            $router = $model->where('nasname', $router_ip)->firstOrFail();
        } else {
            //  <router_ip>>
            $router_ip = NasIdentifierController::getNasIpAddress($request->system_identity);
            if ($router_ip == 0) {
                abort(500, 'Bad Identity');
            }

            // <<operator: Use to set database connection>>
            $operator = NasIdentifierController::getOperator($request->system_identity);
            if (!$operator) {
                abort(500, 'Operator Not Found');
            }

            // << router : Use to set customer router property
            $router = NasIdentifierController::getRouter($request->system_identity);
            if (!$router) {
                abort(500, 'Router Not Found');
            }
        }

        // <<validate mobile>>
        $country_code = getCountryCode($operator->id);
        $mobile = validate_mobile($request->mobile, $country_code);

        if ($mobile == 0) {
            abort(500, 'Invalid Mobile Number');
        }

        // <<count customer in the requested radius server>>
        $model = new customer();
        $model->setConnection($operator->radius_db_connection);
        $mobile_count = $model->where('mobile', $mobile)->count();
        $mac_count = $model->where('username', $request->login_mac_address)->count();

        // << 0 0 |  Registerd with other radius server | Require Registration >>
        if ($mobile_count == 0 && $mac_count == 0) {
            $customer_count = 0;
        }

        // << 0 1 | Forgot mobile number | Show Profile >>
        if ($mobile_count == 0 && $mac_count == 1) {
            $customer_count = 1;
        }

        // << 1 0 | Lost or changed device | Require Mac Replace | Not Hotspot Customer>>
        if ($mobile_count == 1 && $mac_count == 0) {
            $customer_count = 10;
        }

        // << 1 1 | Found one or multiple customer | Consider only the device | Show profile >>
        if ($mobile_count == 1 && $mac_count == 1) {
            $customer_count = 11;
        }

        // <<case#1 Registered with other network || different radius server>>
        if ($customer_count == 0) {
            $central_info_count = all_customer::where('mobile', $mobile)->count();
            if ($central_info_count == 1) {
                $central_customer_info = all_customer::where('mobile', $mobile)->firstOrFail();
                $registered_operator = operator::findOrFail($central_customer_info->operator_id);

                $model = new customer();
                $model->setConnection($registered_operator->radius_db_connection);
                $customer = $model->findOrFail($central_customer_info->customer_id);
                $customer->link_login_only = $request->link_login_only;
                $customer->save();

                CustomerWebLoginController::startWebSession($central_customer_info);
                $request->session()->regenerate();
                return redirect()->route('customers.network-collision', ['new_network' => $operator->company]);
            }
        }

        // <<case#2 new registration>>
        if ($customer_count == 0) {
            //remove previous attempts
            temp_customer::where('mobile', $mobile)->delete();

            //add new attempt
            $tmp_customer = new temp_customer();
            $tmp_customer->mgid = $operator->mgid;
            $tmp_customer->gid = $operator->gid;
            $tmp_customer->operator_id = $operator->id;
            $tmp_customer->company = $operator->company;
            $tmp_customer->connection_type = 'Hotspot';
            $tmp_customer->billing_type = 'Daily';
            $tmp_customer->name = $request->mobile;
            $tmp_customer->mobile = $request->mobile;
            $tmp_customer->username = $request->login_mac_address;
            $tmp_customer->password = $request->login_mac_address;
            $tmp_customer->router_ip = $router_ip;
            $tmp_customer->router_id = $router->id;
            $tmp_customer->link_login_only = $request->link_login_only;
            $tmp_customer->link_logout = $request->link_logout;
            $tmp_customer->login_ip = $request->login_ip;
            $tmp_customer->login_mac_address = $request->login_mac_address;
            $tmp_customer->save();

            return redirect()->route('temp_customers.mobile-verification.create', ['temp_customer' => $tmp_customer]);
        }

        // <<case#3 Found By Only Mobile | Not hotspot customer Or Require MAC Change>>
        if ($customer_count == 10) {

            $model = new customer();
            $model->setConnection($operator->radius_db_connection);
            $customer = $model->where('mobile', $mobile)->first();

            $all_customer = all_customer::where('operator_id', $customer->operator_id)->where('customer_id', $customer->id)->firstOr(function () {
                abort(404, 'customer in all_customers not found');
            });

            // <<billed && !Hotspot customer>>
            if ($customer->connection_type !== 'Hotspot' && $customer->payment_status === 'billed') {
                CustomerWebLoginController::startWebSession($all_customer);
                $request->session()->regenerate();
                return redirect()->route('customers.bills');
            }

            // <<active && !Hotspot customer>>
            if ($customer->connection_type !== 'Hotspot' && $customer->status === 'active') {
                HotspotInternetLoginController::login($customer);
                CustomerWebLoginController::startWebSession($all_customer);
                $request->session()->regenerate();
                return redirect()->route('customers.home');
            }

            // <<Replace MAC Address >> Auto Login Failed>>
            if ($customer->connection_type === 'Hotspot' && $customer->username !== $request->login_mac_address) {
                // <<save new informations >> Will be Required to set the new MAC as new Username and everything else to display as customer info>>
                $customer->link_login_only = $request->link_login_only;
                $customer->link_logout = $request->link_logout;
                $customer->login_ip = $request->login_ip;
                $customer->login_mac_address = $request->login_mac_address;
                $customer->router_ip = $router_ip;
                $customer->router_id = $router->id;
                $customer->save();
                CustomerWebLoginController::startWebSession($all_customer);
                $request->session()->regenerate();
                return redirect()->route('customers.replace-mac-address.index');
            }
        }

        // <<found customer>>
        if ($customer_count == 1 || $customer_count == 11) {

            $model = new customer();
            $model->setConnection($operator->radius_db_connection);
            $customer = $model->where('username', $request->login_mac_address)->first();

            $all_customer = all_customer::where('operator_id', $customer->operator_id)->where('customer_id', $customer->id)->firstOr(function () {
                abort(404, 'customer in all_customers not found');
            });

            // update mobile number
            $existing_mobile = validate_mobile($customer->mobile, $country_code);
            if ($existing_mobile == 0) {
                $model = new customer();
                $model->setConnection($operator->radius_db_connection);
                if ($model->where('mobile', $mobile)->count() == 0) {
                    $customer->mobile = $mobile;
                    $customer->save();
                    AllCustomerController::updateOrCreate($customer);
                }
            }

            // <<billed && !Hotspot customer>>
            if ($customer->connection_type !== 'Hotspot' && $customer->payment_status === 'billed') {
                CustomerWebLoginController::startWebSession($all_customer);
                $request->session()->regenerate();
                return redirect()->route('customers.bills');
            }

            // <<active && !Hotspot customer>>
            if ($customer->connection_type !== 'Hotspot' && $customer->status === 'active') {
                HotspotInternetLoginController::login($customer);
                CustomerWebLoginController::startWebSession($all_customer);
                $request->session()->regenerate();
                return redirect()->route('customers.home');
            }

            // <<save new informations > router_ip will be used by system for force login user and everything else as user info >>
            $customer->link_login_only = $request->link_login_only;
            $customer->link_logout = $request->link_logout;
            $customer->login_ip = $request->login_ip;
            $customer->login_mac_address = $request->login_mac_address;
            $customer->router_ip = $router_ip;
            $customer->router_id = $router->id;
            $customer->save();

            // <<case#4 Customer is suspended>>

            // <<suspended due to Volume Limit>>
            if ($customer->total_octet_limit) {
                $pgsql_radacct_history = new pgsql_radacct_history();
                $pgsql_radacct_history->setConnection($operator->pgsql_connection);
                $download = $pgsql_radacct_history->where('username', '=', $customer->username)->sum('acctoutputoctets');
                $upload = $pgsql_radacct_history->where('username', '=', $customer->username)->sum('acctinputoctets');
                if (($download + $upload) > $customer->total_octet_limit) {
                    $customer->status = 'suspended';
                    $customer->suspend_reason = 'volume_limit_exceeds';
                    $customer->save();
                }
            }

            //  <<suspended due to Time Limit>>
            $expiration = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($customer->operator_id), 'en');
            $now = Carbon::now(getTimeZone($customer->operator_id));
            if ($expiration->lessThan($now)) {
                $customer->status = 'suspended';
                $customer->suspend_reason = 'time_limit_exceeds';
                $customer->save();
            }

            if ($customer->status === 'suspended') {
                if ($customer->texted_locked_status == 0) {
                    SmsMessagesForCustomerController::purchasePackage($customer);
                    $customer->texted_locked_status = 1;
                    $customer->save();
                }
                CustomerWebLoginController::startWebSession($all_customer);
                $request->session()->regenerate();
                return redirect()->route('customers.home')->with('warning', 'Account Status Suspended');
            }

            // <<case#5 Auto Login Failed>>
            HotspotInternetLoginController::login($customer);

            //show Profile
            CustomerWebLoginController::startWebSession($all_customer);
            $request->session()->regenerate();

            return redirect()->route('customers.home')->with('info', 'Account Status ' . $customer->status);
        }
    }
}
