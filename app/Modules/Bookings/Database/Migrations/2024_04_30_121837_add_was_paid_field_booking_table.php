<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bms_booking_services', function (Blueprint $table) {
            $table->renameColumn('payment_status', 'was_paid');
        });

        Schema::table('bms_bookings', function (Blueprint $table) {
            $table->boolean('was_paid')->default(false)->after('voucher');
        });
    }

    public function down(): void
    {
        Schema::table('bms_booking_services', function (Blueprint $table) {
            $table->renameColumn('was_paid', 'payment_status');
        });

        Schema::table('bms_bookings', function (Blueprint $table) {
            $table->dropColumn('was_paid');
        });
    }
};
