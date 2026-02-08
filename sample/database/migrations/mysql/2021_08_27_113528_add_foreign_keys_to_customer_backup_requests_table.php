<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCustomerBackupRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_backup_requests', function (Blueprint $table) {
            $table->foreign('backup_setting_id')->references('id')->on('backup_settings')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_backup_requests', function (Blueprint $table) {
            $table->dropForeign('customer_backup_requests_backup_setting_id_foreign');
        });
    }
}
