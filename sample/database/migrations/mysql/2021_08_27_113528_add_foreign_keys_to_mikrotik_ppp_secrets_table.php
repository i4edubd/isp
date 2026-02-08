<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToMikrotikPppSecretsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mikrotik_ppp_secrets', function (Blueprint $table) {
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
        Schema::table('mikrotik_ppp_secrets', function (Blueprint $table) {
            $table->dropForeign('mikrotik_ppp_secrets_operator_id_foreign');
        });
    }
}
