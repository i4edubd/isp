<?php

namespace App\Console\Commands;

use App\Models\account;
use App\Models\all_customer;
use App\Models\backup_setting;
use App\Models\billing_profile;
use App\Models\billing_profile_operator;
use App\Models\custom_price;
use App\Models\customer_bill;
use App\Models\customer_payment;
use App\Models\Freeradius\customer;
use App\Models\Freeradius\radacct;
use App\Models\ipv4address;
use App\Models\ipv4pool;
use App\Models\ipv6pool;
use App\Models\master_package;
use App\Models\operator;
use App\Models\operator_permission;
use App\Models\package;
use App\Models\pgsql\pgsql_customer;
use App\Models\pgsql\pgsql_radcheck;
use App\Models\pgsql\pgsql_radreply;
use App\Models\pppoe_profile;
use App\Models\recharge_card;
use Illuminate\Console\Command;

class ConvertToMasterAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convert:ToMasterAdmin {operator_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert Operator To MasterAdmin';

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
        $operator_id = $this->argument('operator_id');

        $operator = operator::findOrFail($operator_id);

        if ($operator->role !== 'operator') {
            $this->info("Only operator can be converted!");
            return 0;
        }

        $sub_operator_where = [
            ['gid', '=', $operator->id],
            ['role', '=', 'sub_operator'],
        ];

        if (operator::where($sub_operator_where)->count()) {
            $this->info("The operator has sub operator can not be converted!");
            return 0;
        }

        $info = "operator id: " . $operator->id . " Email: " . $operator->email . " Name: " . $operator->name;

        $this->info($info);

        if (!$this->confirm('Do you wish to continue?', true)) {
            return 0;
        }

        // accounts
        account::where('account_provider', $operator->id)
            ->orWhere('account_owner', $operator->id)
            ->delete();

        // activity_logs
        // all_customers
        all_customer::where('operator_id', $operator->id)->update(['mgid' => $operator->id]);

        // backup_settings
        backup_setting::where('operator_id', $operator->id)->update(['mgid' => $operator->id]);

        // billing_profile_operator
        $operator_profiles = billing_profile_operator::where('operator_id', $operator->id)->get();

        foreach ($operator_profiles as $operator_profile) {

            $profile = billing_profile::find($operator_profile->billing_profile_id);

            $billing_profile = new billing_profile();
            $billing_profile->mgid = $operator->id;
            $billing_profile->billing_type = $profile->billing_type;
            $billing_profile->minimum_validity = $profile->minimum_validity;
            $billing_profile->profile_name = $profile->profile_name;
            $billing_profile->billing_due_date = $profile->billing_due_date;
            $billing_profile->auto_bill = $profile->auto_bill;
            $billing_profile->auto_lock = $profile->auto_lock;
            $billing_profile->cycle_ends_with_month = $profile->cycle_ends_with_month;
            $billing_profile->save();

            // update customers
            $model = new customer();
            $model->setConnection($operator->node_connection);
            $where = [
                ['operator_id', $operator->id],
                ['billing_profile_id', $profile->id],
            ];
            $model->where($where)->update(['billing_profile_id' => $billing_profile->id]);

            // update billing_profile_operator
            $operator_profile->billing_profile_id = $billing_profile->id;
            $operator_profile->save();
        }

