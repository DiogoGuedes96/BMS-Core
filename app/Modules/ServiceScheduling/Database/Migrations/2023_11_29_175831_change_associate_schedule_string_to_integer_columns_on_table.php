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
            $table->dropColumn('associated_schedule');
        });

        Schema::table('bms_service_scheduling', function (Blueprint $table) {
            $table->integer('associated_schedule')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bms_service_scheduling', function (Blueprint $table) {
            $table->dropColumn('associated_schedule');
        });

        Schema::table('bms_service_scheduling', function (Blueprint $table) {
            $table->string('associated_schedule')->default(0);
        });
    }
};
