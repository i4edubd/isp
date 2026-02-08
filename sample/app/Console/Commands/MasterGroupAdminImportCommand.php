<?php

namespace App\Console\Commands;

use App\Http\Controllers\AllCustomerController;
use App\Http\Controllers\Customer\HotspotCustomersRadAttributesController;
use App\Http\Controllers\Customer\PPPoECustomersRadAttributesController;
use App\Http\Controllers\GroupAdminController;
use App\Models\all_customer;
use App\Models\billing_profile;
use App\Models\billing_profile_operator;
use App\Models\customer_bill;
use App\Models\customer_payment;
use App\Models\customer_zone;
use App\Models\device;
use App\Models\Freeradius\customer;
use App\Models\Freeradius\customer_custom_attribute;
use App\Models\Freeradius\nas;
use App\Models\operator;
use App\Models\operator_permission;
use App\Models\package;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MasterGroupAdminImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import_master_group_admin {mgid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import master group admin and resources from other server';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $fmgid = $this->argument('mgid');

        // The master admin to import.
        $model = new operator();
        $model->setConnection('import_master');
        $fmadmin = $model->findOrFail($fmgid);

        // show info
        $info = "Name: " . $fmadmin->name . " Email: " . $fmadmin->email . " Company: " . $fmadmin->company;
        $this->info($info);

        // confirm before process
        if ($this->confirm('Do you wish to continue?') == false) {
            return 0;
        }

        // # 1 Import Operators
        $this->importOperators($fmadmin);

        // # 2 Import Resources
        $this->importResources($fmadmin);

        // # 3 Import Customers and customer's Resources
        $this->importCustomers($fmadmin);

        // # 4 Import Accounts Statement
        $this->accountsStatement($fmadmin);

        return 0;
    }

    /**
     * Import Operators.
     *
     * @return int
     */
    public function importOperators($fmadmin)
    {
        // group admin
        if ($fmadmin->new_id == 0) {
            if (operator::where('email', $fmadmin->email)->count()) {
                $this->error('email address conflict for master admin');
                return 0;
            }
            $isuper_admin = operator::where('role', 'super_admin')->first();
            $radius_db_connection = GroupAdminController::assignDatabaseConnection();

            $imadmin = $fmadmin->replicate()->fill([
                'sid' => $isuper_admin->id,
                'radius_db_connection' => $radius_db_connection,
            ]);
            $imadmin->setConnection('mysql');
            $imadmin->save();
            $imadmin->mgid = $imadmin->id;
            $imadmin->gid = $imadmin->id;
            $imadmin->save();

            // save new id
            $fmadmin->new_id = $imadmin->id;
            $fmadmin->save();
        } else {
            $imadmin = operator::find($fmadmin->new_id);
        }

        // managers of group admins
        $fmadmin_managers = $fmadmin->operators->where('role', 'manager')->where('new_id', 0);
        foreach ($fmadmin_managers as $fmadmin_manager) {
            $imadmin_manager = $fmadmin_manager->replicate()->fill([
                'sid' => $imadmin->sid,
                'radius_db_connection' => $imadmin->radius_db_connection,
                'mgid' => $imadmin->id,
                'gid' => $imadmin->id,
            ]);
            $imadmin_manager->setConnection('mysql');
            $imadmin_manager->save();
            // save new id
            $fmadmin_manager->new_id = $imadmin_manager->id;
            $fmadmin_manager->save();
        }

        // import operators & their sub operators and managers
        $fmadmin_operators = $fmadmin->operators->where('role', 'operator')->where('new_id', 0);
        foreach ($fmadmin_operators as $fmadmin_operator) {
            $imadmin_operator = $fmadmin_operator->replicate()->fill([
                'sid' => $imadmin->sid,
                'radius_db_connection' => $imadmin->radius_db_connection,
                'mgid' => $imadmin->id,
                'gid' => $imadmin->id,
            ]);
            $imadmin_operator->setConnection('mysql');
            $imadmin_operator->save();
            // save new id
            $fmadmin_operator->new_id = $imadmin_operator->id;
            $fmadmin_operator->save();

            // $fmadmin_operator_managers
            $fmadmin_operator_managers = $fmadmin_operator->operators->where('role', 'manager')->where('new_id', 0);
            foreach ($fmadmin_operator_managers as $fmadmin_operator_manager) {
                $imadmin_operator_manager = $fmadmin_operator_manager->replicate()->fill([
                    'sid' => $imadmin->sid,
                    'radius_db_connection' => $imadmin->radius_db_connection,
                    'mgid' => $imadmin->id,
                    'gid' => $imadmin_operator->id,
                ]);
                $imadmin_operator_manager->setConnection('mysql');
                $imadmin_operator_manager->save();
                // save new id
                $fmadmin_operator_manager->new_id = $imadmin_operator_manager->id;
                $fmadmin_operator_manager->save();
            }

            // fmadmin_operator_sub_operators
            $fmadmin_operator_sub_operators = $fmadmin_operator->operators->where('role', 'sub_operator')->where('new_id', 0);
            foreach ($fmadmin_operator_sub_operators as $fmadmin_operator_sub_operator) {
                $imadmin_operator_sub_operator = $fmadmin_operator_sub_operator->replicate()->fill([
                    'sid' => $imadmin->sid,
                    'radius_db_connection' => $imadmin->radius_db_connection,
                    'mgid' => $imadmin->id,
                    'gid' => $imadmin_operator->id,
                ]);
                $imadmin_operator_sub_operator->setConnection('mysql');
                $imadmin_operator_sub_operator->save();
                // save new id
                $fmadmin_operator_sub_operator->new_id = $imadmin_operator_sub_operator->id;
                $fmadmin_operator_sub_operator->save();

                // fmadmin_operator_sub_operator_manager
                $fmadmin_operator_sub_operator_managers = $fmadmin_operator_sub_operator->operators->where('role', 'manager')->where('new_id', 0);
                foreach ($fmadmin_operator_sub_operator_managers as $fmadmin_operator_sub_operator_manager) {
                    $imadmin_operator_sub_operator_manager = $fmadmin_operator_sub_operator_manager->replicate()->fill([
                        'sid' => $imadmin->sid,
                        'radius_db_connection' => $imadmin->radius_db_connection,
                        'mgid' => $imadmin->id,
                        'gid' => $imadmin_operator_sub_operator->id,
                    ]);
                    $imadmin_operator_sub_operator_manager->setConnection('mysql');
                    $imadmin_operator_sub_operator_manager->save();
                    // save new id
                    $fmadmin_operator_sub_operator_manager->new_id = $imadmin_operator_sub_operator_manager->id;
                    $fmadmin_operator_sub_operator_manager->save();
                }
            }
        }

        $this->info('Operators Import Done!');
    }


    /**
     * Import resources.
     *
     * @return int
     */
    public function importResources($fmadmin)
    {
        // group_admin/billing_profiles
        foreach ($fmadmin->billing_profiles->where('new_id', 0) as $fbilling_profile) {
            $ibilling_profile = $fbilling_profile->replicate()->fill([
                'mgid' => $fmadmin->new_id,
            ]);
            $ibilling_profile->setConnection('mysql');
            $ibilling_profile->save();
            // save new id
            $fbilling_profile->new_id = $ibilling_profile->id;
            $fbilling_profile->save();
        }
        $this->info("billing_profiles ... done");

        // group_admin/nas
        $imadmin = operator::find($fmadmin->new_id);
        $model = new nas();
        $model->setConnection('import_node');
        $fnas = $model->where('new_id', 0)->where('mgid', $fmadmin->id)->get();
        foreach ($fnas as $fns) {
            $inas = $fns->replicate()->fill([
                'mgid' => $fmadmin->new_id,
            ]);
            $inas->setConnection($imadmin->radius_db_connection);
            $inas->save();
            // save new id
            $fns->new_id = $inas->id;
            $fns->save();
        }
        $this->info("nas ... done");

        // group_admin/ipv4pools
        foreach ($fmadmin->ipv4pools->where('new_id', 0) as $fipv4pool) {
            $iipv4pool = $fipv4pool->replicate()->fill([
                'mgid' => $fmadmin->new_id,
            ]);
            $iipv4pool->setConnection('mysql');
            $iipv4pool->save();
            // save new id
            $fipv4pool->new_id = $iipv4pool->id;
            $fipv4pool->save();
        }
        $this->info("ipv4pools ... done");

        // group_admin/ipv6pools
        foreach ($fmadmin->ipv6pools->where('new_id', 0) as $fipv6pool) {
            $iipv6pool = $fipv6pool->replicate()->fill([
                'mgid' => $fmadmin->new_id,
            ]);
            $iipv6pool->setConnection('mysql');
            $iipv6pool->save();
            // save new id
            $fipv6pool->new_id = $iipv6pool->id;
            $fipv6pool->save();
        }
        $this->info("ipv6pools ... done");

        // group_admin/pppoe_profiles
        foreach ($fmadmin->pppoe_profiles->where('new_id', 0) as $fpppoe_profile) {
            $ipppoe_profile = $fpppoe_profile->replicate()->fill([
                'mgid' => $fmadmin->new_id,
                'ipv4pool_id' => $fpppoe_profile->ipv4pool->new_id,
                'ipv6pool_id' => $fpppoe_profile->ipv6pool->new_id,
            ]);
            $ipppoe_profile->setConnection('mysql');
            $ipppoe_profile->save();
            // save new id
            $fpppoe_profile->new_id = $ipppoe_profile->id;
            $fpppoe_profile->save();
        }
        $this->info("pppoe_profiles ... done");

        // group_admin/master_packages
        foreach ($fmadmin->master_packages->where('new_id', 0) as $fmaster_package) {
            $imaster_package = $fmaster_package->replicate()->fill([
                'mgid' => $fmadmin->new_id,
                'pppoe_profile_id' => $fmaster_package->pppoe_profile->new_id,
            ]);
            $imaster_package->setConnection('mysql');
            $imaster_package->save();
            // save new id
            $fmaster_package->new_id = $imaster_package->id;
            $fmaster_package->save();
        }
        $this->info("master_packages ... done");

        //group_admin/operators/master_packages/packages
        $foperators = $fmadmin->operators->whereIn('role', ['group_admin', 'operator']);
        foreach ($foperators as $foperator) {
            foreach ($foperator->packages->where('new_id', 0) as $fpackage) {
                $ipackage = $fpackage->replicate()->fill([
                    'mgid' => $fmadmin->new_id,
                    'gid' => $fmadmin->new_id,
                    'operator_id' => $foperator->new_id,
                    'mpid' => $fpackage->master_package->new_id,
                ]);
                $ipackage->setConnection('mysql');
                $ipackage->save();
                $ipackage->ppid = $ipackage->id;
                $ipackage->save();
                // save new id
                $fpackage->new_id = $ipackage->id;
                $fpackage->save();
            }
        }
        $this->info("parent packages ... done");

        $foperators = $fmadmin->operators->whereIn('role', ['sub_operator']);
        foreach ($foperators as $foperator) {
            foreach ($foperator->packages->where('new_id', 0) as $fpackage) {
                $ipackage = $fpackage->replicate()->fill([
                    'mgid' => $fmadmin->new_id,
                    'gid' => $foperator->group_admin->new_id,
                    'operator_id' => $foperator->new_id,
                    'mpid' => $fpackage->master_package->new_id,
                    'ppid' => $fpackage->parent_package->new_id,
                ]);
                $ipackage->setConnection('mysql');
                $ipackage->save();
                // save new id
                $fpackage->new_id = $ipackage->id;
                $fpackage->save();
            }
        }
        $this->info("child packages ... done");

        // group_admin/operators/master_packages/fair_usage_policies
        foreach ($fmadmin->fair_usage_policies->where('new_id', 0) as $ffair_usage_policy) {
            $ifair_usage_policy = $ffair_usage_policy->replicate()->fill([
                'mgid' => $fmadmin->new_id,
                'master_package_id' => $ffair_usage_policy->master_package->new_id,
                'ipv4pool_id' => $ffair_usage_policy->ipv4pool->new_id,
            ]);
            $ifair_usage_policy->setConnection('mysql');
            $ifair_usage_policy->save();
            // save new id
            $ffair_usage_policy->new_id = $ifair_usage_policy->id;
            $ffair_usage_policy->save();
        }
        $this->info("fair_usage_policies ... done");

        //group_admin/operators/accounts
        $fmadmin_operators = $fmadmin->operators;
        foreach ($fmadmin_operators as $fmadmin_operator) {
            $faccounts_owns = $fmadmin_operator->accountsOwns->where('new_id', 0);
            foreach ($faccounts_owns as $faccounts_own) {
                if ($faccounts_own->provider->new_id == 0) {
                    continue;
                }
                $iaccounts_own = $faccounts_own->replicate()->fill([
                    'account_owner' => $fmadmin_operator->new_id,
                    'account_provider' => $faccounts_own->provider->new_id,
                ]);
                $iaccounts_own->setConnection('mysql');
                $iaccounts_own->save();
                // save new id
                $faccounts_own->new_id = $iaccounts_own->id;
                $faccounts_own->save();
            }
        }
        $this->info("accounts ... done");

        // group_admin/operators/billing_profiles/billing_profile_operator'
        $billing_profile_operators = $fmadmin->operators->whereIn('role', ['operator', 'sub_operator']);
        foreach ($billing_profile_operators as $billing_profile_operator) {
            $model = new billing_profile_operator();
            $model->setConnection('import_master');
            $foperators_billing_profiles = $model->where('new_id', 0)->where('operator_id', $billing_profile_operator->id)->get();
            foreach ($foperators_billing_profiles as $foperators_billing_profile) {
                $ioperators_billing_profile = new billing_profile_operator();
                $ioperators_billing_profile->billing_profile_id = $foperators_billing_profile->billing_profile->new_id;
                $ioperators_billing_profile->operator_id = $billing_profile_operator->new_id;
                $ioperators_billing_profile->save();
                $foperators_billing_profile->new_id = $ioperators_billing_profile->id;
                $foperators_billing_profile->save();
            }
        }
        $this->info("billing_profile_operator ... done");


        $foperators = $fmadmin->operators->whereIn('role', ['group_admin', 'operator', 'sub_operator']);

        // group_admin/operators/card_distributors
        // group_admin/operators/card_distributors/card_distributor_payments
        foreach ($foperators as $foperator) {
            $fitems = $foperator->card_distributors->where('new_id', 0);
            foreach ($fitems as $fitem) {
                $iitem = $fitem->replicate()->fill([
                    'operator_id' => $foperator->new_id,
                ]);
                $iitem->setConnection('mysql');
                $iitem->save();
                // save new id
                $fitem->new_id = $iitem->id;
                $fitem->save();

                $fsubitems = $fitem->card_distributor_payments->where('new_id', 0);
                foreach ($fsubitems as $fsubitem) {
                    $isubitem = $fsubitem->replicate()->fill([
                        'operator_id' => $foperator->new_id,
                        'card_distributor_id' => $fitem->new_id,
                    ]);
                    $isubitem->setConnection('mysql');
                    $isubitem->save();
                    // save new id
                    $fsubitem->new_id = $isubitem->id;
                    $fsubitem->save();
                }
            }
        }
        $this->info("card_distributors ... done");

        // group_admin/operators/complain_categories
        foreach ($foperators as $foperator) {
            $fitems = $foperator->complain_categories->where('new_id', 0);
            foreach ($fitems as $fitem) {
                $iitem = $fitem->replicate()->fill([
                    'operator_id' => $foperator->new_id,
                ]);
                $iitem->setConnection('mysql');
                $iitem->save();
                // save new id
                $fitem->new_id = $iitem->id;
                $fitem->save();
            }
        }
        $this->info("complain_categories ... done");

        // group_admin/operators/customer_zones
        foreach ($foperators as $foperator) {
            $fitems = $foperator->customer_zones->where('new_id', 0);
            foreach ($fitems as $fitem) {
                $iitem = $fitem->replicate()->fill([
                    'operator_id' => $foperator->new_id,
                ]);
                $iitem->setConnection('mysql');
                $iitem->save();
                // save new id
                $fitem->new_id = $iitem->id;
                $fitem->save();
            }
        }
        $this->info("customer_zones ... done");

        // group_admin/operators/custom_fields
        foreach ($foperators as $foperator) {
            $fitems = $foperator->custom_fields->where('new_id', 0);
            foreach ($fitems as $fitem) {
                $iitem = $fitem->replicate()->fill([
                    'operator_id' => $foperator->new_id,
                ]);
                $iitem->setConnection('mysql');
                $iitem->save();
                // save new id
                $fitem->new_id = $iitem->id;
                $fitem->save();
            }
        }
        $this->info("custom_fields ... done");

        // group_admin/operators/departments
        foreach ($foperators as $foperator) {
            $fitems = $foperator->departments->where('new_id', 0);
            foreach ($fitems as $fitem) {
                $iitem = $fitem->replicate()->fill([
                    'operator_id' => $foperator->new_id,
                ]);
                $iitem->setConnection('mysql');
                $iitem->save();
                // save new id
                $fitem->new_id = $iitem->id;
                $fitem->save();
            }
        }
        $this->info("departments ... done");

        // group_admin/operators/devices
        foreach ($foperators as $foperator) {
            $fitems = $foperator->devices->where('new_id', 0);
            foreach ($fitems as $fitem) {
                $iitem = $fitem->replicate()->fill([
                    'operator_id' => $foperator->new_id,
                ]);
                $iitem->setConnection('mysql');
                $iitem->save();
                // save new id
                $fitem->new_id = $iitem->id;
                $fitem->save();
            }
        }
        $this->info("devices ... done");

        // group_admin/operators/due_date_reminders
        foreach ($foperators as $foperator) {
            $fitems = $foperator->due_date_reminders->where('new_id', 0);
            foreach ($fitems as $fitem) {
                $iitem = $fitem->replicate()->fill([
                    'operator_id' => $foperator->new_id,
                ]);
                $iitem->setConnection('mysql');
                $iitem->save();
                // save new id
                $fitem->new_id = $iitem->id;
                $fitem->save();
            }
        }
        $this->info("due_date_reminders ... done");

        //group_admin/operators/expense_categories
        //group_admin/operators/expense_categories/expense_subcategories
        foreach ($foperators as $foperator) {
            $fitems = $foperator->expense_categories->where('new_id', 0);
            foreach ($fitems as $fitem) {
                $iitem = $fitem->replicate()->fill([
                    'operator_id' => $foperator->new_id,
                ]);
                $iitem->setConnection('mysql');
                $iitem->save();
                // save new id
                $fitem->new_id = $iitem->id;
                $fitem->save();

                $fsubitems = $fitem->subcategories->where('new_id', 0);
                foreach ($fsubitems as $fsubitem) {
                    $isubitem = $fsubitem->replicate()->fill([
                        'operator_id' => $foperator->new_id,
                        'expense_category_id' => $fitem->new_id,
                    ]);
                    $isubitem->setConnection('mysql');
                    $isubitem->save();
                    // save new id
                    $fsubitem->new_id = $isubitem->id;
                    $fsubitem->save();
                }
            }
        }
        $this->info("expense_categories ... done");

        //group_admin/operators/expense_categories/expense_subcategories/expenses
        foreach ($foperators as $foperator) {
            $fitems = $foperator->expenses->where('new_id', 0);
            foreach ($fitems as $fitem) {
                $iitem = $fitem->replicate()->fill([
                    'operator_id' => $foperator->new_id,
                    'expense_category_id' => $fitem->category->new_id,
                    'expense_subcategory_id' => $fitem->subcategory->new_id,
                ]);
                $iitem->setConnection('mysql');
                $iitem->save();
                // save new id
                $fitem->new_id = $iitem->id;
                $fitem->save();
            }
        }
        $this->info("expenses ... done");

        //group_admin/operators/operator_permissions
        foreach ($foperators as $foperator) {
            $model = new operator_permission();
            $model->setConnection('import_master');
            $foperator_permissions = $model->where('new_id', 0)->where('operator_id', $foperator->id)->get();
            foreach ($foperator_permissions as $foperator_permission) {
                $ioperator_permission = new operator_permission();
                $ioperator_permission->operator_id  = $foperator->new_id;
                $ioperator_permission->permission = $foperator_permission->permission;
                $ioperator_permission->save();
                $foperator_permission->new_id = $ioperator_permission->id;
                $foperator_permission->save();
            }
        }
        $this->info("operator_permissions ... done");

        //group_admin/operators/sms_gateway
        foreach ($foperators as $foperator) {
            $fsms_gateway = $foperator->sms_gateway;
            if (!$fsms_gateway) {
                continue;
            }
            if ($fsms_gateway->new_id !== 0) {
                continue;
            }
            $isms_gateway = $fsms_gateway->replicate()->fill([
                'operator_id' => $foperator->new_id,
            ]);
            $isms_gateway->setConnection('mysql');
            $isms_gateway->save();
            // save new id
            $fsms_gateway->new_id = $isms_gateway->id;
            $fsms_gateway->save();
        }
        $this->info("sms_gateway ... done");

        // {operators,packages,card_distributors}/recharge_cards
        foreach ($foperators as $foperator) {
            $fitems = $foperator->recharge_cards->where('new_id', 0)->where('status', 'unused');
            foreach ($fitems as $fitem) {
                $iitem = $fitem->replicate()->fill([
                    'operator_id' => $foperator->new_id,
                    'card_distributor_id' => $fitem->distributor->new_id,
                    'package_id' => $fitem->package->new_id,
                ]);
                $iitem->setConnection('mysql');
                $iitem->save();
                // save new id
                $fitem->new_id = $iitem->id;
                $fitem->save();
            }
        }
        $this->info("recharge_cards ... done");
    }

    /**
     * Import Customers
     *
     */
    public function importCustomers($fmadmin)
    {

        $imadmin = operator::find($fmadmin->new_id);

        $model = new customer();
        $model->setConnection('import_node');
        $fcustomers = $model->where('new_id', 0)->where('mgid', $fmadmin->id)->get();

        $ttl = 600;

        $this->info('Importing customers ...');
        $bar = $this->output->createProgressBar(count($fcustomers));
        $bar->start();

        foreach ($fcustomers as $fcustomer) {

            $icustomer = $fcustomer->replicate()->fill([
                'mgid' => $fmadmin->new_id,
            ]);

            // operator
            $foperator_key = 'foperator_' . $fcustomer->operator_id;
            $foperator = Cache::remember($foperator_key, $ttl, function () use ($fcustomer) {
                $model = new operator();
                $model->setConnection('import_master');
                return $model->find($fcustomer->operator_id);
            });
            $ioperator = operator::find($foperator->new_id);
            $icustomer->gid = $ioperator->gid;
            $icustomer->operator_id = $ioperator->id;

            // zone_id
            $fzone_key = 'fzone_' . $fcustomer->zone_id;
            $fzone = Cache::remember($fzone_key, $ttl, function () use ($fcustomer) {
                $model = new customer_zone();
                $model->setConnection('import_master');
                return $model->find($fcustomer->zone_id);
            });

            if ($fzone) {
                $icustomer->zone_id = $fzone->new_id;
            }

            // device_id
            $fdevice_key = 'fdevice_' . $fcustomer->device_id;
            $fdevice = Cache::remember($fdevice_key, $ttl, function () use ($fcustomer) {
                $model = new device();
                $model->setConnection('import_master');
                return $model->find($fcustomer->device_id);
            });

            if ($fdevice) {
                $icustomer->device_id = $fdevice->new_id;
            }

            // billing_profile_id
            $fbilling_profile_key = 'fbilling_profile_' . $fcustomer->billing_profile_id;
            $fbilling_profile = Cache::remember($fbilling_profile_key, $ttl, function () use ($fcustomer) {
                $model = new billing_profile();
                $model->setConnection('import_master');
                return $model->find($fcustomer->billing_profile_id);
            });
            if ($fbilling_profile) {
                $icustomer->billing_profile_id = $fbilling_profile->new_id;
            }

            // package_id
            $fpackage_key = 'fpackage_' . $fcustomer->package_id;
            $fpackage = Cache::remember($fpackage_key, $ttl, function () use ($fcustomer) {
                $model = new package();
                $model->setConnection('import_master');
                return $model->find($fcustomer->package_id);
            });
            if ($fpackage) {
                $icustomer->package_id = $fpackage->new_id;
            }

            // save
            try {
                $icustomer->setConnection($imadmin->radius_db_connection);
                $icustomer->save();
                $icustomer->parent_id = $icustomer->id;
                $icustomer->save();
            } catch (\Throwable $th) {
                Log::channel('debug')->info($icustomer->username);
                continue;
            }

            $fcustomer->new_id = $icustomer->id;
            $fcustomer->save();

            //update Central Database
            AllCustomerController::updateOrCreate($icustomer);

            //radcheck and radreply information
            if ($icustomer->connection_type == 'PPPoE') {
                PPPoECustomersRadAttributesController::updateOrCreate($icustomer);
            }

            if ($icustomer->connection_type == 'Hotspot') {
                HotspotCustomersRadAttributesController::updateOrCreate($icustomer);
            }

            // custom_attributes
            $fcustom_attributes = $fcustomer->custom_attributes->where('new_id', 0);
            foreach ($fcustom_attributes as $fcustom_attribute) {
                $icustom_attribute = new customer_custom_attribute();
                $icustom_attribute->setConnection($ioperator->node_connection);
                $icustom_attribute->customer_id = $icustomer->id;
                $icustom_attribute->custom_field_id = $foperator->custom_fields->where('id', $fcustom_attribute->custom_field_id)->first()->new_id;
                $icustom_attribute->value = $fcustom_attribute->value;
                $icustom_attribute->save();
                $fcustom_attribute->new_id = $icustom_attribute->id;
                $fcustom_attribute->save();
            }

            // bills
            $bills_model = new customer_bill();
            $bills_model->setConnection('import_master');
            $fbills = $bills_model->where('new_id', 0)->where('operator_id', $foperator->id)->where('customer_id', $fcustomer->id)->get();
            foreach ($fbills as $fbill) {
                $ibill = $fbill->replicate()->fill([
                    'mgid' => $ioperator->mgid,
                    'gid' => $ioperator->gid,
                    'operator_id' => $ioperator->id,
                    'customer_id' => $icustomer->id,
                    'package_id' => $fpackage->new_id,
                ]);
                if ($fzone) {
                    $ibill->customer_zone_id = $fzone->new_id;
                }
                $ibill->setConnection('mysql');
                $ibill->save();
                $fbill->new_id = $ibill->id;
                $fbill->save();
            }

            // payments
            $payments_model = new customer_payment();
            $payments_model->setConnection('import_master');
            $fpayments = $payments_model->where('new_id', 0)->where('operator_id', $foperator->id)->where('customer_id', $fcustomer->id)->get();
            foreach ($fpayments as $fpayment) {
                $ipayment = $fpayment->replicate()->fill([
                    'mgid' => $ioperator->mgid,
                    'gid' => $ioperator->gid,
                    'operator_id' => $ioperator->id,
                    'cash_collector_id' => $fpayment->cash_collector->new_id,
                    'customer_id' => $icustomer->id,
                    'package_id' => $fpackage->new_id,
                ]);
                $ipayment->setConnection('mysql');
                $ipayment->save();
                $fpayment->new_id = $ipayment->id;
                $fpayment->save();
            }
            $bar->advance();
        }

        $bar->finish();
        $fcustomers = 0;

        $this->info("...done");
    }

    /**
     * Get the accounts transactions
     */
    public function accountsStatement($fmadmin)
    {
        $this->info("Importing accounts statement ..");
        $fmadmin_operators = $fmadmin->operators;
        foreach ($fmadmin_operators as $fmadmin_operator) {
            $faccounts_owns = $fmadmin_operator->accountsOwns;
            foreach ($faccounts_owns as $faccounts_own) {
                $fcash_ins = $faccounts_own->cash_ins->where('new_id', 0);
                foreach ($fcash_ins as $fcash_in) {
                    $icash_in = $fcash_in->replicate()->fill([
                        'account_id' => $faccounts_own->new_id,
                    ]);
                    $fcash_in_transaction = $fcash_in->transaction;
                    if ($fcash_in_transaction) {
                        $icash_in->transaction_id = $fcash_in_transaction->new_id;
                    }
                    $icash_in->setConnection('mysql');
                    $icash_in->save();
                    $fcash_in->new_id = $icash_in->id;
                    $fcash_in->save();
                }

                $fcash_outs = $faccounts_own->cash_outs->where('new_id', 0);
                foreach ($fcash_outs as $fcash_out) {
                    $icash_out = $fcash_out->replicate()->fill([
                        'account_id' => $faccounts_own->new_id,
                    ]);
                    $fcash_out_transaction = $fcash_out->transaction;
                    if ($fcash_out_transaction) {
                        $icash_out->transaction_id = $fcash_out_transaction->new_id;
                    }
                    $icash_out->setConnection('mysql');
                    $icash_out->save();
                    $fcash_out->new_id = $icash_out->id;
                    $fcash_out->save();
                }
            }
        }

        $this->info("All Done ... Thanks to God");
    }
}
