<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_konversi;');

        DB::statement("CREATE VIEW v_konversi AS
            SELECT
                k.id,
                k.id_level,
                l.name AS level_name,
                k.id_soal,
                s.judul AS judul_soal,
                s.soal AS soal_name,
                k.jawaban,
                k.output,
                k.bobot,
                k.created_at,
                k.updated_at,
                k.deleted_at,
                s.status
            FROM konversi k
            LEFT JOIN level l ON k.id_level = l.id
            LEFT JOIN soal s ON k.id_soal = s.id
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_konversi;');
    }
};