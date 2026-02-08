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
        Schema::table('radaccts', function (Blueprint $table) {
            $table->timestamp('start_time')->useCurrent()->after('acctstarttime');
        });
        Schema::connection('pgsql')->table('pgsql_radaccts', function (Blueprint $table) {
            $table->timestamp('start_time')->useCurrent()->after('acctstarttime');
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
