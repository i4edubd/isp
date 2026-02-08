<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIspInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('isp_informations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('operator_id')->index('isp_informations_operator_id_foreign');
            $table->text('isp_name');
            $table->string('isp_contact', 16);
            $table->string('isp_email', 64)->nullable();
            $table->string('isp_web_site', 64)->nullable();
            $table->string('isp_house_number', 64)->nullable();
            $table->string('isp_streetname_number', 64)->nullable();
            $table->string('isp_district', 64)->nullable();
            $table->string('isp_postal_code', 64)->nullable();
            $table->string('isp_logo', 128)->nullable();
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
        Schema::dropIfExists('isp_informations');
    }
}
