<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("DROP VIEW IF EXISTS v_pseudo_konversicode");
        DB::statement("CREATE OR REPLACE VIEW v_pseudo_konversicode AS SELECT * FROM (
             SELECT
                u.id_mahasiswa,
                u.id_level,
                s.id AS id_soal,
                NULL AS id_konversi,
                'pseudo' AS jenis_soal,
                s.difficulty,
                COUNT(ld.id) AS langkah,
                u.waktu AS durasi,
                u.created_at AS created_at,
                ROW_NUMBER() OVER (PARTITION BY u.id_mahasiswa, u.id_level, s.id ORDER BY u.created_at) AS attempt_index,
                ROW_NUMBER() OVER (PARTITION BY u.id_mahasiswa, u.id_level ORDER BY u.created_at) AS pair_index,
                ROW_NUMBER() OVER (PARTITION BY u.id_mahasiswa, u.id_level ORDER BY u.created_at) AS event_index

            FROM ujian u
            JOIN soal s ON s.id = u.id_soal
            LEFT JOIN log_data ld 
                ON ld.id_soal = s.id
                AND ld.id_mahasiswa = u.id_mahasiswa

            GROUP BY 
                u.id_mahasiswa,
                u.id_level,
                s.id,
                s.difficulty,
                u.waktu,
                u.created_at

            UNION ALL

            SELECT
                uk.id_mahasiswa,
                uk.id_level,
                bsk.id_soal AS id_soal,
                bsk.id AS id_konversi,
                'konversi' AS jenis_soal,
                bsk.difficulty,
                COUNT(lk.id) AS langkah,
                uk.waktu AS durasi,
                uk.created_at AS created_at,
                ROW_NUMBER() OVER (PARTITION BY uk.id_mahasiswa, uk.id_level, bsk.id_soal ORDER BY uk.created_at) AS attempt_index,
                ROW_NUMBER() OVER (PARTITION BY uk.id_mahasiswa, uk.id_level ORDER BY uk.created_at) AS pair_index,
                ROW_NUMBER() OVER (PARTITION BY uk.id_mahasiswa, uk.id_level ORDER BY uk.created_at) AS event_index

            FROM ujian_kode uk
            JOIN bank_soal_konversi bsk ON bsk.id = uk.id_bank_soal_konversi
            LEFT JOIN log_ujian_kode lk
                ON lk.id_bank_soal_konversi = bsk.id
                AND lk.id_mahasiswa = uk.id_mahasiswa

            GROUP BY
                uk.id_mahasiswa,
                uk.id_level,
                bsk.id_soal,
                bsk.id,
                bsk.difficulty,
                uk.waktu,
                uk.created_at

        ) base ORDER BY created_at ASC");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS v_pseudo_konversicode");
    }
};
