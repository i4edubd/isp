<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNasPppoeProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nas_pppoe_profile', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('nas_id');
            $table->unsignedBigInteger('pppoe_profile_id')->index('nas_pppoe_profile_pppoe_profile_id_foreign');
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
        Schema::dropIfExists('nas_pppoe_profile');
    }
}
