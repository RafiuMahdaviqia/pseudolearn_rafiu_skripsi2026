<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create v_bank_soal_konversi
        DB::statement("
            CREATE VIEW v_bank_soal_konversi AS
            SELECT
                bsk.id,
                bsk.id_level,
                l.name     AS level_name,
                bsk.id_soal,
                s.judul    AS judul_soal,
                s.soal     AS soal_name,
                bsk.jawaban,
                bsk.output,
                bsk.`order`,
                bsk.created_at,
                bsk.updated_at,
                bsk.deleted_at,
                s.status
            FROM bank_soal_konversi bsk
            LEFT JOIN level l ON bsk.id_level = l.id
            LEFT JOIN soal  s ON bsk.id_soal  = s.id;
        ");

        // 2. Create v_ujian_kode
        DB::statement("
            CREATE VIEW v_ujian_kode AS
            SELECT
                uk.id,
                uk.id_level,
                uk.id_bank_soal_konversi,
                bsk.id_soal,
                uk.id_mahasiswa,
                m.id_user,
                m.id_kelas,
                l.name     AS level_name,
                k.name     AS kelas_name,
                s.judul    AS judul_soal,
                m.nim,
                m.name,
                uk.jawaban,
                uk.output,
                uk.nilai,
                uk.waktu,
                uk.created_at,
                uk.updated_at,
                uk.deleted_at
            FROM ujian_kode uk
            LEFT JOIN bank_soal_konversi bsk ON uk.id_bank_soal_konversi = bsk.id
            LEFT JOIN soal      s ON bsk.id_soal      = s.id
            LEFT JOIN mahasiswa m ON uk.id_mahasiswa  = m.id
            LEFT JOIN kelas     k ON m.id_kelas       = k.id
            JOIN      level     l ON uk.id_level      = l.id;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS v_ujian_kode;");
        DB::statement("DROP VIEW IF EXISTS v_bank_soal_konversi;");
    }
};
