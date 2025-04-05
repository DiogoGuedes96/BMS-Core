<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('business_payments_responsible', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('business_id')->nullable();
            $table->foreign('business_id')->references('id')->on('business');
            $table->unsignedBigInteger('business_payment_id')->nullable();
            $table->foreign('business_payment_id')->references('id')->on('business_payments');
            $table->string('payment_type');
            $table->string('responsible');
            $table->decimal('value', 10, 2)->nullable();
            $table->datetime('payment_date');
            $table->unsignedBigInteger('sequence')->nullable();
            $table->datetime('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_payments_responsible');
    }
};
