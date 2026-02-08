<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCustomerComplainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_complains', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('complain_categories')->onDelete('CASCADE');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('CASCADE');
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
        Schema::table('customer_complains', function (Blueprint $table) {
            $table->dropForeign('customer_complains_category_id_foreign');
            $table->dropForeign('customer_complains_department_id_foreign');
            $table->dropForeign('customer_complains_operator_id_foreign');
        });
    }
}
