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
        Schema::create('clients_have_responsibles', function (Blueprint $table) {
            $table->id();
            $table->BigInteger('client_id')->unsigned();
            $table->BigInteger('client_responsible_id')->unsigned();
            $table->timestamps();

            $table->index(["client_id"], 'fk_clients_have_responsible_client_id_idx');
            $table->index(["client_responsible_id"], 'fk_patient_have_responsible_client_responsible_id_idx');

            $table->foreign('client_id', 'fk_clients_have_responsible_client_id_idx')
                ->references('id')->on('clients')
                ->onDelete('cascade')
                ->onUpdate('no action');

            $table->foreign('client_responsible_id', 'fk_patient_have_responsible_client_responsible_id_idx')
                ->references('id')->on('client_responsible')
                ->onDelete('cascade')
                ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients_have_responsibles');
    }
};
