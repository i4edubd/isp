<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFairUsagePoliciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fair_usage_policies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('mgid');
            $table->unsignedBigInteger('master_package_id')->index('fair_usage_policies_master_package_id_foreign');
            $table->integer('data_limit')->default(0);
            $table->integer('speed_limit')->default(0);
            $table->unsignedBigInteger('ipv4pool_id')->nullable();
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
        Schema::dropIfExists('fair_usage_policies');
    }
}
