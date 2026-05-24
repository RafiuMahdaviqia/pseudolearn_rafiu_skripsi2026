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
        DB::statement("
            CREATE OR REPLACE VIEW v_log_data AS
            SELECT 
                ld.id AS id,
                ld.id_soal AS id_soal,
                s.id_level AS id_level, -- Perubahan di sini: Mengambil dari tabel soal (s)
                s.judul AS judul,
                ld.id_mahasiswa AS id_mahasiswa,
                m.nim AS nim,
                u.name AS name,
                m.id_kelas AS id_kelas,
                ld.index AS `index`,
                ld.itemText AS itemText,
                ld.timer_second AS timer_second,
                ld.type AS type,
                ld.variabel AS variabel,
                ld.created_at AS created_at,
                ld.updated_at AS updated_at,
                ld.deleted_at AS deleted_at
            FROM log_data ld
            LEFT JOIN soal s ON ld.id_soal = s.id
            LEFT JOIN mahasiswa m ON ld.id_mahasiswa = m.id
            LEFT JOIN users u ON m.id_user = u.id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS v_log_data");
    }
};