<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konversi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_level');
            $table->uuid('id_soal');
            $table->json('jawaban')->nullable();
            $table->text('output')->nullable();
            $table->integer('bobot')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_level')->references('id')->on('level')->onDelete('cascade');
            $table->foreign('id_soal')->references('id')->on('soal')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konversi');
    }
};
