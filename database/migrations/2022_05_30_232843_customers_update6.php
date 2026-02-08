<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        DB::statement('ALTER TABLE `customers` CHANGE `connection_type` `connection_type` ENUM("PPPoE","Hotspot","StaticIp","Other") CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT "PPPoE"');

        Schema::table('customers', function (Blueprint $table) {
            $table->enum('ppp_billing_type', ['Daily', 'Monthly', 'Free'])->default('Monthly')->after('connection_type');
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
