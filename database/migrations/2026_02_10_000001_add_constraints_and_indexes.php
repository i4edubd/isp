<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // customers.username unique
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
                if (!Schema::hasColumn('customers', 'username')) {
                    return;
                }
                $table->unique('username', 'customers_username_unique');
            });
        }

        // radcheck username+attribute unique
        if (Schema::hasTable('radcheck')) {
            Schema::table('radcheck', function (Blueprint $table) {
                if (!Schema::hasColumn('radcheck', 'username') || !Schema::hasColumn('radcheck', 'attribute')) {
                    return;
                }
                $table->unique(['username', 'attribute'], 'radcheck_username_attribute_unique');
            });
        }

        // customer_bills unique per customer per month
        if (Schema::hasTable('customer_bills')) {
            Schema::table('customer_bills', function (Blueprint $table) {
                if (!Schema::hasColumn('customer_bills', 'customer_id') || !Schema::hasColumn('customer_bills', 'year') || !Schema::hasColumn('customer_bills', 'month')) {
                    return;
                }
                $table->unique(['customer_id', 'year', 'month'], 'customer_bills_customer_year_month_unique');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropUnique('customers_username_unique');
            });
        }

        if (Schema::hasTable('radcheck')) {
            Schema::table('radcheck', function (Blueprint $table) {
                $table->dropUnique('radcheck_username_attribute_unique');
            });
        }

        if (Schema::hasTable('customer_bills')) {
            Schema::table('customer_bills', function (Blueprint $table) {
                $table->dropUnique('customer_bills_customer_year_month_unique');
            });
        }
    }
};
