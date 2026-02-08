<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_bills', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('operator_id')->index('sms_bills_operator_id_foreign');
            $table->unsignedBigInteger('merchant_id');
            $table->string('sms_count', 8)->nullable();
            $table->string('sms_cost', 8)->nullable();
            $table->string('from_date', 16)->nullable();
            $table->string('to_date', 16)->nullable();
            $table->string('month', 16)->nullable();
            $table->string('year', 16)->nullable();
            $table->string('due_date', 16)->nullable();
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
        Schema::dropIfExists('sms_bills');
    }
}
