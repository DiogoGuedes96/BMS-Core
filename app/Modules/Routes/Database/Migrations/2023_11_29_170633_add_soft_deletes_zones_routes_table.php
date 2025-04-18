<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bms_zones', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('bms_routes', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        //
    }
};