        // billing_profiles
        // bulk_customer_bill_paids
        // cache
        // card_distributor_payments
        // card_distributors
        // cash_ins
        // cash_outs
        // complain_categories
        // complain_comments
        // complain_ledgers
        // custom_fields
        // custom_prices
        custom_price::where('operator_id', $operator->id)->delete();
        // customer_backup_requests
        // customer_bills
        customer_bill::where('operator_id', $operator->id)->update(['mgid' => $operator->id, 'gid' => $operator->id]);
        // customer_complains
        // customer_counts
        // customer_custom_attributes
        // customer_import_reports
        // customer_import_requests
        // customer_payments
        customer_payment::where('operator_id', $operator->id)->update(['mgid' => $operator->id, 'gid' => $operator->id]);
        // customer_zones
        // customers
        $model = new customer();
        $model->setConnection($operator->node_connection);
        $model->where('operator_id', $operator->id)->update(['mgid' => $operator->id, 'gid' => $operator->id]);
        // departments
        // devices
        // due_date_reminders
        // expense_categories
        // expense_subcategories
        // expenses
        // extend_package_validities
        // failed_jobs
        // fair_usage_policies
        // ipv4addresses
        // ipv4pools
        // ipv6pools
        // isp_informations
        // jobs
        // master_packages
        // max_subscription_payments
        // migrations
        // mikrotik_hotspot_user_profiles
        // mikrotik_hotspot_users
        // mikrotik_ip_pools
        // mikrotik_ppp_profiles
        // mikrotik_ppp_secrets
        // minimum_sms_bills
        // nas
        // nas_pppoe_profile
        // operator_packages
        // operator_permissions
        operator_permission::where('operator_id', $operator->id)->delete();
        // operators_incomes
        // packages
        $packages = package::where('operator_id', $operator->id)->get();

        foreach ($packages as $package) {

            $master_package = $package->master_package;

            // << pppoe_profile_id
            if ($master_package->connection_type == 'PPPoE') {
                $pppoe_profile = pppoe_profile::find($master_package->pppoe_profile_id);
                $ipv4pool = ipv4pool::find($pppoe_profile->ipv4pool_id);
                $ipv6pool = ipv6pool::find($pppoe_profile->ipv6pool_id);

                $new_ipv4pool = new ipv4pool();
                $new_ipv4pool->mgid = $operator->id;
                $new_ipv4pool->name = $ipv4pool->name;
                $new_ipv4pool->subnet = $ipv4pool->subnet;
                $new_ipv4pool->mask = $ipv4pool->mask;
                $new_ipv4pool->gateway = $ipv4pool->gateway;
                $new_ipv4pool->broadcast = $ipv4pool->broadcast;
                $new_ipv4pool->save();

                // ipv4addresses
                $update_ipv4pool_where = [
                    ['operator_id', '=', $operator->id],
                    ['ipv4pool_id', '=', $ipv4pool->id],
                ];
                ipv4address::where($update_ipv4pool_where)->update(['ipv4pool_id' => $new_ipv4pool->id]);

                if ($ipv6pool) {
                    $new_ipv6pool = new ipv6pool();
                    $new_ipv6pool->mgid = $operator->id;
                    $new_ipv6pool->prefix = $ipv6pool->prefix;
                    $new_ipv6pool->lowest_address = $ipv6pool->lowest_address;
                    $new_ipv6pool->highest_address = $ipv6pool->highest_address;
                    $new_ipv6pool->prefix_length = $ipv6pool->prefix_length;
                    $new_ipv6pool->save();
                }

                $new_pppoe_profile = new pppoe_profile();
                $new_pppoe_profile->mgid = $operator->id;
                $new_pppoe_profile->name = $pppoe_profile->name;
                $new_pppoe_profile->ipv4pool_id = $new_ipv4pool->id;
                if ($ipv6pool) {
                    $new_pppoe_profile->ipv6pool_id = $new_ipv6pool->id;
                }
                $new_pppoe_profile->ip_allocation_mode = $pppoe_profile->ip_allocation_mode;
                $new_pppoe_profile->save();

                $pppoe_profile_id = $new_pppoe_profile->id;
            } else {
                $pppoe_profile_id = 0;
            }
            // pppoe_profile_id >>

            $new_master_package = new master_package();
            $new_master_package->mgid = $operator->id;
            $new_master_package->pppoe_profile_id = $pppoe_profile_id;
            $new_master_package->connection_type = $master_package->connection_type;
            $new_master_package->name = $master_package->name;
            $new_master_package->rate_limit = $master_package->rate_limit;
            $new_master_package->rate_unit = $master_package->rate_unit;
            $new_master_package->speed_controller = $master_package->speed_controller;
            $new_master_package->volume_limit = $master_package->volume_limit;
            $new_master_package->volume_unit = $master_package->volume_unit;
            $new_master_package->validity = $master_package->validity;
            $new_master_package->price = $master_package->price;
            $new_master_package->operator_price = $master_package->operator_price;
            $new_master_package->visibility = $master_package->visibility;
            $new_master_package->save();

            $new_package = new package();
            $new_package->mgid = $operator->id;
            $new_package->gid = $operator->id;
            $new_package->operator_id = $operator->id;
            $new_package->mpid = $new_master_package->id;
            $new_package->ppid = 0;
            $new_package->name = $package->name;
            $new_package->price = $package->price;
            $new_package->operator_price = $package->operator_price;
            $new_package->visibility = $package->visibility;
            $new_package->save();
            $new_package->ppid = $new_package->id;
            $new_package->save();

            $update_package_where = [
                ['operator_id', '=', $operator->id],
                ['package_id', '=', $package->id],
            ];

            // [0] => customers
            $model = new customer();
            $model->setConnection($operator->node_connection);
            $model->where($update_package_where)->update(['package_id' => $new_package->id]);
            // [1] => customer_bills
            customer_bill::where($update_package_where)->update(['package_id' => $new_package->id]);
            // [2] => customer_payments
            customer_payment::where($update_package_where)->update(['package_id' => $new_package->id]);
            // [3] => custom_prices
            custom_price::where($update_package_where)->delete();
            // [4] => extend_package_validities
            // [5] => mikrotik_hotspot_user_profiles
            // [6] => mikrotik_ppp_profiles
            // [7] => operator_packages
            // [8] => recharge_cards
            recharge_card::where($update_package_where)->update(['package_id' => $new_package->id]);
            // [9] => temp_customers
            $package->delete();
        }

