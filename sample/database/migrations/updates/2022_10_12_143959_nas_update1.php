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
        Schema::table('nas', function (Blueprint $table) {
            $table->integer('sstp_port')->default(443)->after('telnet_port');
        });

        Schema::connection('pgsql')->table('pgsql_nas', function (Blueprint $table) {
            $table->integer('sstp_port')->default(443)->after('telnet_port');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
