<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bms_table_routes', function (Blueprint $table) {
            $table->id();
            $table->float('pax14', 6, 2);
            $table->float('pax58', 6, 2);

            $table->unsignedBigInteger('table_id');
            $table->foreign('table_id')->references('id')->on('bms_tables');

            $table->unsignedBigInteger('route_id');
            $table->foreign('route_id')->references('id')->on('bms_routes');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bms_table_routes');
    }
};
