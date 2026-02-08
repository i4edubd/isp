<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToFairUsagePoliciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fair_usage_policies', function (Blueprint $table) {
            $table->foreign('master_package_id')->references('id')->on('master_packages')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fair_usage_policies', function (Blueprint $table) {
            $table->dropForeign('fair_usage_policies_master_package_id_foreign');
        });
    }
}
