<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerBackupRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_backup_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('mgid')->index('customer_backup_requests_gid_foreign');
            $table->unsignedBigInteger('backup_setting_id')->index('customer_backup_requests_backup_setting_id_foreign');
            $table->string('status')->default('Processing');
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
        Schema::dropIfExists('customer_backup_requests');
    }
}
