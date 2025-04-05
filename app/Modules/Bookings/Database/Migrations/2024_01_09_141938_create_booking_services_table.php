<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bms_booking_services', function (Blueprint $table) {
            $table->id();
            $table->date('start');
            $table->time('hour');
            $table->unsignedInteger('number_adults');
            $table->unsignedInteger('number_children')->nullable();
            $table->unsignedInteger('number_baby_chair')->nullable();
            $table->unsignedInteger('booster');
            $table->string('flight_number', 10)->nullable();
            $table->string('car_type', 20)->nullable();
            $table->string('pickup_location', 255)->nullable();
            $table->string('pickup_address', 255)->nullable();
            $table->string('pickup_reference_point', 255)->nullable();
            $table->string('dropoff_location', 255)->nullable();
            $table->string('dropoff_address', 255)->nullable();
            $table->string('dropoff_reference_point', 255)->nullable();
            $table->float('value', 6, 2);
            $table->string('charge', 20);
            $table->float('commission', 6, 2)->nullable();
            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable();

            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('service_type_id');
            $table->unsignedBigInteger('service_state_id');
            $table->unsignedBigInteger('staff_id')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('pickup_location_id')->nullable();
            $table->unsignedBigInteger('pickup_zone_id');
            $table->unsignedBigInteger('dropoff_location_id')->nullable();
            $table->unsignedBigInteger('dropoff_zone_id');
            $table->unsignedBigInteger('parent_id')->nullable();

            $table->foreign('booking_id')->references('id')->on('bms_bookings');
            $table->foreign('service_type_id')->references('id')->on('bms_services');
            $table->foreign('service_state_id')->references('id')->on('bms_service_states');
            $table->foreign('staff_id')->references('id')->on('bms_workers');
            $table->foreign('supplier_id')->references('id')->on('bms_workers');
            $table->foreign('vehicle_id')->references('id')->on('bms_vehicles');
            $table->foreign('pickup_zone_id')->references('id')->on('bms_zones');
            $table->foreign('dropoff_zone_id')->references('id')->on('bms_zones');
            $table->foreign('pickup_location_id')->references('id')->on('bms_locations');
            $table->foreign('dropoff_location_id')->references('id')->on('bms_locations');
            $table->foreign('parent_id')->references('id')->on('bms_booking_services');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bms_booking_services');
    }
};
