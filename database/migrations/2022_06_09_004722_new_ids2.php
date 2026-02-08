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
        // operator_permissions
        Schema::table('operator_permissions', function (Blueprint $table) {
            $table->foreignId('new_id')->default(0)->after('id');
        });

        // sms_gateways
        Schema::table('sms_gateways', function (Blueprint $table) {
            $table->foreignId('new_id')->default(0)->after('id');
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
