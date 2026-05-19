<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_ujian_konversi;');

        DB::statement("
            CREATE VIEW v_ujian_konversi AS
            SELECT
                uk.id,
                uk.id_level,
                uk.id_soal_konversi,
                k.id_soal,
                uk.id_mahasiswa,
                s.judul     AS judul_soal,
                uk.jawaban,
                uk.output,
                uk.nilai,
                uk.waktu,
                uk.created_at,
                uk.updated_at,
                uk.deleted_at
            FROM ujian_konversi uk
            LEFT JOIN konversi k ON k.id = uk.id_soal_konversi
            LEFT JOIN soal  s ON s.id = k.id_soal
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_ujian_konversi;');
    }
};
