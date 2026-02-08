<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToNasPppoeProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nas_pppoe_profile', function (Blueprint $table) {
            $table->foreign('pppoe_profile_id')->references('id')->on('pppoe_profiles')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nas_pppoe_profile', function (Blueprint $table) {
            $table->dropForeign('nas_pppoe_profile_pppoe_profile_id_foreign');
        });
    }
}
