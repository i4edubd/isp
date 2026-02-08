<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerImportReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_import_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('request_id');
            $table->unsignedBigInteger('mgid');
            $table->unsignedBigInteger('operator_id')->index('customer_import_reports_operator_id_foreign');
            $table->unsignedBigInteger('nas_id');
            $table->enum('menu', ['ipv4pool', 'pppoe_profile', 'customer'])->nullable();
            $table->text('name');
            $table->enum('status', ['success', 'failed'])->nullable();
            $table->text('comment')->nullable();
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
        Schema::dropIfExists('customer_import_reports');
    }
}
