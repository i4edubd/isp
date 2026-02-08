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
        Schema::table('card_distributors', function (Blueprint $table) {
            $table->enum('account_type', ['prepaid', 'postpaid'])->default('postpaid')->after('commission');
            $table->string('timezone', 16)->nullable()->after('email');
            $table->string('lang_code', 4)->nullable()->after('timezone');
            $table->timestamp('email_verified_at')->nullable()->after('email');
            $table->tinyInteger('webauthn_enabled')->default(0)->after('password');
            $table->tinyInteger('two_factor_activated')->default(0)->after('password');
            $table->tinyInteger('two_factor_challenge_due')->default(0)->after('password');
            $table->text('two_factor_secret')->nullable()->after('password');
            $table->text('two_factor_recovery_codes')->nullable()->after('password');
            $table->tinyInteger('device_identification_enabled')->default(0)->after('password');
            $table->tinyInteger('device_identification_pending')->default(0)->after('password');
            $table->rememberToken()->after('password');
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
