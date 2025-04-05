<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bms_booking_services', function (Blueprint $table) {
            $table->integer('value')->length(8)->unsigned()->nullable()->change();
            $table->integer('commission')->length(8)->unsigned()->nullable()->change();
        });

        Schema::table('bms_bookings', function (Blueprint $table) {
            $table->integer('value')->length(8)->unsigned()->nullable()->change();
            $table->integer('deposits_paid')->length(8)->unsigned()->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('bms_booking_services', function (Blueprint $table) {
            $table->double('value', 6, 2)->change();
            $table->double('commission', 6, 2)->change();
        });

        Schema::table('bms_bookings', function (Blueprint $table) {
            $table->double('value', 6, 2)->change();
            $table->double('deposits_paid', 6, 2)->change();
        });
    }
};
