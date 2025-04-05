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
            $table->softDeletes();
        });
        Schema::table('bms_service_scheduling_upload', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bms_service_scheduling', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('bms_service_scheduling_upload', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
