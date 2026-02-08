<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PgsqlForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        #radaccts
        Schema::connection('pgsql')->table('pgsql_radaccts', function ($table) {
            $table->foreign('username')->references('username')->on('pgsql_customers')->onDelete('cascade')->onUpdate('cascade');
        });

        #radchecks
        Schema::connection('pgsql')->table('pgsql_radchecks', function ($table) {
            $table->foreign('username')->references('username')->on('pgsql_customers')->onDelete('cascade')->onUpdate('cascade');
        });

        #radpostauths
        Schema::connection('pgsql')->table('pgsql_radpostauths', function ($table) {
            $table->foreign('username')->references('username')->on('pgsql_customers')->onDelete('cascade');
        });

        #radreplies
        Schema::connection('pgsql')->table('pgsql_radreplies', function ($table) {
            $table->foreign('username')->references('username')->on('pgsql_customers')->onDelete('cascade')->onUpdate('cascade');
        });

        #radusergroups
        Schema::connection('pgsql')->table('pgsql_radusergroups', function ($table) {
            $table->foreign('username')->references('username')->on('pgsql_customers')->onDelete('cascade')->onUpdate('cascade');
        });

        // pgsql_radacct_histories
        Schema::connection('pgsql')->table('pgsql_radacct_histories', function ($table) {
            $table->foreign('username')->references('username')->on('pgsql_customers')->onDelete('cascade')->onUpdate('cascade');
        });

        // pgsql_customer_custom_attributes
        Schema::connection('pgsql')->table('pgsql_customer_custom_attributes', function ($table) {
            $table->foreign('customer_id')->references('id')->on('pgsql_customers')->onDelete('cascade');
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
