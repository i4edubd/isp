<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PackageChangeHistoriesUpdate1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_change_histories', function (Blueprint $table) {
            $table->enum('changed_by', ['operator', 'customer'])->nullable()->after('customer_id');
            $table->foreignId('changer_id')->nullable()->after('changed_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
