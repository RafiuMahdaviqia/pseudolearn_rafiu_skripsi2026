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
        Schema::create('ars_result', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->integer('ars_batch')->default(1);

            $table->uuid('id_mahasiswa');
            $table->uuid('id_level');
            $table->uuid('id_soal');
            $table->string('difficulty')->nullable();

            $table->integer('pseudo_langkah')->nullable();
            $table->integer('pseudo_durasi')->nullable();
            $table->string('pseudo_label')->nullable();
            $table->integer('pseudo_score')->nullable();

            $table->integer('konversi_langkah')->nullable();
            $table->integer('konversi_durasi')->nullable();
            $table->string('konversi_label')->nullable();
            $table->integer('konversi_score')->nullable();

            $table->timestamps();

            $table->unique(['id_mahasiswa', 'id_level', 'id_soal']);

            $table->foreign('id_mahasiswa')
                ->references('id')
                ->on('mahasiswa')
                ->cascadeOnDelete();

            $table->foreign('id_level')
                ->references('id')
                ->on('level')
                ->cascadeOnDelete();

            $table->foreign('id_soal')
                ->references('id')
                ->on('soal')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ars_result');
    }
};
