<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bms_bookings', function (Blueprint $table) {
            $table->string('reference', 10)->nullable()->after('was_paid');
        });
    }

    public function down(): void
    {
        Schema::table('bms_bookings', function (Blueprint $table) {
            $table->dropColumn('reference');
        });
    }
};
