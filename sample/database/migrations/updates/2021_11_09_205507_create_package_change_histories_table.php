<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageChangeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_change_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_payment_id')->default(0);
            $table->foreignId('operator_id');
            $table->foreignId('customer_id');
            $table->string('from_package');
            $table->string('to_package');
            $table->enum('status', ['attempt', 'success'])->default('attempt');
            $table->timestamps();
        });

        Schema::table('package_change_histories', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('package_change_histories');
    }
}
