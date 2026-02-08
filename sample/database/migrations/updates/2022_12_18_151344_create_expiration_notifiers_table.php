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
        Schema::create('expiration_notifiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id');
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->json('connection_types');
            $table->json('billing_types');
            $table->integer('notify_before')->default(1);
            $table->enum('unit', ['Day', 'Hour'])->default('Day');
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
        Schema::dropIfExists('expiration_notifiers');
    }
};
