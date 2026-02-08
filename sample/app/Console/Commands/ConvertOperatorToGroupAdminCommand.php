<?php

namespace App\Console\Commands;

use App\Http\Controllers\CacheController;
use App\Models\all_customer;
use App\Models\backup_setting;
use App\Models\billing_profile_operator;
use App\Models\bkash_checkout_agreement;
use App\Models\custom_price;
use App\Models\customer_bill;
use App\Models\customer_change_log;
use App\Models\customer_payment;
use App\Models\deleted_customer;
use App\Models\Freeradius\customer;
use App\Models\Freeradius\radacct;
use App\Models\master_package;
use App\Models\operator;
use App\Models\pgsql\pgsql_customer;
use App\Models\pgsql\pgsql_radcheck;
use App\Models\pgsql\pgsql_radreply;
use Illuminate\Console\Command;

class ConvertOperatorToGroupAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convert_operator_to_group_admin {operator_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert Operator To Group Admin';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Input
        $operator = operator::findOrFail($this->argument('operator_id'));

        $old_admin = operator::findOrFail($operator->mgid);

        // confirm before process
        $info = 'The operator which will be converted is ' . $operator->id . '::' . $operator->email . '::' . $operator->role;
        $this->info($info);

        $info = 'The Group Admin the operator belongs to is ' . $old_admin->id . '::' . $old_admin->email  . '::' . $old_admin->role;
        $this->info($info);

        if ($this->confirm('Do you wish to continue?') == false) {
            return 0;
        }

        if ($operator->role !== 'operator') {
            $this->error('Not a operator');
            return 0;
        }

        $this->convertOperators($operator, $old_admin);

        $this->copyResources($operator);

        $this->convertCustomers($operator);

