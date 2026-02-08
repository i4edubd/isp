<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_bills', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sid');
            $table->unsignedBigInteger('mgid')->index('subscription_bills_mgid_foreign');
            $table->string('operator_name');
            $table->string('operator_email');
            $table->integer('user_count');
            $table->integer('amount');
            $table->string('calculated_price')->nullable();
            $table->string('month', 16);
            $table->string('year', 16);
            $table->string('due_date', 16)->nullable();
            $table->enum('status', ['paid', 'unpaid'])->default('unpaid');
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
        Schema::dropIfExists('subscription_bills');
    }
}
