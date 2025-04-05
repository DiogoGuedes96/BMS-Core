<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['is_back_service']);
        });
        Schema::table('bms_service_scheduling', function (Blueprint $table) {
            $table->string('is_back_service')->require();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients_and_put_on_sheduling', function (Blueprint $table) {
            //
        });
    }
};
