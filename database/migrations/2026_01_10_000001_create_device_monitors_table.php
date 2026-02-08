<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceMonitorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_monitors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('operator_id');
            $table->unsignedBigInteger('gid');
            $table->string('device_type', 32); // ap, host, mikrotik
            $table->string('device_name', 255);
            $table->string('ip_address', 45);
            $table->string('monitor_method', 16)->default('ping'); // ping, snmp
            $table->integer('port')->nullable();
            $table->string('snmp_community', 64)->nullable();
            $table->string('status', 16)->default('unknown'); // up, down, unknown
            $table->timestamp('last_checked_at')->nullable();
            $table->integer('response_time')->nullable(); // in milliseconds
            $table->text('last_error')->nullable();
            $table->timestamps();

            $table->foreign('operator_id')->references('id')->on('operators')->onDelete('CASCADE');
            $table->foreign('gid')->references('id')->on('operators')->onDelete('CASCADE');
            $table->index(['operator_id', 'status']);
            $table->index(['gid', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('device_monitors');
    }
}
