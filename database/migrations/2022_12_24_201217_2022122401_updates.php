<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('telegraph_chats', function (Blueprint $table) {
            $table->foreignId('operator_id')->nullable()->after('id');
        });

        Schema::table('telegraph_chats', function (Blueprint $table) {
            $table->foreign(['operator_id'])->references(['id'])->on('operators')->onUpdate('NO ACTION')->onDelete('CASCADE');
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
};
