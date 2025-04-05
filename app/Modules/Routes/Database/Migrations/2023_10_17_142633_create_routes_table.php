<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bms_routes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_zone_id');
            $table->unsignedBigInteger('to_zone_id');
            $table->foreign('from_zone_id')->references('id')->on('bms_zones');
            $table->foreign('to_zone_id')->references('id')->on('bms_zones');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
