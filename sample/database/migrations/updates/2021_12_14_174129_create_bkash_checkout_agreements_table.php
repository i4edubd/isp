<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBkashCheckoutAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bkash_checkout_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mgid');
            $table->foreignId('operator_id');
            $table->foreignId('customer_id')->nullable();
            $table->enum('role', ['operator', 'customer']);
            $table->enum('payment_type', ['internet', 'sms', 'subscription']);
            $table->foreignId('payment_id');
            $table->string('payer_reference')->nullable();
            $table->string('bkash_payment_id')->nullable();
            $table->string('agreement_id')->nullable();
            $table->string('customer_msisdn')->nullable();
            $table->string('agreement_status')->nullable();
            $table->timestamps();
        });

        Schema::table('bkash_checkout_agreements', function (Blueprint $table) {
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
        Schema::dropIfExists('bkash_checkout_agreements');
    }
}
