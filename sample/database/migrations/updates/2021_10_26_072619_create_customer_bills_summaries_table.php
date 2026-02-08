<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerBillsSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_bills_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id');
            $table->enum('type', ['direct', 'resell', 'sub_resell', 'to_operator', 'to_group_admin']);
            $table->foreignId('reseller_id')->nullable();
            $table->foreignId('sub_reseller_id')->nullable();
            $table->foreignId('package_id');
            $table->integer('bill_count');
            $table->string('package_price');
            $table->string('subtotal');
            $table->timestamps();
        });

        Schema::table('customer_bills_summaries', function (Blueprint $table) {
            $table->foreign('operator_id')->references('id')->on('operators')->onDelete('CASCADE');
            $table->foreign('reseller_id')->references('id')->on('operators')->onDelete('CASCADE');
            $table->foreign('sub_reseller_id')->references('id')->on('operators')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_bills_summaries');
    }
}
