<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToOperatorPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('operator_permissions', function (Blueprint $table) {
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
        Schema::table('operator_permissions', function (Blueprint $table) {
            $table->dropForeign('operator_permissions_operator_id_foreign');
        });
    }
}
