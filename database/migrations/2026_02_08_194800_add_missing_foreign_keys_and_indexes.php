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
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->unique(['customer_id', 'billing_period']);
        });

        Schema::table('customer_payments', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('customer_bill_id')->references('id')->on('customer_bills')->onDelete('cascade');
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
            $table->unique('pgw_txnid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_bills', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropUnique(['customer_id', 'billing_period']);
        });

        Schema::table('customer_payments', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['customer_bill_id']);
            $table->dropForeign(['package_id']);
            $table->dropUnique(['pgw_txnid']);
        });
    }
};