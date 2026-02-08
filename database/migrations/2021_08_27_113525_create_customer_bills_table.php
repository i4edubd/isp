<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_bills', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('mgid')->default(0);
            $table->unsignedBigInteger('gid');
            $table->unsignedBigInteger('operator_id')->index('customer_bills_operator_id_foreign');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('package_id')->index('customer_bills_package_id_foreign');
            $table->integer('validity_period')->default(0);
            $table->unsignedBigInteger('customer_zone_id')->nullable();
            $table->string('name')->nullable();
            $table->string('mobile')->nullable();
            $table->string('username', 64)->nullable();
            $table->string('amount', 16);
            $table->string('operator_amount', 16)->nullable();
            $table->text('description');
            $table->text('billing_period');
            $table->string('due_date', 16);
            $table->text('remark')->nullable();
            $table->string('year', 16);
            $table->string('month', 16);
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
        Schema::dropIfExists('customer_bills');
    }
}
