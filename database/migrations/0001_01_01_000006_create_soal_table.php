<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('soal', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_level')->nullable();
            $table->string('judul', 255)->nullable();
            $table->text('soal')->nullable();
            $table->json('kunci_tipe_data')->nullable();
            $table->json('kunci_algoritma')->nullable();
            $table->integer('order')->nullable();
            $table->integer('status')->nullable();
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('easy');
            
            $table->timestamps();
            $table->softDeletes();

            // Relasi ke tabel level
            $table->foreign('id_level')
                  ->references('id')
                  ->on('level')
                  ->onDelete('cascade'); 
                  // Menggunakan cascade agar jika level dihapus, soal ikut terhapus otomatis
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soal');
    }
};