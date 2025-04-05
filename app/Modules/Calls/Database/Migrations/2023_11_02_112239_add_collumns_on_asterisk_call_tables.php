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
        Schema::table('asterisk_calls', function (Blueprint $table) {
            $table->string('call_reason')->nullable();
            $table->unsignedBigInteger('call_operator')->nullable();

            $table->index('call_operator', 'fk_asterisk_calls_call_operator');

            $table->foreign('call_operator', 'fk_asterisk_calls_call_operator')
                ->references('id')
                ->on('clients');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asterisk_calls', function (Blueprint $table) {
            $table->dropColumn('discount_default');
            $table->dropForeign('fk_asterisk_calls_call_operator');
            $table->dropIndex('fk_asterisk_calls_call_operator');
            $table->dropColumn('call_operator');
        });
    }
};
