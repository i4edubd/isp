<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('customer_bills')) {
            Schema::create('customer_bills', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('customer_id');
                $table->unsignedBigInteger('package_id')->nullable();
                $table->decimal('amount', 10, 2)->default(0);
                $table->string('status')->default('pending');
                $table->integer('year');
                $table->integer('month');
                $table->timestamps();

                $table->unique(['customer_id', 'year', 'month'], 'customer_bills_customer_year_month_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_bills');
    }
};
