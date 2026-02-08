<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYearlyOperatorsIncomesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pgsql')->create('yearly_operators_incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id');
            $table->string('month', 16);
            $table->string('year', 16);
            $table->string('amount', 32)->default('0');
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
        Schema::dropIfExists('yearly_operators_incomes');
    }
}
