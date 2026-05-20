<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_soal_konversi', function (Blueprint $table) {
            $table->string('id', 255)->primary();
            $table->string('id_level', 255)->nullable();
            $table->string('id_soal', 255)->nullable();
            $table->text('jawaban')->nullable();
            $table->text('output')->nullable();
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('easy');
            
            $table->timestamps();
            $table->softDeletes();

            // Relasi ke tabel level
            $table->foreign('id_level')
                  ->references('id')
                  ->on('level')
                  ->onDelete('cascade');

            // Relasi ke tabel soal (opsional namun sangat disarankan untuk integritas)
            $table->foreign('id_soal')
                  ->references('id')
                  ->on('soal')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_soal_konversi');
    }
};