<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('level', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 255)->nullable();
            $table->string('image', 255)->nullable();
            $table->text('feedback_data_type')->nullable();
            $table->text('feedback_algorithm')->nullable();
            $table->integer('order')->nullable();
            $table->integer('manual_active')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('level');
    }
};