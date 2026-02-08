<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsVsPaymentsChartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pgsql')->create('bills_vs_payments_charts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id');
            $table->foreignId('source_operator_id');
            $table->string('source_operator_name');
            $table->enum('topic', ['bill', 'payment']);
            $table->string('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bills_vs_payments_charts');
    }
}
