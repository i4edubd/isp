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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('iso2', 2);
            $table->string('iso3', 3);
            $table->string('phone_code', 5);
            $table->string('timezone', 32)->nullable();
            $table->string('currency_name', 32)->nullable();
            $table->string('currency_code', 32)->nullable();
            $table->string('currency_symbol', 32)->nullable();
            $table->string('currency_symbol_native', 32)->nullable();
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
        Schema::dropIfExists('countries');
    }
};
