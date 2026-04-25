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
        Schema::create('ujian_kode', function (Blueprint $table) {
            $table->string('id', 255)->primary();
            $table->string('id_level', 255)->nullable();
            $table->string('id_bank_soal_konversi', 255)->nullable();
            $table->string('id_mahasiswa', 255)->nullable();
            $table->text('jawaban')->nullable();
            $table->text('output')->nullable();
            $table->integer('nilai')->nullable();
            $table->integer('waktu')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_bank_soal_konversi')
                ->references('id')->on('bank_soal_konversi')
                ->onDelete('cascade');

            $table->foreign('id_mahasiswa')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('id_level')
                ->references('id')->on('level')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ujian_kode');
    }
};
