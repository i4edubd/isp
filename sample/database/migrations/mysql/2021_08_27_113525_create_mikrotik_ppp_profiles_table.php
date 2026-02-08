<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMikrotikPppProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mikrotik_ppp_profiles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('customer_import_request_id')->nullable();
            $table->unsignedBigInteger('mgid');
            $table->unsignedBigInteger('operator_id')->index('mikrotik_ppp_profiles_operator_id_foreign');
            $table->unsignedBigInteger('nas_id');
            $table->unsignedBigInteger('pppoe_profile_id')->default(0);
            $table->unsignedBigInteger('package_id')->default(0);
            $table->string('name', 128);
            $table->string('local_address', 128)->nullable();
            $table->string('remote_address', 128)->nullable();
            $table->string('rate_limit', 128)->nullable();
            $table->text('dns_server')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mikrotik_ppp_profiles');
    }
}
