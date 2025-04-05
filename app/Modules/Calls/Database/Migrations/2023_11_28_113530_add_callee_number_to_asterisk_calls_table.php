<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('asterisk_calls', function (Blueprint $table) {
            $table->string('callee_phone')->nullable();
        });
    }

    public function down()
    {
        Schema::table('asterisk_calls', function (Blueprint $table) {
            $table->dropColumn('callee_phone');
        });
    }
};
