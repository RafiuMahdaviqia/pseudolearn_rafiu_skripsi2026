<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pencapaian', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_mahasiswa');
            $table->uuid('id_level')->nullable();
            $table->uuid('id_soal')->nullable();
            $table->uuid('id_soal_konversi')->nullable();
            $table->string('category')->nullable();
            $table->text('img')->nullable();
            $table->string('name')->nullable();
            $table->text('desc')->nullable();
            $table->integer('progress')->default(0);
            $table->integer('max_progress')->default(0);
            $table->integer('status')->default(0);
            $table->integer('heart')->nullable();
            $table->dateTime('date_claimed')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_mahasiswa')->references('id')->on('mahasiswa')->onDelete('cascade');
            $table->foreign('id_level')->references('id')->on('level')->onDelete('set null');
            $table->foreign('id_soal')->references('id')->on('soal')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pencapaian');
    }
};
