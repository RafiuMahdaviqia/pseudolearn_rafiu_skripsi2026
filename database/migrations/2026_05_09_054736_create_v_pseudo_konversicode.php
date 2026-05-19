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
        DB::statement("DROP VIEW IF EXISTS v_pseudo_konversicode;");

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
                u.created_at AS created_at

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
                k.id_soal,
                k.id AS id_konversi,
                'konversi' AS jenis_soal,
                s.difficulty,

                CASE 
                    WHEN uk.nilai = 100 THEN 3
                    WHEN uk.nilai >= 70 THEN 5
                    ELSE 8
                END AS langkah,

                uk.waktu AS durasi,
                uk.created_at AS created_at

            FROM ujian_konversi uk
            JOIN konversi k ON k.id = uk.id_soal_konversi
            JOIN soal s ON s.id = k.id_soal

        ) base ORDER BY created_at ASC");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS v_pseudo_konversicode;");
    }
};
