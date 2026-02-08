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
        Schema::create('batch_bill_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mgid')->default(0);
            $table->foreignId('gid');
            $table->foreignId('operator_id');
            $table->foreignId('cash_collector_id')->nullable()->default('0');
            $table->foreignId('payment_gateway_id')->nullable()->default('0');
            $table->string('payment_gateway_name')->nullable();
            $table->enum('type', ['Cash', 'Online'])->default('Cash');
            $table->enum('pay_status', ['Pending', 'Failed', 'Successful'])->default('Pending');
            $table->string('amount_paid', 32)->default('0');
            $table->string('store_amount', 32)->default('0');
            $table->string('transaction_fee', 32)->default('0');
            $table->string('vat_paid', 16)->nullable()->default('0');
            $table->text('pgw_payment_identifier')->nullable();
            $table->text('mer_txnid');
            $table->text('pgw_txnid')->nullable();
            $table->text('bank_txnid')->nullable();
            $table->string('card_type')->nullable();
            $table->string('card_number')->nullable();
            $table->text('payment_token')->nullable();
            $table->tinyInteger('used')->default(0);
            $table->tinyInteger('require_sms_notice')->default(0);
            $table->tinyInteger('dnb_operator')->default(0);
            $table->tinyInteger('require_accounting')->default(1);
            $table->text('note')->nullable();
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
        Schema::dropIfExists('batch_bill_payments');
    }
};
