<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMikrotikIpPoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mikrotik_ip_pools', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('customer_import_request_id')->nullable();
            $table->unsignedBigInteger('mgid');
            $table->unsignedBigInteger('operator_id')->index('mikrotik_ip_pools_operator_id_foreign');
            $table->unsignedBigInteger('nas_id');
            $table->unsignedBigInteger('ipv4pool_id')->default(0);
            $table->string('name', 128);
            $table->text('ranges');
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
        Schema::dropIfExists('mikrotik_ip_pools');
    }
}
