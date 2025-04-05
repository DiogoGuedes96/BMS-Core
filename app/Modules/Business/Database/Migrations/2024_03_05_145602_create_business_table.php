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
        $comissionMethods = ['recorrente', '3x', '6x', '12x', 'pagamento total'];

        Schema::create('business', function (Blueprint $table) use ($comissionMethods) {
            $table->id();
            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id')->references('id')->on('uni_clients');
            $table->string('name');
            $table->decimal('value', 10, 2)->nullable();
            $table->string('type_product');
            $table->unsignedBigInteger('business_kanban_id');
            $table->foreign('business_kanban_id')->references('id')->on('business_kanban');
            $table->string('stage');
            $table->enum('state_business', ['aberto', 'fechado'])->default('aberto');
            $table->unsignedBigInteger('referrer_id')->nullable();
            $table->foreign('referrer_id')->references('id')->on('users');
            $table->decimal('referrer_commission', 10, 2)->nullable();
            $table->enum('referrer_commission_method', $comissionMethods)->nullable();
            $table->unsignedBigInteger('coach_id')->nullable();
            $table->foreign('coach_id')->references('id')->on('users');
            $table->decimal('coach_commission', 10, 2)->nullable();
            $table->enum('coach_commission_method', $comissionMethods)->nullable();
            $table->unsignedBigInteger('closer_id')->nullable();
            $table->foreign('closer_id')->references('id')->on('users');
            $table->decimal('closer_commission', 10, 2)->nullable();
            $table->enum('closer_commission_method', $comissionMethods)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business');
    }
};
