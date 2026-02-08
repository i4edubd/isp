<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerComplainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_complains', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('operator_id')->index('customer_complains_operator_id_foreign');
            $table->unsignedBigInteger('customer_id');
            $table->string('mobile')->nullable();
            $table->string('username', 64)->nullable();
            $table->unsignedBigInteger('category_id')->nullable()->index('customer_complains_category_id_foreign');
            $table->unsignedBigInteger('department_id')->nullable()->index('customer_complains_department_id_foreign');
            $table->tinyInteger('ack_status')->default(1);
            $table->unsignedBigInteger('ack_by')->nullable();
            $table->enum('requester', ['customer', 'operator'])->default('operator');
            $table->unsignedBigInteger('receiver_id')->nullable();
            $table->longText('message');
            $table->unsignedBigInteger('ledger_head')->default(0);
            $table->string('status', 16)->default('In Progress');
            $table->tinyInteger('is_active')->default(1);
            $table->text('attachment')->nullable();
            $table->text('start_date');
            $table->text('start_time');
            $table->text('stop_time')->nullable();
            $table->text('diff_in_seconds')->nullable();
            $table->text('week');
            $table->text('month');
            $table->text('year');
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
        Schema::dropIfExists('customer_complains');
    }
}
