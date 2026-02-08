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
        Schema::create('other_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mgid');
            $table->foreignId('gid');
            $table->foreignId('operator_id');
            $table->foreignId('msid')->default(0);
            $table->foreignId('psid')->default(0);
            $table->text('name');
            $table->string('operator_price')->default('0');
            $table->integer('price')->default(0);
            $table->integer('validity')->default(30);
            $table->timestamps();
        });

        Schema::table('other_services', function (Blueprint $table) {
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
        Schema::dropIfExists('other_services');
    }
};
