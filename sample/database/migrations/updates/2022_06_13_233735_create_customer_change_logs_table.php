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
        Schema::create('customer_change_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gid')->default(0);
            $table->foreignId('operator_id')->default(0);
            $table->foreignId('customer_id')->default(0);
            $table->string('changed_by')->nullable();
            $table->string('topic')->nullable();
            $table->longText('change_log')->nullable();
            $table->timestamps();
        });

        Schema::table('customer_change_logs', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_change_logs');
    }
};
