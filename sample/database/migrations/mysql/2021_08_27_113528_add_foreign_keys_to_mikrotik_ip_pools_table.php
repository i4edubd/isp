<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToMikrotikIpPoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mikrotik_ip_pools', function (Blueprint $table) {
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
        Schema::table('mikrotik_ip_pools', function (Blueprint $table) {
            $table->dropForeign('mikrotik_ip_pools_operator_id_foreign');
        });
    }
}
