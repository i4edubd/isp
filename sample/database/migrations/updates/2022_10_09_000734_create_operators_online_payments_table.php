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
        Schema::create('operators_online_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id');
            $table->foreignId('account_id');
            $table->enum('payment_purpose', ['cash_in', 'cash_out']);
            $table->enum('pay_status', ['Pending', 'Failed', 'Successful'])->default('Pending');
            $table->string('currency', 16)->nullable();
            $table->string('amount_paid', 32)->default('0');
            $table->string('store_amount', 32)->default('0');
            $table->string('transaction_fee', 32)->default('0');
            $table->text('pgw_payment_identifier')->nullable();
            $table->text('mer_txnid');
            $table->text('pgw_txnid')->nullable();
            $table->text('bank_txnid')->nullable();
            $table->string('card_type')->nullable();
            $table->string('card_number')->nullable();
            $table->text('payment_token')->nullable();
            $table->string('date', 16);
            $table->string('month', 16);
            $table->string('year', 16);
            $table->tinyInteger('used')->default(0);
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::table('operators_online_payments', function (Blueprint $table) {
            $table->foreign(['operator_id'])->references(['id'])->on('operators')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['account_id'])->references(['id'])->on('accounts')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operators_online_payments');
    }
};
