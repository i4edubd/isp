<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePppoeProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pppoe_profiles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('new_id')->default(0);
            $table->unsignedBigInteger('mgid')->index('pppoe_profiles_mgid_foreign');
            $table->string('name', 128)->index();
            $table->unsignedBigInteger('ipv4pool_id');
            $table->unsignedBigInteger('ipv6pool_id')->default(0);
            $table->enum('ip_allocation_mode', ['dynamic', 'static'])->default('static');
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
        Schema::dropIfExists('pppoe_profiles');
    }
}
