<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillingProfileOperatorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_profile_operator', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('billing_profile_id')->index('billing_profile_operator_billing_profile_id_foreign');
            $table->unsignedBigInteger('operator_id')->index('billing_profile_operator_operator_id_foreign');
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
        Schema::dropIfExists('billing_profile_operator');
    }
}
