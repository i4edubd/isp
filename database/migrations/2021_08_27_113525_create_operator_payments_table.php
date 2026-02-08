<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperatorPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operator_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('mgid')->default(0);
            $table->unsignedBigInteger('operator_id')->index('operator_payments_operator_id_index');
            $table->unsignedBigInteger('cash_collector_id')->default(0);
            $table->unsignedBigInteger('payment_gateway_id')->nullable();
            $table->string('payment_gateway_name')->nullable();
            $table->string('mobile', 64)->nullable();
            $table->string('username', 64)->nullable();
            $table->string('type', 16)->default('Cash');
            $table->enum('pay_status', ['Pending', 'Failed', 'Successful'])->default('Pending');
            $table->string('amount_paid', 32)->default('0');
            $table->string('store_amount', 32)->default('0');
            $table->string('transaction_fee', 32)->default('0');
            $table->string('discount', 16)->default('0');
            $table->string('vat_paid', 16)->nullable();
            $table->string('first_party', 32)->nullable();
            $table->string('second_party', 32)->nullable();
            $table->string('third_party', 32)->nullable();
            $table->text('pgw_payment_identifier')->nullable();
            $table->text('mer_txnid')->nullable();
            $table->text('pgw_txnid')->nullable();
            $table->text('bank_txnid')->nullable();
            $table->string('card_type')->nullable();
            $table->string('card_number')->nullable();
            $table->text('payment_token')->nullable();
            $table->text('note')->nullable();
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
        Schema::dropIfExists('operator_payments');
    }
}
