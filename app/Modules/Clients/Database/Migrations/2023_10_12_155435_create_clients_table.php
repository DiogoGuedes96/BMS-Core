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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->required();
            $table->string('email', 50)->email()->nullable()->unique();
            $table->string('type', 25)->required();
            $table->string('address', 255)->required();
            $table->integer('nif')->length(9)->nullable();
            $table->integer('phone')->length(13)->required();
            $table->boolean('status')->required();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
