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
        DB::statement("DROP VIEW IF EXISTS v_bank_soal_konversi;");

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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS v_bank_soal_konversi;");

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
                bsk.created_at,
                bsk.updated_at,
                bsk.deleted_at,
                s.status
            FROM bank_soal_konversi bsk
            LEFT JOIN level l ON bsk.id_level = l.id
            LEFT JOIN soal  s ON bsk.id_soal  = s.id;
        ");
    }
};
