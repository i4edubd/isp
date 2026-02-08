<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIpv6poolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ipv6pools', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('mgid')->index('ipv6pools_mgid_foreign');
            $table->string('name', 128)->index();
            $table->string('prefix', 128);
            $table->string('lowest_address', 128);
            $table->string('highest_address', 128);
            $table->string('prefix_length')->default('64');
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
        Schema::dropIfExists('ipv6pools');
    }
}
