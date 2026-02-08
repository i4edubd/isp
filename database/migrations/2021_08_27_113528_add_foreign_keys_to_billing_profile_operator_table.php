<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToBillingProfileOperatorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('billing_profile_operator', function (Blueprint $table) {
            $table->foreign('billing_profile_id')->references('id')->on('billing_profiles')->onDelete('CASCADE');
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
        Schema::table('billing_profile_operator', function (Blueprint $table) {
            $table->dropForeign('billing_profile_operator_billing_profile_id_foreign');
            $table->dropForeign('billing_profile_operator_operator_id_foreign');
        });
    }
}
