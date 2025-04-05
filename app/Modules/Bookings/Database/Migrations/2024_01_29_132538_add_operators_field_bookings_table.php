<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Modules\Bookings\Enums\CreatedByEnum;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bms_bookings', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('pax_group');
            $table->date('end_date')->nullable()->after('start_date');
            $table->string('pickup_location', 255)->nullable()->after('end_date');
            $table->string('dropoff_location', 255)->nullable()->after('pickup_location');
            $table->string('created_by', 20)->default(CreatedByEnum::ATRAVEL)->after('dropoff_location');
            $table->unsignedBigInteger('pickup_location_id')->nullable()->after('booking_client_id');
            $table->unsignedBigInteger('dropoff_location_id')->nullable()->after('pickup_location_id');

            $table->foreign('pickup_location_id')->references('id')->on('bms_locations');
            $table->foreign('dropoff_location_id')->references('id')->on('bms_locations');
        });
    }

    public function down(): void
    {
        Schema::table('bms_bookings', function (Blueprint $table) {
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
            $table->dropColumn('pickup_location');
            $table->dropColumn('dropoff_location');
            $table->dropColumn('created_by');
            $table->dropColumn('pickup_location_id');
            $table->dropColumn('dropoff_location_id');
        });
    }
};
