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
        Schema::create('vat_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mgid');
            $table->string('description');
            $table->string('identification_number');
            $table->string('rate');
            $table->enum('status', ['enabled', 'disabled']);
            $table->timestamps();
        });

        Schema::table('vat_profiles', function (Blueprint $table) {
            $table->foreign(['mgid'])->references(['id'])->on('operators')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vat_profiles');
    }
};
