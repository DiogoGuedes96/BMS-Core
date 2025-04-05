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
        Schema::create('ambulance_crew', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->required();
            $table->string('email', 50)->required();
            $table->integer('phone_number')->required();
            $table->integer('nif')->nullable();
            $table->string('driver_license', 20)->required();
            $table->date('contract_date')->nullable(false);
            $table->integer('contract_number')->nullable();
            $table->string('job_title', 100)->nullable();
            $table->string('address', 255)->required();
            $table->boolean('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ambulance_crew');
    }
};
