<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bms_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('address', 255);
            $table->float('lat');
            $table->float('long');
            $table->string('reference_point', 255)->nullable();
            $table->boolean('active')->default(true);

            $table->unsignedBigInteger('zone_id');
            $table->foreign('zone_id')->references('id')->on('bms_zones');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bms_locations');
    }
};
