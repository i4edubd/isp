<?php

namespace App\Console\Commands;

use App\Http\Controllers\CacheController;
use App\Http\Controllers\Customer\PPPoECustomersFramedIPAddressController;
use App\Http\Controllers\Customer\StaticIpCustomersFirewallController;
use App\Http\Controllers\PPPCustomerDisconnectController;
use App\Models\customer_bill;
use App\Models\Freeradius\customer;
use App\Models\Freeradius\radacct;
use App\Models\operator;
use App\Models\pgsql\pgsql_radacct_history;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AutomaticallySuspendCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:suspend_customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically Suspend Customers';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::channel('auto_suspend_customers')->debug('Auto suspend script started at: ' . Carbon::now());

        $madmins = operator::where('role', 'group_admin')->get();

        while ($madmin = $madmins->shift()) {

            $model = new customer();
            $model->setConnection($madmin->node_connection);
            $customers = $model->where('mgid', $madmin->id)->get();

            while ($customer = $customers->shift()) {

                // << Early Returns
                if ($customer->status === 'suspended') {
                    continue;
                }

                if ($customer->status === 'disabled') {
                    continue;
                }
                // >>

                // billing profile
                if ($customer->connection_type !== 'Hotspot') {

                    $billing_profile = CacheController::getBillingProfile($customer->billing_profile_id);

                    if (!$billing_profile) {
                        $info = 'Billing Profile Not Found! operator_id: ' .  $customer->operator_id . ' customer  id: ' . $customer->id;
                        Log::channel('auto_suspend_customers')->debug($info);
                        continue;
                    }

                    if ($billing_profile->auto_lock == 'no') {
                        Cache::remember('auto_lock_no_' . $billing_profile->id, 600, function () use ($customer) {
                            $info = 'auto lock = no ! operator_id: ' .  $customer->operator_id;
                            Log::channel('auto_suspend_customers')->debug($info);
                            return 0;
                        });
                        continue;
                    }
                }

                // <<suspended due to Volume Limit>>
                if ($customer->total_octet_limit) {

                    // radacct
                    $radacct = new radacct();
                    $radacct->setConnection($madmin->node_connection);
                    $download = $radacct->where('username', '=', $customer->username)->sum('acctoutputoctets');
                    $upload = $radacct->where('username', '=', $customer->username)->sum('acctinputoctets');

                    // radacct history
                    $radacct_history = new pgsql_radacct_history();
                    $radacct_history->setConnection($madmin->pgsql_connection);
                    $download_histroy = $radacct_history->where('username', '=', $customer->username)->sum('acctoutputoctets');
                    $upload_histroy = $radacct_history->where('username', '=', $customer->username)->sum('acctinputoctets');

                    $usage =  $download + $upload + $download_histroy + $upload_histroy;

                    if ($usage > $customer->total_octet_limit) {
                        $customer->status = 'suspended';
                        $customer->suspend_reason = 'volume_limit_exceeds';
                        $customer->save();
                    }
                }

                // <<suspended due to Time Limit>>
                // <<Don't be fooled with customer's bills. There is no bills for customers using daily basis billing or using package has validity less than 30 days>>
                try {
                    $expiration = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($customer->operator_id), 'en');
                } catch (\Throwable $th) {
                    Log::channel('auto_suspend_customers')->debug($th->getTraceAsString());
                    continue;
                }

                $now = Carbon::now(getTimeZone($customer->operator_id))->setHour(0)->setMinute(0)->setSecond(0);

                if ($expiration->lessThan($now)) {
                    if ($customer->connection_type === 'Hotspot') {
                        $customer->status = 'suspended';
                        $customer->suspend_reason = 'time_limit_exceeds';
                        $customer->save();
                    } else {
                        // Monthly Billing
                        if ($billing_profile->billing_type === 'Monthly') {

                            $package = CacheController::getPackage($customer->package_id);

                            if (!$package) {
                                $info = 'Package Not Found! operator_id: ' .  $customer->operator_id . ' customer  id: ' . $customer->id;
                                Log::channel('auto_suspend_customers')->debug($info);
                                continue;
                            }

                            $master_package = $package->master_package;

                            if ($master_package->validity != 30) {
                                $customer->status = 'suspended';
                                $customer->suspend_reason = 'time_limit_exceeds';
                                $customer->save();
                            } else {
                                $bill_where = [
                                    ['mgid', '=', $customer->mgid],
                                    ['customer_id', '=', $customer->id],
                                ];
                                if (customer_bill::where($bill_where)->whereNull('remark')->count()) {

                                    $customer->status = 'suspended';
                                    $customer->suspend_reason = 'payment_due';
                                    $customer->save();

                                    /*
                                    $bill = customer_bill::where($bill_where)->whereNull('remark')->first();

                                    try {
                                        $last_due_date = date_format(date_create($bill->due_date), config('app.date_format'));
                                        $last_payment_date = Carbon::createFromFormat(config('app.date_format'), $last_due_date, getTimeZone($customer->operator_id));
                                    } catch (\Throwable $th) {
                                        Log::channel('auto_suspend_customers')->debug($th->getTraceAsString());
                                        continue;
                                    }
                                    if ($last_payment_date->lessThan($now)) {
                                        $customer->status = 'suspended';
                                        $customer->suspend_reason = 'payment_due';
                                        $customer->save();
                                    }
                                    */
                                } else {
                                    $info = 'Bill Not Found! operator_id: ' .  $customer->operator_id . ' customer  id: ' . $customer->id;
                                    Log::channel('auto_suspend_customers')->debug($info);
                                }
                            }
                        }
                        // Daily Billing
                        if ($billing_profile->billing_type === 'Daily') {
                            $customer->status = 'suspended';
                            $customer->suspend_reason = 'time_limit_exceeds';
                            $customer->save();
                        }
                        // Free Customer >> Do Nothing
                    }
                }

                if ($customer->status == 'suspended') {
                    switch ($customer->connection_type) {
                        case 'PPPoE':
                            PPPoECustomersFramedIPAddressController::updateOrCreate($customer);
                            PPPCustomerDisconnectController::disconnect($customer);
                            break;
                        case 'StaticIp':
                            StaticIpCustomersFirewallController::updateOrCreate($customer);
                            break;
                    }
                }
            }
        }

        Log::channel('auto_suspend_customers')->debug('Auto suspend script stopped at: ' . Carbon::now());

        return 0;
    }
}
