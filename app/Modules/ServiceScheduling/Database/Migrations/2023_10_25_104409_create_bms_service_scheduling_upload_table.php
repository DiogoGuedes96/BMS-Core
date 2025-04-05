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
        Schema::create('bms_service_scheduling_upload', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->unsignedBigInteger('bms_service_scheduling_id');
            $table->foreign('bms_service_scheduling_id')->references('id')->on('bms_service_scheduling');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_scheduling_upload');
    }
};
