<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ujian_konversi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_level')->nullable();
            $table->uuid('id_soal_konversi')->nullable();
            $table->uuid('id_mahasiswa')->nullable();
            $table->json('jawaban')->nullable();
            $table->text('output')->nullable();
            $table->integer('nilai')->nullable();
            $table->integer('waktu')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_level')
                ->references('id')->on('level')
                ->onDelete('cascade');

            $table->foreign('id_soal_konversi')
                ->references('id')->on('konversi')
                ->onDelete('cascade');

            $table->foreign('id_mahasiswa')
                ->references('id')->on('mahasiswa')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ujian_konversi');
    }
};