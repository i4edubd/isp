<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillingProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_profiles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('new_id')->default(0);
            $table->unsignedBigInteger('mgid')->index('billing_profiles_mgid_foreign');
            $table->string('partial_payment', 8)->default('no');
            $table->tinyInteger('minimum_validity')->default(0);
            $table->string('profile_name', 64)->nullable();
            $table->integer('billing_due_date')->nullable();
            $table->enum('auto_bill', ['yes', 'no'])->default('yes');
            $table->enum('auto_lock', ['yes', 'no'])->default('yes');
            $table->string('cycle_ends_with_month', 8)->default('no');
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
        Schema::dropIfExists('billing_profiles');
    }
}
