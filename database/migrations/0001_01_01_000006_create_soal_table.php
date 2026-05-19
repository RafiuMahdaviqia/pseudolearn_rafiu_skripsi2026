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
            $table->uuid('id_level');
            $table->string('judul')->nullable();
            $table->longText('soal')->nullable();
            $table->json('kunci_tipe_data')->nullable();
            $table->json('kunci_algoritma')->nullable();
            $table->integer('order')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_level')->references('id')->on('level')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soal');
    }
};
