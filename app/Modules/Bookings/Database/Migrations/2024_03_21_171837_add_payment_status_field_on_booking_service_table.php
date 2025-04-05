<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bms_booking_services', function (Blueprint $table) {
            $table->boolean('payment_status')->default(false)->after('voucher');
        });
    }

    public function down(): void
    {
        Schema::table('bms_booking_services', function (Blueprint $table) {
            $table->dropColumn('payment_status');
        });
    }
};