        // password_resets
        // payment_gateways
        // pending_transactions
        // pppoe_profiles
        // question_answers
        // question_explanations
        // question_options
        // questions
        // radaccts
        $model  = new radacct();
        $model->setConnection($operator->node_connection);
        $model->where('operator_id', $operator->id)->update(['mgid' => $operator->id]);
        // radchecks
        // radgroupchecks
        // radgroupreplies
        // radpostauths
        // radreplies
        // radusergroups
        // recharge_cards
        // sales_comments
        // sessions
        // sms_bills
        // sms_gateways
        // sms_histories
        // sms_payments
        // subscription_bills
        // subscription_discounts
        // subscription_payments
        // temp_billing_profiles
        // temp_customers
        // temp_packages
        // users

        // pgsql
        // pgsql_customers
        $model = new pgsql_customer();
        $model->setConnection($operator->pgsql_connection);
        $model->where('operator_id', $operator->id)->update(['mgid' => $operator->id]);
        // pgsql_radchecks
        // pgsql_radreplies
        $model = new pgsql_customer();
        $model->setConnection($operator->pgsql_connection);
        $pgsql_customers =  $model->where('operator_id', $operator->id)->get();
        while ($pgsql_customer = $pgsql_customers->shift()) {
            $pgsql_radcheck = new pgsql_radcheck();
            $pgsql_radcheck->setConnection($operator->pgsql_connection);
            $pgsql_radcheck->where('username', $pgsql_customer->username)->update(['mgid' => $operator->id]);

            $pgsql_radreply = new pgsql_radreply();
            $pgsql_radreply->setConnection($operator->pgsql_connection);
            $pgsql_radreply->where('username', $pgsql_customer->username)->update(['mgid' => $operator->id]);
        }

        // manager
        $manager_where = [
            ['gid', '=', $operator->id],
            ['role', '=', 'manager'],
        ];

        $managers = operator::where($manager_where)->get();

        foreach ($managers as $manager) {
            $manager->mgid = $operator->id;
            $manager->gid = $operator->id;
            $manager->save();
        }

        // operators
        $operator->mgid = $operator->id;
        $operator->gid = $operator->id;
        $operator->role = 'group_admin';
        $operator->status = 'active';
        $operator->subscription_type = 'Paid';
        $operator->subscription_status = 'active';
        $operator->save();
        return 0;
    }
}
