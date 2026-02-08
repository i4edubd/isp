<?php

use App\Models\billing_profile;
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
        Schema::table('billing_profiles', function (Blueprint $table) {
            $table->enum('billing_type', ['Daily', 'Monthly', 'Free'])->default('Monthly')->after('mgid');
        });

        Schema::table('billing_profiles', function (Blueprint $table) {
            $table->dropColumn('partial_payment');
        });

        Schema::table('temp_billing_profiles', function (Blueprint $table) {
            $table->dropColumn('partial_payment');
        });

        if (config('local.host_type') == 'central') {
            billing_profile::where('profile_name', 'Daily Billing')->update(['billing_type' => 'Daily']);
            billing_profile::where('profile_name', 'Free Customer')->update(['billing_type' => 'Free']);
        }
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
