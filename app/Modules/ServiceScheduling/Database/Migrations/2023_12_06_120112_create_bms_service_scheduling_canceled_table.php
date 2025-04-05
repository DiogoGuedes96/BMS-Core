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
        Schema::create('bms_service_scheduling_canceled', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bms_service_scheduling_id');
            $table->foreign('bms_service_scheduling_id')->references('id')->on('bms_service_scheduling')->name('fk_bms_canceled_scheduling');
            $table->string('canceled_reason');
            $table->string('canceled_name');
            $table->string('canceled_client_patient');
            $table->string('canceled_through');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bms_service_scheduling_canceled');
    }
};
