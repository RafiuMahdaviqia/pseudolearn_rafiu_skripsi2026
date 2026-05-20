<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_konversi;');

        DB::statement("\n            CREATE VIEW v_konversi AS\n            SELECT\n                k.id,\n                k.id_level,\n                l.name                          AS level_name,\n                k.id_soal,\n                s.judul                         AS judul_soal,\n                s.`order`                       AS `order`,\n                CAST(k.jawaban AS CHAR(10000))  AS konversi_output,\n                k.jawaban,\n                k.output,\n                k.bobot,\n                k.created_at,\n                k.updated_at,\n                k.deleted_at,\n                s.status\n            FROM konversi k\n            LEFT JOIN level l ON k.id_level = l.id\n            LEFT JOIN soal  s ON k.id_soal  = s.id\n        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_konversi;');

        DB::statement("\n            CREATE VIEW v_konversi AS\n            SELECT\n                k.id,\n                k.id_level,\n                l.name                          AS level_name,\n                k.id_soal,\n                s.judul                         AS judul_soal,\n                CAST(k.jawaban AS CHAR(10000))  AS konversi_output,\n                k.jawaban,\n                k.output,\n                k.bobot,\n                k.created_at,\n                k.updated_at,\n                k.deleted_at,\n                s.status\n            FROM konversi k\n            LEFT JOIN level l ON k.id_level = l.id\n            LEFT JOIN soal  s ON k.id_soal  = s.id\n        ");
    }
};