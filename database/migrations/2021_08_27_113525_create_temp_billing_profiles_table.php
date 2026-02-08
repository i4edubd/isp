<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempBillingProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_billing_profiles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('mgid')->index('temp_billing_profiles_mgid_foreign');
            $table->enum('profile_for', ['daily_billing', 'monthly_billing', 'free_customer']);
            $table->enum('partial_payment', ['yes', 'no'])->nullable();
            $table->tinyInteger('minimum_validity')->default(0);
            $table->enum('cycle_ends_with_month', ['yes', 'no'])->nullable();
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
        Schema::dropIfExists('temp_billing_profiles');
    }
}
