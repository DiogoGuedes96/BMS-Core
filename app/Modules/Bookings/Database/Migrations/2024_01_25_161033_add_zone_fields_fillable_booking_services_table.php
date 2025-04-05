<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bms_booking_services', function (Blueprint $table) {
            $table->string('pickup_zone', 255)->nullable()->after('pickup_location');
            $table->string('dropoff_zone', 255)->nullable()->after('dropoff_location');
            $table->unsignedBigInteger('pickup_zone_id')->nullable()->change();
            $table->unsignedBigInteger('dropoff_zone_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('bms_booking_services', function (Blueprint $table) {
            $table->dropColumn('pickup_zone');
            $table->dropColumn('dropoff_zone');
            $table->unsignedBigInteger('pickup_zone_id')->change();
            $table->unsignedBigInteger('dropoff_zone_id')->change();
        });
    }
};
