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
            $table->string('name');
            $table->integer('jumlah_soal')->nullable();
            $table->string('image')->nullable();
            $table->string('feedback_data_type')->nullable();
            $table->string('feedback_algorithm')->nullable();
            $table->integer('limit_soal')->nullable();
            $table->integer('limit_ars')->nullable();
            $table->integer('order')->nullable();
            $table->boolean('manual_active')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('level');
    }
};
