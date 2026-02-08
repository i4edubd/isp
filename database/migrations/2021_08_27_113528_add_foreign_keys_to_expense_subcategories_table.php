<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToExpenseSubcategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expense_subcategories', function (Blueprint $table) {
            $table->foreign('expense_category_id')->references('id')->on('expense_categories')->onDelete('CASCADE');
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
        Schema::table('expense_subcategories', function (Blueprint $table) {
            $table->dropForeign('expense_subcategories_expense_category_id_foreign');
            $table->dropForeign('expense_subcategories_operator_id_foreign');
        });
    }
}
