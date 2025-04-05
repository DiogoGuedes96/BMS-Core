<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bms_service_states', function (Blueprint $table) {
            $table->boolean('readonly')->default(false)->after('is_default');
        });
    }

    public function down(): void
    {
        Schema::table('bms_service_states', function (Blueprint $table) {
            $table->boolean('readonly')->default(false);
        });
    }
};
