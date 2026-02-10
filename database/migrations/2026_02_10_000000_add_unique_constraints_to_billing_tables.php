<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customer_bills', function (Blueprint $table) {
            $table->unique(['customer_id', 'billing_month', 'billing_year']);
        });

        Schema::table('customer_payments', function (Blueprint $table) {
            $table->string('transaction_id')->nullable()->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_bills', function (Blueprint $table) {
            $table->dropUnique(['customer_id', 'billing_month', 'billing_year']);
        });

        Schema::table('customer_payments', function (Blueprint $table) {
            $table->dropUnique(['transaction_id']);
        });
    }
};
