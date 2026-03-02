<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_mahasiswa');
            $table->uuid('id_level')->nullable();
            $table->uuid('id_soal')->nullable();
            $table->enum('type', ['biasa', 'adaptive'])->default('biasa');
            $table->text('pesan');
            $table->text('respons');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_mahasiswa')->references('id')->on('mahasiswa')->onDelete('cascade');
            $table->foreign('id_level')->references('id')->on('level')->onDelete('set null');
            $table->foreign('id_soal')->references('id')->on('soal')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_logs');
    }
};