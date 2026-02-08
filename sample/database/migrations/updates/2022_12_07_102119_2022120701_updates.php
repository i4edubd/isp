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
        Schema::table('customer_bills', function (Blueprint $table) {
            $table->renameColumn('batch_id', 'customer_payment_id');
            $table->unsignedBigInteger('mgid')->default(0)->change();
        });

        Schema::table('customer_payments', function (Blueprint $table) {
            $table->dropColumn('batch_id');
            $table->unsignedBigInteger('mgid')->default(0)->change();
        });

        Schema::dropIfExists('batch_bill_payments');
        Schema::dropIfExists('groups');
        Schema::dropIfExists('group_members');
        Schema::dropIfExists('isp_informations');
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
