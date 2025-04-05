<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bms_locations', function (Blueprint $table) {
            $table->decimal('lat', 15, 10)->change();
            $table->decimal('long', 15, 10)->change();
        });
    }

    public function down(): void
    {
        //
    }
};
