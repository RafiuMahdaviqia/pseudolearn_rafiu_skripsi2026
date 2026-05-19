<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_data', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_soal');
            $table->uuid('id_mahasiswa');
            $table->integer('index')->nullable();
            $table->text('itemText')->nullable();
            $table->integer('timer_second')->nullable();
            $table->string('type')->nullable();
            $table->text('variabel')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_soal')->references('id')->on('soal')->onDelete('cascade');
            $table->foreign('id_mahasiswa')->references('id')->on('mahasiswa')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_data');
    }
};
