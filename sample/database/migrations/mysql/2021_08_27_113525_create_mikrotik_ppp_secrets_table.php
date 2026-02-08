<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMikrotikPppSecretsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mikrotik_ppp_secrets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('customer_import_request_id')->nullable();
            $table->unsignedBigInteger('mgid');
            $table->unsignedBigInteger('operator_id')->index('mikrotik_ppp_secrets_operator_id_foreign');
            $table->unsignedBigInteger('nas_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('name', 128);
            $table->string('password', 128);
            $table->string('profile', 128);
            $table->text('comment')->nullable();
            $table->string('disabled', 16)->nullable();
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
        Schema::dropIfExists('mikrotik_ppp_secrets');
    }
}
