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
                ROW_NUMBER() OVER (
                    PARTITION BY u.id_mahasiswa, u.id_level, s.id
                    ORDER BY u.created_at ASC
                ) AS attempt_index,
                DENSE_RANK() OVER (
                    PARTITION BY u.id_mahasiswa, u.id_level
                    ORDER BY s.difficulty ASC, u.created_at ASC
                ) AS pair_index

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
                m.id AS id_mahasiswa,
                uk.id_level,
                bsk.id_soal,
                bsk.id AS id_konversi,
                'konversi' AS jenis_soal,
                s.difficulty,
                COUNT(luk.id) AS langkah,
                uk.waktu AS durasi,
                uk.created_at AS created_at,
                ROW_NUMBER() OVER (
                    PARTITION BY m.id, uk.id_level, bsk.id_soal
                    ORDER BY uk.created_at ASC
                ) AS attempt_index,
                DENSE_RANK() OVER (
                    PARTITION BY m.id, uk.id_level
                    ORDER BY s.difficulty ASC, uk.created_at ASC
                ) AS pair_index

            FROM ujian_kode uk
            JOIN users u ON u.id = uk.id_mahasiswa
            JOIN mahasiswa m ON m.id_user = u.id
            JOIN bank_soal_konversi bsk ON bsk.id = uk.id_bank_soal_konversi
            JOIN soal s ON s.id = bsk.id_soal
            LEFT JOIN log_ujian_kode luk 
                ON luk.id_bank_soal_konversi = bsk.id
                AND luk.id_mahasiswa = m.id

            GROUP BY 
                m.id,
                uk.id_level,
                bsk.id_soal,
                bsk.id,
                s.difficulty,
                uk.waktu,
                uk.created_at

        ) base ORDER BY created_at ASC");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
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
                ROW_NUMBER() OVER (
                    PARTITION BY u.id_mahasiswa, u.id_level, s.id
                    ORDER BY u.created_at ASC
                ) AS attempt_index,
                DENSE_RANK() OVER (
                    PARTITION BY u.id_mahasiswa, u.id_level
                    ORDER BY s.difficulty ASC, u.created_at ASC
                ) AS pair_index

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
                bsk.id_soal,
                bsk.id AS id_konversi,
                'konversi' AS jenis_soal,
                s.difficulty,
                COUNT(luk.id) AS langkah,
                uk.waktu AS durasi,
                uk.created_at AS created_at,
                ROW_NUMBER() OVER (
                    PARTITION BY uk.id_mahasiswa, uk.id_level, bsk.id_soal
                    ORDER BY uk.created_at ASC
                ) AS attempt_index,
                DENSE_RANK() OVER (
                    PARTITION BY uk.id_mahasiswa, uk.id_level
                    ORDER BY s.difficulty ASC, uk.created_at ASC
                ) AS pair_index

            FROM ujian_kode uk
            JOIN bank_soal_konversi bsk ON bsk.id = uk.id_bank_soal_konversi
            JOIN soal s ON s.id = bsk.id_soal
            LEFT JOIN log_ujian_kode luk 
                ON luk.id_bank_soal_konversi = bsk.id
                AND luk.id_mahasiswa = uk.id_mahasiswa

            GROUP BY 
                uk.id_mahasiswa,
                uk.id_level,
                bsk.id_soal,
                bsk.id,
                s.difficulty,
                uk.waktu,
                uk.created_at

        ) base ORDER BY created_at ASC");
    }
};
