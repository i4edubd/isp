<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('operator_id')->index('sms_payments_operator_id_foreign');
            $table->unsignedBigInteger('merchant_id');
            $table->unsignedBigInteger('sms_bill_id')->nullable();
            $table->unsignedBigInteger('payment_gateway_id')->nullable();
            $table->string('payment_gateway_name')->nullable();
            $table->enum('type', ['Cash', 'Online'])->default('Cash');
            $table->enum('pay_status', ['Pending', 'Failed', 'Successful'])->default('Pending');
            $table->string('sms_count', 8)->nullable();
            $table->string('sms_cost', 8)->nullable();
            $table->string('amount_paid', 32);
            $table->string('store_amount', 32)->default('0');
            $table->string('transaction_fee', 32)->default('0');
            $table->text('pgw_payment_identifier')->nullable();
            $table->text('mer_txnid');
            $table->text('pgw_txnid')->nullable();
            $table->text('bank_txnid')->nullable();
            $table->string('date', 16);
            $table->string('week', 16);
            $table->string('month', 16);
            $table->string('year', 16);
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
        Schema::dropIfExists('sms_payments');
    }
}
