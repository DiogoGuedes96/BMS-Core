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
        Schema::create('patient_have_responsible', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('patient_id')->unsigned();
            $table->bigInteger('patient_responsible_id')->unsigned();
            $table->timestamps();

            $table->index(["patient_id"], 'fk_patient_have_responsible_patient_id_idx');
            $table->index(["patient_responsible_id"], 'fk_patient_have_responsible_patient_responsible_id_idx');

            $table->foreign('patient_id', 'fk_patient_have_responsible_patient_id_idx')
                ->references('id')->on('patients')
                ->onDelete('cascade')
                ->onUpdate('no action');

            $table->foreign('patient_responsible_id', 'fk_patient_have_responsible_patient_responsible_id_idx')
                ->references('id')->on('patient_responsible')
                ->onDelete('cascade')
                ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('responsible_patient_and_patient');
    }
};
