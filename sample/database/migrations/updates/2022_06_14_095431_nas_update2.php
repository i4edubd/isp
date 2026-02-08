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
            $table->enum('api_status', ['OK', 'Failed'])->default('OK')->after('overwrite_comment');
            $table->string('api_last_check', 32)->nullable()->after('api_status');
            $table->enum('identity_status', ['correct', 'incorrect'])->default('correct')->after('api_last_check');
        });

        Schema::connection('pgsql')->table('pgsql_nas', function (Blueprint $table) {
            $table->enum('api_status', ['OK', 'Failed'])->default('OK')->after('overwrite_comment');
            $table->string('api_last_check', 32)->nullable()->after('api_status');
            $table->enum('identity_status', ['correct', 'incorrect'])->default('correct')->after('api_last_check');
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
