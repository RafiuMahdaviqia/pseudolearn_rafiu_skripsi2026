<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('bank_soal_konversi', function (Blueprint $table) {
            $table->string('id', 255)->primary();
            $table->string('id_level', 255)->nullable()->index();
            $table->string('id_soal', 255)->nullable()->index();
            $table->text('jawaban')->nullable();
            $table->text('output')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_soal_konversi');
    }
};
