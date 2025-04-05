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
        Schema::create('kanban_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id')->nullable();
            $table->foreign('business_id')->references('id')->on('business');
            $table->unsignedBigInteger('kanban_id');
            $table->foreign('kanban_id')->references('id')->on('business_kanban');
            $table->unsignedBigInteger('kanban_column_id')->default(1);
            $table->foreign('kanban_column_id')->references('id')->on('business_kanban_columns');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kanban_history');
    }
};
