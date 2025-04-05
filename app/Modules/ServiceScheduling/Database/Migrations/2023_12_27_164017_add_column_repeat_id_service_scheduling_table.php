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
        Schema::table('bms_service_scheduling', function (Blueprint $table) {
            $table->unsignedBigInteger('repeat_id')->nullable();
            $table->foreign('repeat_id')->references('id')->on('bms_service_scheduling_repeat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bms_service_scheduling', function (Blueprint $table) {
            $table->dropColumn('repeat_id');
        });
    }
};
