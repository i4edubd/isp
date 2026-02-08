<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplainLedgersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('complain_ledgers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('complain_id')->index('complain_ledgers_complain_id_foreign');
            $table->unsignedBigInteger('operator_id');
            $table->enum('topic', ['category', 'department', 'acknowledge', 'done', 'comment', 'status'])->nullable();
            $table->unsignedBigInteger('fdid')->nullable();
            $table->unsignedBigInteger('fcid')->nullable();
            $table->unsignedBigInteger('tdid')->nullable();
            $table->unsignedBigInteger('tcid')->nullable();
            $table->unsignedBigInteger('comment_id')->nullable();
            $table->text('from_status')->nullable();
            $table->text('to_status')->nullable();
            $table->text('start_time')->nullable();
            $table->text('comment')->nullable();
            $table->text('diff_in_seconds')->nullable();
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
        Schema::dropIfExists('complain_ledgers');
    }
}
