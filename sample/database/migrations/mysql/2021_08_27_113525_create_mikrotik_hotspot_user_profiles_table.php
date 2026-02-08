<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMikrotikHotspotUserProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mikrotik_hotspot_user_profiles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('customer_import_request_id')->nullable();
            $table->unsignedBigInteger('mgid');
            $table->unsignedBigInteger('operator_id')->index('mikrotik_hotspot_user_profiles_operator_id_foreign');
            $table->unsignedBigInteger('nas_id');
            $table->unsignedBigInteger('package_id')->nullable();
            $table->string('name', 128);
            $table->string('display_name', 128)->nullable();
            $table->string('address_pool', 128)->nullable();
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
        Schema::dropIfExists('mikrotik_hotspot_user_profiles');
    }
}
