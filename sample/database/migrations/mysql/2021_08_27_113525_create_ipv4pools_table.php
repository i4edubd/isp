<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIpv4poolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ipv4pools', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('new_id')->default(0);
            $table->unsignedBigInteger('mgid')->index('ipv4pools_mgid_foreign');
            $table->string('name', 128)->index();
            $table->string('subnet', 64);
            $table->string('mask', 32);
            $table->string('gateway', 64);
            $table->string('broadcast', 64);
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
        Schema::dropIfExists('ipv4pools');
    }
}
