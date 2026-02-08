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
        Schema::create('vat_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vat_profile_id');
            $table->foreignId('customer_payment_id');
            $table->foreignId('mgid');
            $table->foreignId('operator_id');
            $table->string('amount');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::table('vat_collections', function (Blueprint $table) {
            $table->foreign(['vat_profile_id'])->references(['id'])->on('vat_profiles')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['customer_payment_id'])->references(['id'])->on('customer_payments')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['mgid'])->references(['id'])->on('operators')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['operator_id'])->references(['id'])->on('operators')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vat_collections');
    }
};
