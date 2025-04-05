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
        Schema::create('clients_have_patients', function (Blueprint $table) {
            $table->id();
            $table->BigInteger('client_id')->unsigned();
            $table->BigInteger('patient_id')->unsigned();
            $table->timestamps();

            $table->index(["client_id"], 'fk_clients_have_patients_client_id_idx');
            $table->index(["patient_id"], 'fk_clients_have_patients_patient_id_idx');

            $table->foreign('client_id', 'fk_clients_have_patients_client_id_idx')
                ->references('id')->on('clients')
                ->onDelete('cascade')
                ->onUpdate('no action');

            $table->foreign('patient_id', 'fk_clients_have_patients_patient_id_idx')
                ->references('id')->on('patients')
                ->onDelete('cascade')
                ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients_have_patients');
    }
};
