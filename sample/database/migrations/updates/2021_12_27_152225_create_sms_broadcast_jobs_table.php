<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsBroadcastJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_broadcast_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id');
            $table->json('filter')->nullable();
            $table->string('message');
            $table->integer('message_count')->default(0);
            $table->enum('status', ['initiated', 'running', 'completed'])->default('initiated');
            $table->timestamps();
        });

        Schema::table('sms_broadcast_jobs', function (Blueprint $table) {
            $table->foreign('operator_id')->references('id')->on('operators')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_broadcast_jobs');
    }
}
