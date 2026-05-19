<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('history_jawaban', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_level');
            $table->uuid('id_soal');
            $table->uuid('id_mahasiswa');
            $table->integer('index_tipe_data')->nullable();
            $table->string('tipe_data')->nullable();
            $table->integer('index_algoritma')->nullable();
            $table->string('algoritma')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_level')->references('id')->on('level')->onDelete('cascade');
            $table->foreign('id_soal')->references('id')->on('soal')->onDelete('cascade');
            $table->foreign('id_mahasiswa')->references('id')->on('mahasiswa')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('history_jawaban');
    }
};
