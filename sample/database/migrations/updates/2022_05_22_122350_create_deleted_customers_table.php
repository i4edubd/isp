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
        Schema::create('deleted_customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('mgid')->default(0);
            $table->unsignedBigInteger('gid');
            $table->unsignedBigInteger('operator_id');
            $table->enum('connection_type', ['PPPoE', 'Hotspot', 'StaticIp'])->default('PPPoE');
            $table->unsignedBigInteger('zone_id')->nullable();
            $table->unsignedBigInteger('device_id')->nullable()->default(0);
            $table->string('name')->nullable();
            $table->string('mobile')->nullable()->unique();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('billing_profile_id')->nullable();
            $table->string('username', 64)->nullable()->unique();
            $table->string('password', 64)->nullable();
            $table->unsignedBigInteger('package_id')->default(0);
            $table->text('package_name')->nullable();
            $table->text('package_started_at')->nullable();
            $table->text('package_expired_at')->nullable();
            $table->string('status', 16)->default('active');
            $table->string('house_no')->nullable();
            $table->string('road_no')->nullable();
            $table->string('thana')->nullable();
            $table->string('district')->nullable();
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
        Schema::dropIfExists('deleted_customers');
    }
};
