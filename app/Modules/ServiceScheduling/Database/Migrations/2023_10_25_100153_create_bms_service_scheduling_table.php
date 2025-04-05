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
        Schema::create('bms_service_scheduling', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('client_id')->references('id')->on('clients');
            $table->unsignedBigInteger('patient_id')->references('id')->on('patients');
            $table->string('reason');
            $table->string('additional_note', 255);
            $table->string('patients_status');
            $table->string('patient_name', 255);
            $table->integer('patient_number');
            $table->integer('patient_nif');
            $table->integer('patient_contact');
            $table->string('transport_feature', 255);
            $table->string('service_type', 255);
            $table->dateTime('date');
            $table->time('time');
            $table->string('origin', 255);
            $table->string('destination', 255);
            $table->string('vehicle', 255);
            $table->string('license_plate', 9);
            $table->string('responsible_tats_1');
            $table->string('responsible_tats_2', 255);
            $table->boolean('companion');
            $table->string('companion_name', 255)->nullable();
            $table->unsignedBigInteger('companion_contact')->nullable();
            $table->string('transport_justification');
            $table->string('payment_method');
            $table->double('total_value');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('bms_service_scheduling')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bms_service_scheduling');
    }
};
