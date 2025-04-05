<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Modules\Bookings\Enums\CreatedByEnum;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bms_bookings', function (Blueprint $table) {
            $table->dropColumn('end_date');
            $table->time('hour')->nullable()->after('start_date');
            $table->text('additional_information')->nullable()->after('created_by');
            $table->text('status_reason')->nullable()->after('additional_information');
        });
    }

    public function down(): void
    {
        Schema::table('bms_bookings', function (Blueprint $table) {
            $table->date('end_date')->nullable()->after('start_date');
            $table->dropColumn('hour');
            $table->dropColumn('status_reason');
            $table->dropColumn('additional_information');
        });
    }
};
