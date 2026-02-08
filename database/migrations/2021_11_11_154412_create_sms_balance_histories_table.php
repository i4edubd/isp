<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsBalanceHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_balance_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id');
            $table->enum('type', ['in', 'out']);
            $table->foreignId('sms_bill_id')->default(0);
            $table->foreignId('sms_payment_id')->default(0);
            $table->string('amount');
            $table->string('old_balance');
            $table->string('new_balance');
            $table->timestamps();
        });

        Schema::table('sms_balance_histories', function (Blueprint $table) {
            $table->foreign('operator_id')->references('id')->on('operators')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_balance_histories');
    }
}
