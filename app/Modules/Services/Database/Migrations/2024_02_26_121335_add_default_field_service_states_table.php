<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bms_service_states', function (Blueprint $table) {
            $table->boolean('is_default')->default(false)->after('active');
        });
    }

    public function down(): void
    {
        Schema::table('bms_service_states', function (Blueprint $table) {
            $table->dropColumn('is_default');
        });
    }
};
