<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToComplainLedgersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('complain_ledgers', function (Blueprint $table) {
            $table->foreign('complain_id')->references('id')->on('customer_complains')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('complain_ledgers', function (Blueprint $table) {
            $table->dropForeign('complain_ledgers_complain_id_foreign');
        });
    }
}
