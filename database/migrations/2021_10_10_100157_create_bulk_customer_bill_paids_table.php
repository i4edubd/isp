<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBulkCustomerBillPaidsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bulk_customer_bill_paids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_id');
            $table->foreignId('customer_bill_id');
            $table->string('amount');
            $table->string('operator_amount');
            $table->timestamps();
        });

        Schema::table('bulk_customer_bill_paids', function (Blueprint $table) {
            $table->foreign('requester_id')->references('id')->on('operators')->onDelete('CASCADE');
            $table->foreign('customer_bill_id')->references('id')->on('customer_bills')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bulk_customer_bill_paids');
    }
}
