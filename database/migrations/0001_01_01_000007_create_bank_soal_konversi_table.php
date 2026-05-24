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
        Schema::create('bank_soal_konversi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_level')->nullable();
            $table->uuid('id_soal')->nullable();
            $table->text('jawaban')->nullable();
            $table->text('output')->nullable();
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('easy');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_level')
                ->references('id')->on('level')
                ->onDelete('cascade');

            $table->foreign('id_soal')
                ->references('id')->on('soal')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_soal_konversi');
    }
};