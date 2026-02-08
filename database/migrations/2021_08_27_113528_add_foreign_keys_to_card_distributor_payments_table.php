<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCardDistributorPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('card_distributor_payments', function (Blueprint $table) {
            $table->foreign('card_distributor_id')->references('id')->on('card_distributors')->onDelete('CASCADE');
            $table->foreign('operator_id')->references('id')->on('operators')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('card_distributor_payments', function (Blueprint $table) {
            $table->dropForeign('card_distributor_payments_card_distributor_id_foreign');
            $table->dropForeign('card_distributor_payments_operator_id_foreign');
        });
    }
}
