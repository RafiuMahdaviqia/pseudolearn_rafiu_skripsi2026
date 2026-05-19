<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_test', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_level');
            $table->uuid('id_mahasiswa');
            $table->decimal('pre_test', 5, 2)->nullable();
            $table->decimal('post_test', 5, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_level')->references('id')->on('level')->onDelete('cascade');
            $table->foreign('id_mahasiswa')->references('id')->on('mahasiswa')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_test');
    }
};
