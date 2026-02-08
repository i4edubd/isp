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
        Schema::table('customer_payments', function (Blueprint $table) {
            $table->foreignId('batch_id')->default(0)->after('new_id');
            $table->foreignId('parent_customer_id')->default(0)->after('cash_collector_id');
            $table->string('currency', 16)->nullable()->after('pay_status');
            $table->string('purpose_of_payment')->nullable()->after('note');
            $table->text('billing_period')->nullable()->after('purpose_of_payment');
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
