<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('email', 150)->unique();
            $table->string('img_url', 150)->nullable(); 
            $table->string('phone', 9);
            $table->time('notification_time')->nullable();
            $table->boolean('automatic_notification')->default(false);
            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('companies');
    }
};
