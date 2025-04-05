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
        Schema::create('feedback_have_feedback_who', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('feedback_id')->unsigned();
            $table->bigInteger('feedback_who_id')->unsigned();
            $table->timestamps();

            $table->index(["feedback_id"], 'fk_feedback_have_feedback_who_feedback_id_idx');
            $table->index(["feedback_who_id"], 'fk_feedback_have_feedback_who_feedback_who_id_idx');

            $table->foreign('feedback_id', 'fk_feedback_have_feedback_who_feedback_id_idx')
                ->references('id')->on('feedback')
                ->onDelete('cascade')
                ->onUpdate('no action');

            $table->foreign('feedback_who_id', 'fk_feedback_have_feedback_who_feedback_who_id_idx')
                ->references('id')->on('feedback_who')
                ->onDelete('cascade')
                ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_feedback_who');
    }
};
