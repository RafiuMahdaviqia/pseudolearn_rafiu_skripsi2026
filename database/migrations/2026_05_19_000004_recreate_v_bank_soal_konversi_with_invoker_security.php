<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_bank_soal_konversi;');

        $hasBskDifficulty = Schema::hasColumn('bank_soal_konversi', 'difficulty');
        $hasSoalDifficulty = Schema::hasColumn('soal', 'difficulty');

        if ($hasBskDifficulty && $hasSoalDifficulty) {
            $difficultyExpr = 'COALESCE(bsk.difficulty, s.difficulty) AS difficulty';
        } elseif ($hasBskDifficulty) {
            $difficultyExpr = 'bsk.difficulty AS difficulty';
        } elseif ($hasSoalDifficulty) {
            $difficultyExpr = 's.difficulty AS difficulty';
        } else {
            $difficultyExpr = 'NULL AS difficulty';
        }

        DB::statement("\n            CREATE ALGORITHM=UNDEFINED SQL SECURITY INVOKER VIEW v_bank_soal_konversi AS\n            SELECT\n                bsk.id,\n                bsk.id_level,\n                l.name     AS level_name,\n                bsk.id_soal,\n                s.judul    AS judul_soal,\n                s.soal     AS soal_name,\n                bsk.jawaban,\n                bsk.output,\n                s.`order`  AS `order`,\n                {$difficultyExpr},\n                bsk.created_at,\n                bsk.updated_at,\n                bsk.deleted_at,\n                s.status\n            FROM bank_soal_konversi bsk\n            LEFT JOIN level l ON bsk.id_level = l.id\n            LEFT JOIN soal  s ON bsk.id_soal  = s.id\n        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_bank_soal_konversi;');
    }
};
