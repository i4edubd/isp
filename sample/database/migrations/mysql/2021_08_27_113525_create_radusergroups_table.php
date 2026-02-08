<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRadusergroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('radusergroups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('mgid');
            $table->string('username', 64)->index('radusergroups_username_foreign');
            $table->string('groupname', 64);
            $table->integer('priority')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('radusergroups');
    }
}
