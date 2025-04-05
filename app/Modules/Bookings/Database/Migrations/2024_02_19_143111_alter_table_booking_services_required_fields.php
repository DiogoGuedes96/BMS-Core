<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bms_booking_services', function (Blueprint $table) {
            $table->unsignedInteger('number_adults')->nullable()->change();
            $table->unsignedInteger('booster')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('bms_booking_services', function (Blueprint $table) {
            $table->unsignedInteger('number_adults')->change();
            $table->unsignedInteger('booster')->change();
        });
    }
};
