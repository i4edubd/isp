<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCustomPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('custom_prices', function (Blueprint $table) {
            $table->foreign('mgid')->references('id')->on('operators')->onDelete('CASCADE');
            $table->foreign('operator_id')->references('id')->on('operators')->onDelete('CASCADE');
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('custom_prices', function (Blueprint $table) {
            $table->dropForeign('custom_prices_mgid_foreign');
            $table->dropForeign('custom_prices_operator_id_foreign');
            $table->dropForeign('custom_prices_package_id_foreign');
        });
    }
}
