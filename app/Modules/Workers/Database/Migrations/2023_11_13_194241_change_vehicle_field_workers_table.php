<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bms_workers', function (Blueprint $table) {
            $table->dropColumn('vehicle');

            $table->unsignedBigInteger('vehicle_id')->nullable()->after('table_id');
            $table->foreign('vehicle_id')->references('id')->on('vehicles');
        });
    }

    public function down(): void
    {
        Schema::table('bms_workers', function (Blueprint $table) {
            $table->dropForeign('bms_workers_vehicle_id_foreign');
            $table->dropColumn('vehicle_id');

            $table->string('vehicle', 100)->nullable()->after('password');
        });
    }
};
