<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NasPppoeProfileUpdate1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nas_pppoe_profile', function (Blueprint $table) {
            $table->foreignId('mgid')->nullable()->after('id');
        });

        Schema::table('nas_pppoe_profile', function (Blueprint $table) {
            $table->foreign('mgid')->references('id')->on('operators')->onDelete('CASCADE');
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
}
