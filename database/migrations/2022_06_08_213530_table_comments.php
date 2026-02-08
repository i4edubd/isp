<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:yes,order:group_admin/operators/accounts');
        });

        Schema::dropIfExists('activity_logs');

        Schema::table('all_customers', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:no,transfer:no,order:recreate from customers');
        });

        Schema::table('backup_settings', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:no,transfer:no,order:recreate by create');
        });

        Schema::table('billing_profiles', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:yes,order:group_admin/operators,billing_profiles');
        });

        Schema::table('billing_profile_operator', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:yes,order:group_admin/operators,billing_profiles/billing_profile_operator');
        });

        Schema::table('bkash_checkout_agreements', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:no,order:backup is optional');
        });

        Schema::table('bulk_customer_bill_paids', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:no,transfer:no,order:supporting table');
        });

        Schema::table('cache', function (Blueprint $table) {
            // $table->comment('managed by framework');
        });

        Schema::table('card_distributors', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:yes,order:group_admin/operators/card_distributors');
        });

        Schema::table('card_distributor_payments', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:yes,order:group_admin/operators/card_distributors/card_distributor_payments');
        });

        Schema::table('cash_ins', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:yes,order:group_admin/operators/accounts/cash_ins');
        });

        Schema::table('cash_outs', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:yes,order:group_admin/operators/accounts/cash_outs');
        });

        Schema::table('complain_categories', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:yes,order:group_admin/operators/complain_categories');
        });

        Schema::table('complain_comments', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:no,order:no need to transer');
        });

        Schema::table('complain_ledgers', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:no,order:no need to transer');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->comment('model:mysql_node,backup:yes,transfer:yes,order:group_admin/billing_profiles,master_packages*/operators/billing_profile_operator,*_id/customers');
        });

        Schema::table('customer_backup_requests', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:no,transfer:no,order:no');
        });

        Schema::table('customer_bills', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:yes,order:*customers/customer_bills');
        });

        Schema::table('customer_bills_summaries', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:no,transfer:no,order:no');
        });

        Schema::table('customer_complains', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:no,order:no');
        });

        Schema::table('customer_counts', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:no,order:no');
        });

        Schema::table('customer_counts', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:no,order:no');
        });

        Schema::table('customer_custom_attributes', function (Blueprint $table) {
            $table->comment('model:mysql_node,backup:yes,transfer:yes,order:*customers/customer_custom_attributes');
        });

        Schema::table('customer_import_reports', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:no,transfer:no,order:no');
        });

        Schema::table('customer_import_requests', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:no,transfer:no,order:no');
        });

        Schema::table('customer_payments', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:yes,order:*customers/customer_payments,cash_ins_update');
        });

        Schema::table('customer_zones', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:yes,order:group_admin/billing_profiles,master_packages*/operators/customer_zones');
        });

        Schema::table('custom_fields', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:yes,order:group_admin/billing_profiles,master_packages*/operators/custom_fields');
        });

        Schema::table('custom_prices', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:*customers/custom_prices');
        });

        Schema::table('deleted_customers', function (Blueprint $table) {
            // 
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:yes,order:group_admin/billing_profiles,master_packages*/operators/departments');
        });

        Schema::table('devices', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:yes,order:group_admin/billing_profiles,master_packages*/operators/devices');
        });

        // due_date_reminders
        Schema::table('due_date_reminders', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:yes,order:group_admin/billing_profiles,master_packages*/operators/due_date_reminders');
        });

        // expenses
        Schema::table('expenses', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:yes,order:group_admin/billing_profiles,master_packages*/operators/cat/sub_cat/expenses');
        });

        // expense_categories
        Schema::table('expense_categories', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:yes,order:group_admin/billing_profiles,master_packages*/operators/expense_categories');
        });

        // expense_subcategories
        Schema::table('expense_subcategories', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:yes,order:group_admin/billing_profiles,master_packages*/operators/expense_categories/expense_subcategories');
        });

        // extend_package_validities
        // failed_jobs
        // fair_usage_policies
        Schema::table('fair_usage_policies', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:yes,order:group_admin/billing_profiles,master_packages*/operators,fair_usage_policies');
        });

        // ipv4addresses
        // ipv4pools
        Schema::table('ipv4pools', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:yes,order:group_admin/ipv4pools');
        });

        // ipv6pools
        Schema::table('ipv6pools', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:yes,order:group_admin/ipv6pools');
        });

        // isp_informations
        // jobs
        // master_packages
        Schema::table('master_packages', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:yes,order:group_admin/ipv4pools,ipv6pools/pppoe_profiles/master_packages');
        });

        // max_subscription_payments
        // migrations
        // mikrotik_*
        // minimum_sms_bills
        // nas
        Schema::table('nas', function (Blueprint $table) {
            $table->comment('model:mysql_node,backup:yes,transfer:yes,order:group_admin/nas');
        });
        // nas_pppoe_profile
        // operators
        Schema::table('operators', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:yes,order:group_admin/operators');
        });
        // operators_incomes
        // operator_packages
        // operator_permissions
        Schema::table('operator_permissions', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:yes,order:*master_packages/operators/operator_permissions');
        });
        // packages
        Schema::table('packages', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:yes,order:*master_packages/operators/packages');
        });
        // package_change_histories
        // password_resets
        // payment_gateways
        // pending_transactions
        // pppoe_profiles
        Schema::table('pppoe_profiles', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:yes,order:group_admin/ipv4pools,ipv6pools/pppoe_profiles');
        });
        // questions*
        // rad*
        // recharge_cards
        Schema::table('recharge_cards', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:yes,order:{operators,packages,card_distributors}/recharge_cards');
        });

        // sales_comments
        // sessions
        // sms_gateways
        Schema::table('sms_gateways', function (Blueprint $table) {
            $table->comment('model:mysql_central,backup:yes,transfer:yes,order:/group_admin/operators/sms_gateways');
        });
        // sms_*
        // subscription*
        // temp_*
        // vpn*
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
