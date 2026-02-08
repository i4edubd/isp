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
        Schema::table('sms_gateways', function (Blueprint $table) {
            $table->dropColumn('country_id');
            $table->string('country_code', 8)->default('BD')->after('operator_id');
            $table->longText('token')->nullable()->after('provider_name');
            $table->string('email')->nullable()->after('username');
        });

        Schema::table('payment_gateways', function (Blueprint $table) {
            $table->dropColumn('country_id');
            $table->string('country_code', 8)->default('BD')->after('operator_id');
            $table->longText('token')->nullable()->after('payment_method');
            $table->string('email')->nullable()->after('username');
            $table->longText('ssh_key')->nullable()->after('password');
            $table->longText('gpg_key')->nullable()->after('ssh_key');
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
