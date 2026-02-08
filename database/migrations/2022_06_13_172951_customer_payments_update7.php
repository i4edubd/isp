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
        Schema::table('customer_payments', function (Blueprint $table) {
            $table->string('vat_paid', 16)->default(0)->nullable()->after('discount');
            $table->string('first_party', 32)->default(0)->nullable()->after('vat_paid');
            $table->string('second_party', 32)->default(0)->nullable()->after('first_party');
            $table->string('third_party', 32)->default(0)->nullable()->after('second_party');
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
