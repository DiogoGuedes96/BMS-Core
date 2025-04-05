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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->required();
            $table->integer('patient_number')->nullable(false);
            $table->integer('nif')->nullable(false);
            $table->date('birthday')->nullable(false);
            $table->string('email', 255)->nullable();
            $table->string('address', 255)->required();
            $table->string('postal_code', 255)->required();
            $table->string('postal_code_address', 255)->required();
            $table->string('transport_feature', 255)->required();
            $table->string('patient_responsible', 255)->nullable();
            $table->integer('phone_number')->nullable(false);
            $table->text('patient_observations')->required();
            $table->integer('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
