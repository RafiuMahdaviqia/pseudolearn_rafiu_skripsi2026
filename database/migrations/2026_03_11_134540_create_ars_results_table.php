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
        Schema::create('ars_results', function (Blueprint $table) {
            $table->id();
            $table->integer('id_mahasiswa');           // mahasiswa yang mengerjakan
            $table->integer('id_soal');                // soal pseudo/konversi
            $table->string('jenis_soal');              // 'pseudo' atau 'konversi'
            $table->string('difficulty_sekarang');     // difficulty soal
            $table->string('cluster');                 // ideal / normal / gaming / struggling
            $table->string('rekomendasi_difficulty')->nullable(); // hasil ARS
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ars_results');
    }
};
