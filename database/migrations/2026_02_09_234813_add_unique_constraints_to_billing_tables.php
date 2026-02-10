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
            // Prevent duplicate bills for the same customer, package, and billing period.
            $table->unique(['customer_id', 'package_id', 'year', 'month'], 'customer_bill_period_unique');
        });

        Schema::table('customer_payments', function (Blueprint $table) {
            // Prevent duplicate payment records from the same payment gateway.
            $table->unique(['payment_gateway_id', 'pgw_txnid'], 'customer_payment_gateway_unique');

            // Prevent duplicate internal transaction records.
            $table->unique('mer_txnid', 'customer_payment_internal_txn_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_bills', function (Blueprint $table) {
            $table->dropUnique('customer_bill_period_unique');
        });

        Schema::table('customer_payments', function (Blueprint $table) {
            $table->dropUnique('customer_payment_gateway_unique');
            $table->dropUnique('customer_payment_internal_txn_unique');
        });
    }
};