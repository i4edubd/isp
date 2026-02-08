<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerCountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_counts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('mgid')->index('customer_counts_mgid_foreign');
            $table->integer('customer_count');
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
        Schema::dropIfExists('customer_counts');
    }
}
