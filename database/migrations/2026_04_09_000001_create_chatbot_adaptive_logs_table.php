<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_adaptive_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_mahasiswa')->nullable();
            $table->string('nim')->nullable();
            $table->string('nama')->nullable();
            $table->uuid('id_kelas')->nullable();
            $table->string('kelas')->nullable();
            $table->uuid('id_level')->nullable();
            $table->string('level_soal')->nullable();
            $table->uuid('id_soal')->nullable();
            $table->string('jenis_soal')->nullable();
            $table->unsignedInteger('jumlah_langkah')->default(0);
            $table->timestamp('waktu_mulai')->nullable();
            $table->timestamp('waktu_selesai')->nullable();
            $table->uuid('id_label_skor')->nullable();
            $table->string('labeling')->nullable();
            $table->unsignedInteger('durasi_menit')->nullable();
            $table->unsignedInteger('total_akses_chatbot_adaptive')->default(0);
            $table->longText('pesan_bimbingan')->nullable();
            $table->json('detail')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_mahasiswa')->references('id')->on('mahasiswa')->nullOnDelete();
            $table->foreign('id_kelas')->references('id')->on('kelas')->nullOnDelete();
            $table->foreign('id_level')->references('id')->on('level')->nullOnDelete();
            $table->foreign('id_soal')->references('id')->on('soal')->nullOnDelete();
            $table->foreign('id_label_skor')->references('id')->on('label_skor')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_adaptive_logs');
    }
};
