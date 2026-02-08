<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_prices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('mgid')->index('custom_prices_mgid_foreign');
            $table->unsignedBigInteger('operator_id')->index('custom_prices_operator_id_foreign');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('package_id')->index('custom_prices_package_id_foreign');
            $table->string('price', 32)->default('0');
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
        Schema::dropIfExists('custom_prices');
    }
}
