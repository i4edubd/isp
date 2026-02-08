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
        Schema::create('mandatory_customers_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mgid');
            $table->string('attribute');
            $table->timestamps();
        });

        Schema::table('mandatory_customers_attributes', function (Blueprint $table) {
            $table->foreign(['mgid'])->references(['id'])->on('operators')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mandatory_customers_attributes');
    }
};
