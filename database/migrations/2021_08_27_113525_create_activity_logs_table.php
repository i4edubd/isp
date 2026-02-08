<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('gid')->index('activity_logs_gid_foreign');
            $table->unsignedBigInteger('operator_id')->index('activity_logs_operator_id_foreign');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('topic', 64)->nullable();
            $table->string('year', 16);
            $table->string('month', 16);
            $table->string('week', 16);
            $table->longText('log')->nullable();
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
        Schema::dropIfExists('activity_logs');
    }
}
