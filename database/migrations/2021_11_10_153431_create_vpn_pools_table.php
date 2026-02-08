<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVpnPoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vpn_pools', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['client', 'server']);
            $table->string('subnet', 64);
            $table->string('mask', 32);
            $table->string('gateway', 64);
            $table->string('broadcast', 64);
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
        Schema::dropIfExists('vpn_pools');
    }
}
