<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVpnAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vpn_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mgid');
            $table->foreignId('vpn_pool_id');
            $table->string('username');
            $table->string('password');
            $table->string('ip_address')->unique();
            $table->string('server_ip');
            $table->string('vpn_type')->default('SSTP');
            $table->timestamps();
        });

        Schema::table('vpn_accounts', function (Blueprint $table) {
            $table->foreign('mgid')->references('id')->on('operators')->onDelete('CASCADE');
        });

        Schema::table('vpn_accounts', function (Blueprint $table) {
            $table->foreign('vpn_pool_id')->references('id')->on('vpn_pools')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vpn_accounts');
    }
}
