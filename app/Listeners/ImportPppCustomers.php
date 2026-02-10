<?php

namespace App\Listeners;

use App\Events\ImportPppCustomersRequested;
use App\Http\Controllers\AllCustomerController;
use App\Http\Controllers\Customer\PPPoECustomersRadAttributesController;
use App\Http\Controllers\Ipv4poolController;
use App\Http\Controllers\Mikrotik\MikrotikDbSyncController;
use App\Http\Controllers\RouterToRadiusController;
use App\Models\all_customer;
use App\Models\billing_profile;
use App\Models\customer_import_report;
use App\Models\customer_zone;
use App\Models\Freeradius\customer;
use App\Models\Freeradius\nas;
use App\Models\ipv4address;
use App\Models\ipv4pool;
use App\Models\master_package;
use App\Models\Mikrotik\mikrotik_ip_pool;
use App\Models\Mikrotik\mikrotik_ppp_profile;
use App\Models\Mikrotik\mikrotik_ppp_secret;
use App\Models\operator;
use App\Models\package;
use App\Models\pppoe_profile;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Net_IPv4;

class ImportPppCustomers implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The name of the connection the job should be sent to.
     *
     * @var string|null
     */
    public $connection = 'database';

    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'import_ppp_customers';

    /**
     * The time (seconds) before the job should be processed.
     *
     * @var int
     */
    public $delay = 10;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ImportPppCustomersRequested  $event
     * @return void
     */
    public function handle(ImportPppCustomersRequested $event)
    {
        // << Import Resources from Mikrotik to Database
        MikrotikDbSyncController::sync($event->customer_import_request);
        // >>

        // << Required Variables
        $request = $event->customer_import_request;

        $group_admin = operator::find($request->mgid);

        $operator = operator::find($request->operator_id);

        $generate_bill = $request->generate_bill;

        $model = new nas();
        $model->setConnection($group_admin->radius_db_connection);
        $router = $model->find($request->nas_id);

        $package_started_at = Carbon::now(getTimeZone($operator->id))->isoFormat(config('app.expiry_time_format'));

        $billing_profile = billing_profile::find($request->billing_profile_id);

        if ($generate_bill == 'yes') {
            $package_expired_at = Carbon::createFromFormat(config('app.date_format'), $billing_profile->payment_date, getTimeZone($operator->id))->isoFormat(config('app.expiry_time_format'));
        } else {
            $package_expired_at = Carbon::createFromFormat(config('app.date_format'), $billing_profile->next_payment_date, getTimeZone($operator->id))->isoFormat(config('app.expiry_time_format'));
        }
        // >>

        // << Import ipv4pools
        $ipv4lib = new Net_IPv4();

        $mikrotik_ip_pools = mikrotik_ip_pool::where('customer_import_request_id', $request->id)->orderBy('ranges', 'asc')->get();

        foreach ($mikrotik_ip_pools as $mikrotik_ip_pool) {

            $net = $ipv4lib->parseAddress($mikrotik_ip_pool->ranges);
            $network = $net->network;
            $broadcast = $net->broadcast;
            $bitmask = $net->bitmask;

            $overlapped = Ipv4poolController::isOverlapped($group_admin, $network, $broadcast);

            $ipv4pool = 0;

            if ($overlapped == 0) {

                //save ipv4pool
                $ipv4pool = new ipv4pool();
                $ipv4pool->mgid = $group_admin->mgid;
                $ipv4pool->name = $mikrotik_ip_pool->name;
                $ipv4pool->subnet =  $ipv4lib->ip2double($network);
                $ipv4pool->mask = $bitmask;
                $ipv4pool->gateway = $ipv4lib->ip2double($network) + 1;
                $ipv4pool->broadcast = $ipv4lib->ip2double($broadcast);
                $ipv4pool->save();

                //save network address
                $ipv4address = new ipv4address();
                $ipv4address->customer_id = 0;
                $ipv4address->operator_id = $group_admin->id;
                $ipv4address->ipv4pool_id = $ipv4pool->id;
                $ipv4address->ip_address = $ipv4lib->ip2double($network);
                $ipv4address->description = 'Network Address';
                $ipv4address->save();

                //save gateway address
                $ipv4address = new ipv4address();
                $ipv4address->customer_id = 0;
                $ipv4address->operator_id = $group_admin->id;
                $ipv4address->ipv4pool_id = $ipv4pool->id;
                $ipv4address->ip_address = $ipv4lib->ip2double($network) + 1;
                $ipv4address->is_gateway = 1;
                $ipv4address->description = 'Gateway Address';
                $ipv4address->save();

                //save broadcast address
                $ipv4address = new ipv4address();
                $ipv4address->customer_id = 0;
                $ipv4address->operator_id = $group_admin->id;
                $ipv4address->ipv4pool_id = $ipv4pool->id;
                $ipv4address->ip_address = $ipv4lib->ip2double($broadcast);
                $ipv4address->description = 'Broadcast Address';
                $ipv4address->save();
            } else {

                $net = $ipv4lib->parseAddress($overlapped);
                $subnet = $ipv4lib->ip2double($net->network);

                $ipv4pool = ipv4pool::where('mgid', $group_admin->id)
                    ->where('subnet', $subnet)
                    ->first();
            }

            if ($ipv4pool) {

                $mikrotik_ip_pool->ipv4pool_id = $ipv4pool->id;
                $mikrotik_ip_pool->save();

                // success report
                $customer_import_report = new customer_import_report();
                $customer_import_report->request_id = $request->id;
                $customer_import_report->mgid = $request->mgid;
                $customer_import_report->operator_id = $request->operator_id;
                $customer_import_report->nas_id = $request->nas_id;
                $customer_import_report->menu = 'ipv4pool';
                $customer_import_report->name = $ipv4pool->name;
                $customer_import_report->status = 'success';
                $customer_import_report->save();
            } else {
                //faild report
                $customer_import_report = new customer_import_report();
                $customer_import_report->request_id = $request->id;
                $customer_import_report->mgid = $request->mgid;
                $customer_import_report->operator_id = $request->operator_id;
                $customer_import_report->nas_id = $request->nas_id;
                $customer_import_report->menu = 'ipv4pool';
                $customer_import_report->name = $mikrotik_ip_pool->name;
                $customer_import_report->status = 'failed';
                $customer_import_report->save();
            }
        }
        // Import ipv4pools Done! >>

        // << Import ppp profiles and Create Packages
        $mikrotik_ppp_profiles = mikrotik_ppp_profile::where('customer_import_request_id', $request->id)->get();

        foreach ($mikrotik_ppp_profiles as $mikrotik_ppp_profile) {

            $pool_where = [
                ['customer_import_request_id', '=', $request->id],
                ['name', '=', $mikrotik_ppp_profile->remote_address],
            ];

            $mikrotik_ip_pool = mikrotik_ip_pool::where($pool_where)->first();

            if ($mikrotik_ip_pool && $mikrotik_ip_pool->ipv4pool_id > 0) {

                $new_profile_where = [
                    ['mgid', '=', $group_admin->id],
                    ['ipv4pool_id', '=', $mikrotik_ip_pool->ipv4pool_id],
                ];

                if (pppoe_profile::where($new_profile_where)->count()) {
                    $pppoe_profile = pppoe_profile::where($new_profile_where)->first();
                } else {
                    $pppoe_profile = new pppoe_profile();
                    $pppoe_profile->mgid = $group_admin->id;
                    $pppoe_profile->name = $mikrotik_ppp_profile->name;
                    $pppoe_profile->ipv4pool_id = $mikrotik_ip_pool->ipv4pool_id;
                    $pppoe_profile->ip_allocation_mode = 'static';
                    $pppoe_profile->save();
                }

                $master_package_where = [
                    ['mgid', '=', $group_admin->id],
                    ['pppoe_profile_id', '=', $pppoe_profile->id],
                ];

                if (master_package::where($master_package_where)->count()) {
                    $master_package = master_package::where($master_package_where)->first();

                    $package_where = [
                        ['mpid', '=', $master_package->id],
                        ['operator_id', '=', $operator->id],
                    ];

                    if (package::where($package_where)->count()) {
                        $package = package::where($package_where)->first();
                    } else {
                        $package = self::assignPackage($operator, $master_package);
                    }
                } else {
                    // << build master_package
                    $master_package = new master_package();
                    $master_package->mgid = $group_admin->id;
                    $master_package->pppoe_profile_id = $pppoe_profile->id;
                    $master_package->connection_type = 'PPPoE';
                    $master_package->name = $pppoe_profile->name;
                    $master_package->save();

                    $package = self::assignPackage($operator, $master_package);
                }

                $mikrotik_ppp_profile->pppoe_profile_id = $pppoe_profile->id;
                $mikrotik_ppp_profile->package_id = $package->id;
                $mikrotik_ppp_profile->save();

                // success report
                $customer_import_report = new customer_import_report();
                $customer_import_report->request_id = $request->id;
                $customer_import_report->mgid = $request->mgid;
                $customer_import_report->operator_id = $request->operator_id;
                $customer_import_report->nas_id = $request->nas_id;
                $customer_import_report->menu = 'pppoe_profile';
                $customer_import_report->name = $pppoe_profile->name;
                $customer_import_report->status = 'success';
                $customer_import_report->save();
            } else {

                //faild report
                $customer_import_report = new customer_import_report();
                $customer_import_report->request_id = $request->id;
                $customer_import_report->mgid = $request->mgid;
                $customer_import_report->operator_id = $request->operator_id;
                $customer_import_report->nas_id = $request->nas_id;
                $customer_import_report->menu = 'pppoe_profile';
                $customer_import_report->name = $mikrotik_ppp_profile->name;
                $customer_import_report->status = 'failed';
                $customer_import_report->comment = 'IPv4 pool missing';
                $customer_import_report->save();
            }
        }
        // Import ppp profiles and Create Packages Done >>

        // << import customers
        $mikrotik_ppp_secrets = mikrotik_ppp_secret::where('customer_import_request_id', $request->id)->get();

        foreach ($mikrotik_ppp_secrets as $mikrotik_ppp_secret) {

            // User Name Sanitization
            if (trim($mikrotik_ppp_secret->name) !== getUserName($mikrotik_ppp_secret->name)) {
                //faild report
                $customer_import_report = new customer_import_report();
                $customer_import_report->request_id = $request->id;
                $customer_import_report->mgid = $request->mgid;
                $customer_import_report->operator_id = $request->operator_id;
                $customer_import_report->nas_id = $request->nas_id;
                $customer_import_report->menu = 'customer';
                $customer_import_report->name = trim($mikrotik_ppp_secret->name);
                $customer_import_report->status = 'failed';
                $customer_import_report->comment = 'User-Name contains white space or Invalid characters';
                $customer_import_report->save();
                continue;
            }

            //check duplicate username
            $model = new customer();
            $model->setConnection($group_admin->radius_db_connection);

            if ($model->where('username', $mikrotik_ppp_secret->name)->count()) {
                //faild report
                $customer_import_report = new customer_import_report();
                $customer_import_report->request_id = $request->id;
                $customer_import_report->mgid = $request->mgid;
                $customer_import_report->operator_id = $request->operator_id;
                $customer_import_report->nas_id = $request->nas_id;
                $customer_import_report->menu = 'customer';
                $customer_import_report->name = $mikrotik_ppp_secret->name;
                $customer_import_report->status = 'failed';
                $customer_import_report->comment = 'Duplicate Username';
                $customer_import_report->save();
                continue;
            }

            $mikrotik_ppp_profile_where = [
                ['customer_import_request_id', '=', $request->id],
                ['name', '=', $mikrotik_ppp_secret->profile],
            ];

            $ppp_profile = mikrotik_ppp_profile::where($mikrotik_ppp_profile_where)->first();

            if ($ppp_profile && $ppp_profile->package_id > 0) {

                $package = package::find($ppp_profile->package_id);

                try {
                    $customer = new customer();
                    $customer->setConnection($group_admin->radius_db_connection);
                    $customer->mgid = $operator->mgid;
                    $customer->gid = $operator->gid;
                    $customer->operator_id = $operator->id;
                    $customer->company = $operator->company;
                    $customer->connection_type = 'PPPoE';
                    $customer->billing_type = $billing_profile->billing_type;
                    $customer->name = trim($mikrotik_ppp_secret->name);
                    $customer->mobile = null;
                    $customer->billing_profile_id = $request->billing_profile_id;
                    $customer->username = $mikrotik_ppp_secret->name;
                    $customer->password = trim($mikrotik_ppp_secret->password);
                    $customer->package_id = $package->id;
                    $customer->package_name = $package->name;
                    $customer->package_started_at = $package_started_at;
                    $customer->package_expired_at = $package_expired_at;
                    $customer->exptimestamp = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($customer->operator_id), 'en')->timestamp;
                    $customer->advance_payment = 0;
                    $customer->payment_status = 'paid';
                    $customer->status = 'active';
                    $customer->comment = $mikrotik_ppp_secret->comment;
                    $customer->registration_date = date(config('app.date_format'));
                    $customer->registration_week = date(config('app.week_format'));
                    $customer->registration_month = date(config('app.month_format'));
                    $customer->registration_year = date(config('app.year_format'));
                    $customer->save();
                    $customer->parent_id = $customer->id;
                    $customer->save();
                } catch (\Throwable $th) {
                    Log::channel('debug')->debug($th->getTraceAsString());
                    continue;
                }

                // process Comment
                $mkt_comment = Str::remove('"', $mikrotik_ppp_secret->comment);
                $mkt_comment_array = explode(',', $mkt_comment);
                if (count($mkt_comment_array) == 8) {
                    $backup = [];
                    foreach ($mkt_comment_array as $backup_statement) {
                        $key_value_array =  explode("--", $backup_statement);
                        if (count($key_value_array) == 2) {
                            $backup[trim($key_value_array['0'])] = trim($key_value_array['1']);
                        }
                    }

                    // restore mobile
                    if (array_key_exists('mobile', $backup)) {
                        $backup_mobile = validate_mobile($backup['mobile'], getCountryCode($group_admin->id));
                        if ($backup_mobile) {
                            if (all_customer::where('mobile', $backup_mobile)->count() == 0) {
                                $customer->mobile = $backup_mobile;
                                $customer->save();
                            }
                        }
                    }

                    // restore exp_date
                    if (array_key_exists('exp_date', $backup)) {
                        $customer->package_expired_at = $backup['exp_date'];
                        $customer->exptimestamp = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($customer->operator_id), 'en')->timestamp;
                        $customer->save();
                    }

                    //restore status
                    if (array_key_exists('status', $backup)) {
                        $customer->status = $backup['status'];
                        $customer->save();
                    }

                    //restore name
                    if (array_key_exists('name', $backup)) {
                        $customer->name = $backup['name'];
                        $customer->save();
                    }

                    // restore operator_id
                    if (array_key_exists('oid', $backup)) {
                        $backup_op = operator::find($backup['oid']);
                        if ($backup_op) {
                            if ($backup_op->mgid == $group_admin->id) {
                                $customer->operator_id = $backup['oid'];
                                $customer->save();
                            }
                        }
                    }

                    //restore  zone_id
                    if (array_key_exists('zid', $backup)) {
                        if (strlen($backup['zid'])) {
                            $customer_zone = customer_zone::find($backup['zid']);
                            if ($customer_zone) {
                                $customer->zone_id = $customer_zone->id;
                                $customer->save();
                            }
                        }
                    }

                    // restore billing_profile_id
                    if (array_key_exists('bpid', $backup)) {
                        if (strlen($backup['bpid'])) {
                            $billing_profile = billing_profile::find($backup['bpid']);
                            if ($billing_profile) {
                                $customer->billing_profile_id = $billing_profile->id;
                                $customer->save();
                            }
                        }
                    }
                }

                //Central customer information
                AllCustomerController::updateOrCreate($customer);

                PPPoECustomersRadAttributesController::updateOrCreate($customer);

                // success report
                $customer_import_report = new customer_import_report();
                $customer_import_report->request_id = $request->id;
                $customer_import_report->mgid = $request->mgid;
                $customer_import_report->operator_id = $request->operator_id;
                $customer_import_report->nas_id = $request->nas_id;
                $customer_import_report->menu = 'customer';
                $customer_import_report->name = $customer->name;
                $customer_import_report->status = 'success';
                $customer_import_report->save();
            } else {

                //faild report
                $customer_import_report = new customer_import_report();
                $customer_import_report->request_id = $request->id;
                $customer_import_report->mgid = $request->mgid;
                $customer_import_report->operator_id = $request->operator_id;
                $customer_import_report->nas_id = $request->nas_id;
                $customer_import_report->menu = 'customer';
                $customer_import_report->name = $mikrotik_ppp_secret->name;
                $customer_import_report->status = 'failed';
                $customer_import_report->comment = 'Package Not Found';
                $customer_import_report->save();
            }
        }
        // import customers done ! >>

        // << Transfer Customers from Router to Radius
        $reports_where = [
            ['request_id', '=', $request->id],
            ['menu', '=', 'customer'],
            ['status', '=', 'failed'],
        ];

        $failed_count = customer_import_report::where($reports_where)->count();

        if ($failed_count < 10) {
            $customers_where = [
                ['operator_id', '=', $request->operator_id],
            ];

            $model = new customer();
            $model->setConnection($group_admin->radius_db_connection);
            $customers = $model->where($customers_where)->get();
            while ($customer = $customers->shift()) {
                $today = date(config('app.date_format'));
                $reg_date = date_format(date_create($customer->created_at), config('app.date_format'));
                if ($reg_date == $today) {
                    RouterToRadiusController::transfer($router, $customer);
                }
            }
        }
        // >>

        // do not generate bill | package price not set yet
        if ($generate_bill == 'yes') {
        }

        $request->status = 'done';
        $request->save();
    }


    /**
     * Get Package
     *
     * @param  \App\Models\operator $operator
     * @param  \App\Models\master_package  $master_package
     * @return \App\Models\package
     */
    public static function assignPackage(operator $operator, master_package $master_package)
    {
        if ($operator->role == 'operator' || $operator->role == 'group_admin') {
            $package = new package();
            $package->mgid = $operator->mgid;
            $package->gid = $operator->gid;
            $package->operator_id = $operator->id;
            $package->mpid = $master_package->id;
            $package->name = $master_package->name;
            $package->price = 1;
            $package->save();
            $package->ppid = $package->id;
            $package->save();
            return $package;
        }

        if ($operator->role == 'sub_operator') {
            $gadmin = operator::find($operator->gid);
            $package = new package();
            $package->mgid = $gadmin->mgid;
            $package->gid = $gadmin->gid;
            $package->operator_id = $gadmin->id;
            $package->mpid = $master_package->id;
            $package->name = $master_package->name;
            $package->price = 1;
            $package->save();
            $package->ppid = $package->id;
            $package->save();

            $sub_package = new package();
            $sub_package->mgid = $operator->mgid;
            $sub_package->gid = $operator->gid;
            $sub_package->operator_id = $operator->id;
            $sub_package->mpid = $master_package->id;
            $sub_package->ppid = $package->id;
            $sub_package->name = $master_package->name;
            $sub_package->price = 1;
            $sub_package->save();
            return $sub_package;
        }
    }
}
