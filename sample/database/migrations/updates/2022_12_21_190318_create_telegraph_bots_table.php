<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('telegraph_bots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->nullable();
            $table->string('token')->unique();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        Schema::table('telegraph_bots', function (Blueprint $table) {
            $table->foreign(['operator_id'])->references(['id'])->on('operators')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }
};
