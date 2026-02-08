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
        // accounts
        Schema::table('accounts', function (Blueprint $table) {
            $table->foreignId('new_id')->default(0)->nullable()->change();
        });

        // billing_profile_operator
        Schema::table('billing_profile_operator', function (Blueprint $table) {
            $table->foreignId('new_id')->default(0)->nullable()->change();
        });

        // card_distributor_payments
        Schema::table('card_distributor_payments', function (Blueprint $table) {
            $table->foreignId('new_id')->default(0)->nullable()->change();
        });

        // cash_ins
        Schema::table('cash_ins', function (Blueprint $table) {
            $table->foreignId('new_id')->default(0)->nullable()->change();
        });

        // cash_outs
        Schema::table('cash_outs', function (Blueprint $table) {
            $table->foreignId('new_id')->default(0)->nullable()->change();
        });

        // complain_categories
        Schema::table('complain_categories', function (Blueprint $table) {
            $table->foreignId('new_id')->default(0)->nullable()->change();
        });

        // customer_bills
        Schema::table('customer_bills', function (Blueprint $table) {
            $table->foreignId('new_id')->default(0)->nullable()->change();
        });

        // customer_custom_attributes
        Schema::table('customer_custom_attributes', function (Blueprint $table) {
            $table->foreignId('new_id')->default(0)->nullable()->change();
        });

        // customer_payments
        Schema::table('customer_payments', function (Blueprint $table) {
            $table->foreignId('new_id')->default(0)->nullable()->change();
        });

        // custom_fields
        Schema::table('custom_fields', function (Blueprint $table) {
            $table->foreignId('new_id')->default(0)->nullable()->change();
        });

        // custom_prices
        Schema::table('custom_prices', function (Blueprint $table) {
            $table->foreignId('new_id')->default(0)->nullable()->change();
        });

        // departments
        Schema::table('departments', function (Blueprint $table) {
            $table->foreignId('new_id')->default(0)->nullable()->change();
        });

        // devices
        Schema::table('devices', function (Blueprint $table) {
            $table->foreignId('new_id')->default(0)->nullable()->change();
        });

        // due_date_reminders
        Schema::table('due_date_reminders', function (Blueprint $table) {
            $table->foreignId('new_id')->default(0)->nullable()->change();
        });

        // expenses
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('new_id')->default(0)->nullable()->change();
        });

        // expense_categories
        Schema::table('expense_categories', function (Blueprint $table) {
            $table->foreignId('new_id')->default(0)->nullable()->change();
        });

        // expense_subcategories
        Schema::table('expense_subcategories', function (Blueprint $table) {
            $table->foreignId('new_id')->default(0)->nullable()->change();
        });

        // fair_usage_policies
        Schema::table('fair_usage_policies', function (Blueprint $table) {
            $table->foreignId('new_id')->default(0)->nullable()->change();
        });

        // ipv6pools
        Schema::table('ipv6pools', function (Blueprint $table) {
            $table->foreignId('new_id')->default(0)->nullable()->change();
        });

        // recharge_cards
        Schema::table('recharge_cards', function (Blueprint $table) {
            $table->foreignId('new_id')->default(0)->nullable()->change();
        });

        // operator_permissions
        Schema::table('operator_permissions', function (Blueprint $table) {
            $table->foreignId('new_id')->default(0)->nullable()->change();
        });

        // sms_gateways
        Schema::table('sms_gateways', function (Blueprint $table) {
            $table->foreignId('new_id')->default(0)->nullable()->change();
        });
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