        return Command::SUCCESS;
    }

    /**
     * Convert Operators.
     *
     * @return void
     */
    public function convertOperators(operator $foperator, operator $old_admin)
    {
        $this->info('converting operator ....');

        // convert operator to group_admin
        $foperator->mgid = $foperator->id;
        $foperator->gid = $foperator->id;
        $foperator->role = 'group_admin';
        $foperator->save();

        all_customer::where('operator_id', $foperator->id)->update(['mgid' => $foperator->id]);
        backup_setting::where('operator_id', $foperator->id)->delete();
        backup_setting::where('operator_id', $foperator->id)->delete();
        billing_profile_operator::where('operator_id', $foperator->id)->delete();
        bkash_checkout_agreement::where('operator_id', $foperator->id)->delete();
        customer_change_log::where('operator_id', $foperator->id)->update(['gid' => $foperator->gid]);
        custom_price::where('operator_id', $foperator->id)->delete();
        customer_bill::where('operator_id', $foperator->id)->update(['mgid' => $foperator->id, 'gid' => $foperator->id]);
        customer_payment::where('operator_id', $foperator->id)->update(['mgid' => $foperator->id, 'gid' => $foperator->id]);
        deleted_customer::where('operator_id', $foperator->id)->delete();

        $model = new radacct();
        $model->setConnection($foperator->node_connection);
        $model->where('operator_id', $foperator->id)->update(['mgid' => $foperator->id]);

        $model = new pgsql_customer();
        $model->setConnection($foperator->pgsql_connection);
        $model->where('operator_id', $foperator->id)->update(['mgid' => $foperator->id]);

        $model = new pgsql_radcheck();
        $model->setConnection($foperator->pgsql_connection);
        $model->where('mgid', $old_admin->id)->update(['mgid' => $foperator->id]);

        $model = new pgsql_radreply();
        $model->setConnection($foperator->pgsql_connection);
        $model->where('mgid', $old_admin->id)->update(['mgid' => $foperator->id]);

        /*
            [0] => all_customers >> done
            [1] => backup_settings >> done
            [2] => billing_profiles >> will be copied during update customers
            [3] => bkash_checkout_agreements >> done
            [4] => custom_prices >> done
            [5] => customer_backup_requests >> n/a
            [6] => customer_bills >> done
            [7] => customer_counts >> n/a
            [8] => customer_import_reports >> n/a
            [9] => customer_import_requests >> n/a
            [10] => customer_payments >> done
            [11] => customers >> during update customers
            [12] => deleted_customers >> done
            [13] => fair_usage_policies >> n/a
            [14] => ipv4pools >> done
            [15] => ipv6pools >> done
            [16] => master_packages >> done
            [17] => mikrotik_hotspot_user_profiles >> n/a
            [18] => mikrotik_hotspot_users >> n/a
            [19] => mikrotik_ip_pools >> n/a
            [20] => mikrotik_ppp_profiles >> n/a
            [21] => mikrotik_ppp_secrets >> n/a
            [22] => nas >> n/a
            [23] => nas_pppoe_profile >> n/a
            [24] => operators >> done
            [25] => packages >> done
            [26] => pppoe_profiles >> done
            [27] => radaccts >> done
            [28] => radchecks >> done
            [29] => radpostauths >> n/a
            [30] => radreplies >> done
            [31] => radusergroups >> n/a
            [32] => sales_comments >> n/a
            [33] => subscription_bills >> n/a
            [34] => subscription_payments >> n/a
            [35] => temp_billing_profiles >> n/a
            [36] => temp_customers >> n/a
            [37] => temp_packages >> n/a
            [38] => vat_collections >> n/a
            [39] => vat_profiles >> n/a
            [40] => vpn_accounts >> n/a
        */

        $operators = $foperator->operators;

        // move managers of the foperator
        foreach ($operators->where('role', 'manager') as $manager) {
            $manager->mgid = $foperator->id;
            $manager->gid = $foperator->id;
            $manager->save();
        }

        // move sub_operators of the foperator and convert them to operator
        foreach ($operators->where('role', 'sub_operator') as $sub_operator) {

            all_customer::where('operator_id', $sub_operator->id)->update(['mgid' => $foperator->id]);
            backup_setting::where('operator_id', $sub_operator->id)->delete();
            billing_profile_operator::where('operator_id', $sub_operator->id)->delete();
            bkash_checkout_agreement::where('operator_id', $sub_operator->id)->delete();
            customer_change_log::where('operator_id', $sub_operator->id)->update(['gid' => $sub_operator->gid]);
            custom_price::where('operator_id', $sub_operator->id)->delete();
            customer_bill::where('operator_id', $sub_operator->id)->update(['mgid' => $foperator->id, 'gid' => $sub_operator->gid]);
            customer_payment::where('operator_id', $sub_operator->id)->update(['mgid' => $foperator->id, 'gid' => $sub_operator->gid]);
            deleted_customer::where('operator_id', $sub_operator->id)->delete();

            $model = new radacct();
            $model->setConnection($foperator->node_connection);
            $model->where('operator_id', $sub_operator->id)->update(['mgid' => $foperator->id]);

            $model = new pgsql_customer();
            $model->setConnection($foperator->pgsql_connection);
            $model->where('operator_id', $sub_operator->id)->update(['mgid' => $foperator->id]);

            $sub_operator->mgid = $foperator->id;
            $sub_operator->gid = $foperator->id;
            $sub_operator->role = 'operator';
            $sub_operator->save();

            // move managers of the sub_operators
            foreach ($sub_operator->operators->where('role', 'manager') as $manager) {
                $manager->mgid = $foperator->id;
                $manager->save();
            }
        }

        $this->info('Operators convert Done!');
    }

    /**
     * copy Resources
     *
     * @return void
     */
    public function copyResources(operator $foperator)
    {
        $this->info('Package copy processing ...');

        // packages >  master_packages > pppoe_profiles > ipv6pools & ipv4pools

        $master_package_mapping = [];

        foreach ($foperator->assigned_packages as $package) {

            $master_package = $package->master_package;

            if (array_key_exists($master_package->id, $master_package_mapping) == false) {
                $new_master_package = $master_package->replicate()->fill([
                    'mgid' => $foperator->id,
                ]);
                $new_master_package->save();

                $pppoe_profile = $master_package->pppoe_profile;
                $new_pppoe_profile = $pppoe_profile->replicate()->fill([
                    'mgid' => $foperator->id,
                ]);
                $new_pppoe_profile->save();
                $new_master_package->pppoe_profile_id = $new_pppoe_profile->id;
                $new_master_package->save();

                $ipv4pool = $pppoe_profile->ipv4pool;
                $new_ipv4pool = $ipv4pool->replicate()->fill([
                    'mgid' => $foperator->id,
                ]);
                $new_ipv4pool->save();
                $new_pppoe_profile->ipv4pool_id = $new_ipv4pool->id;
                $new_pppoe_profile->ipv6pool_id = 0;
                $new_pppoe_profile->save();

                $master_package_mapping[$master_package->id] = $new_master_package->id;
            } else {
                $new_master_package = master_package::findOrFail($master_package_mapping[$master_package->id]);
            }

            $package->mgid = $foperator->id;
            $package->gid = $foperator->gid;
            $package->operator_id = $foperator->id;
            $package->mpid = $new_master_package->id;
            $package->save();

            foreach ($package->child_packages as $child_package) {
                $child_package->mgid = $foperator->id;
                $child_package->gid = $child_package->operator->gid;
                $child_package->mpid = $new_master_package->id;
                $child_package->save();
            }
        }

        $this->info('Package copy Done!');
    }

    /**
     * convert customers
     *
     * @return void
     */
    public function convertCustomers(operator $foperator)
    {

        $this->info('converting customers ... ');

        $billing_profiles_mapping = [];

        // operators customers
        $model = new customer();
        $model->setConnection($foperator->node_connection);
        $customers = $model->where('operator_id', $foperator->id)->get();
        foreach ($customers as $customer) {
            $billing_profile = CacheController::getBillingProfile($customer->billing_profile_id);
            if (array_key_exists($billing_profile->id, $billing_profiles_mapping) == false) {
                $new_billing_profile = $billing_profile->replicate()->fill([
                    'mgid' => $foperator->id,
                ]);
                $new_billing_profile->save();
                $billing_profiles_mapping[$billing_profile->id] = $new_billing_profile->id;
            } else {
                $new_billing_profile = CacheController::getBillingProfile($billing_profiles_mapping[$billing_profile->id]);
            }

            $customer->mgid = $foperator->id;
            $customer->gid = $foperator->id;
            $customer->billing_profile_id = $new_billing_profile->id;
            $customer->save();
        }

        // sub_operators customers
        $operators = $foperator->operators;
        foreach ($operators->where('role', 'sub_operator') as $sub_operator) {
            $model = new customer();
            $model->setConnection($sub_operator->node_connection);
            $customers = $model->where('operator_id', $sub_operator->id)->get();
            foreach ($customers as $customer) {
                $billing_profile = CacheController::getBillingProfile($customer->billing_profile_id);
                if (array_key_exists($billing_profile->id, $billing_profiles_mapping) == false) {
                    $new_billing_profile = $billing_profile->replicate()->fill([
                        'mgid' => $foperator->id,
                    ]);
                    $new_billing_profile->save();
                    $billing_profiles_mapping[$billing_profile->id] = $new_billing_profile->id;
                } else {
                    $new_billing_profile = CacheController::getBillingProfile($billing_profiles_mapping[$billing_profile->id]);
                }

                $customer->mgid = $foperator->id;
                $customer->gid = $sub_operator->gid;
                $customer->billing_profile_id = $new_billing_profile->id;
                $customer->save();
            }
        }

        $this->info('converting done!');
    }
}
