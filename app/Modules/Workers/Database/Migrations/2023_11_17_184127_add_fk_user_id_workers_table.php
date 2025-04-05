<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bms_workers', function (Blueprint $table) {
            $table->dropColumn('password');

            $table->unsignedBigInteger('user_id')->nullable()->after('table_id');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::table('bms_workers', function (Blueprint $table) {
            $table->dropForeign('bms_workers_user_id_foreign');
            $table->dropColumn('user_id');

            $table->string('password')->nullable()->after('email');
        });
    }
};
