<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventSmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pgsql')->create('event_sms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id');
            $table->string('event', 128);
            $table->string('readable_event', 128);
            $table->longText('variables')->nullable();
            $table->longText('default_sms');
            $table->enum('status', ['enabled', 'disabled']);
            $table->longText('operator_sms')->nullable();
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
        Schema::dropIfExists('event_sms');
    }
}
