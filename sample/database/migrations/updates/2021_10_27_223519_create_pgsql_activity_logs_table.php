<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePgsqlActivityLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pgsql')->create('pgsql_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gid')->index();
            $table->foreignId('operator_id')->index();
            $table->foreignId('customer_id')->nullable();
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
        Schema::dropIfExists('pgsql_activity_logs');
    }
}
