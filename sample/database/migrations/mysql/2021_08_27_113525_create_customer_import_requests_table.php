<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerImportRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_import_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('connection_type', ['PPPoE', 'Hotspot'])->default('PPPoE');
            $table->enum('import_disabled_user', ['yes', 'no'])->default('no');
            $table->unsignedBigInteger('mgid');
            $table->unsignedBigInteger('operator_id')->index('customer_import_requests_operator_id_foreign');
            $table->unsignedBigInteger('nas_id');
            $table->unsignedBigInteger('billing_profile_id');
            $table->string('generate_bill')->default('no');
            $table->integer('validity')->default(30);
            $table->enum('status', ['processing', 'done'])->default('processing');
            $table->string('date');
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
        Schema::dropIfExists('customer_import_requests');
    }
}
