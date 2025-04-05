<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bms_workers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('phone', 30)->nullable();
            $table->string('social_denomination', 30)->nullable();
            $table->string('nif', 9)->nullable();
            $table->string('responsible_name', 255)->nullable();
            $table->string('responsible_phone', 30)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('postal_code', 30)->nullable();
            $table->string('locality', 50)->nullable();
            $table->string('antecedence', 255)->nullable();
            $table->string('username', 50)->nullable()->unique();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('vehicle', 100)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('active')->default(true);
            $table->string('type')->default('operators');
            $table->timestamps();
            $table->softDeletes();

            $table->unsignedBigInteger('table_id');
            $table->foreign('table_id')->references('id')->on('bms_tables');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bms_workers');
    }
};
