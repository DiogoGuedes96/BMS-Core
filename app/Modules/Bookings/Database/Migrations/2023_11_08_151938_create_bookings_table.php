<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bms_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('client_name', 255)->nullable();
            $table->string('client_email', 255)->nullable();
            $table->string('client_phone', 30)->nullable();
            $table->float('value', 6, 2)->default(0);
            $table->float('deposits_paid', 6, 2)->default(0);
            $table->unsignedInteger('pax_group')->default(0);
            $table->boolean('emphasis')->default(false);
            $table->string('status')->nullable();

            $table->unsignedBigInteger('operator_id')->nullable();
            $table->foreign('operator_id')->references('id')->on('bms_workers');

            $table->unsignedBigInteger('booking_client_id')->nullable();
            $table->foreign('booking_client_id')->references('id')->on('bms_booking_clients');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bms_bookings');
    }
};
