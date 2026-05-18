<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bank_soal_konversi', function (Blueprint $table) {
            if (!Schema::hasColumn('bank_soal_konversi', 'difficulty')) {
                $table->enum('difficulty', ['easy', 'medium', 'hard'])->nullable()->after('output')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('bank_soal_konversi', function (Blueprint $table) {
            if (Schema::hasColumn('bank_soal_konversi', 'difficulty')) {
                $table->dropColumn('difficulty');
            }
        });
    }
};
