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
        Schema::create('bms_service_scheduling_repeat', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_repeat_schedule')->default(false)->nullable();
            $table->dateTime('repeat_date')->nullable();
            $table->time('repeat_time')->nullable();
            $table->json('repeat_days')->nullable();
            $table->string('repeat_finish_by')->nullable();
            $table->dateTime('repeat_final_date')->nullable();
            $table->integer('repeat_number_sessions')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bms_service_scheduling_repeat');
    }
};
