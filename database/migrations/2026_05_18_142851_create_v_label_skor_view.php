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
        // Mengubah tabel sumber dari 'ars_result' menjadi 'label_skor'
        DB::statement("
            CREATE OR REPLACE VIEW v_label_skor AS
            SELECT 
                ls.id AS id,
                ls.id_level AS id_level,
                ls.id_soal AS id_soal,
                ls.id_mahasiswa AS id_mahasiswa,
                m.id_kelas AS id_kelas,
                ls.label AS label,
                ls.skor AS skor,
                ls.created_at AS created_at,
                ls.updated_at AS updated_at,
                ls.deleted_at AS deleted_at
            FROM label_skor ls
            LEFT JOIN mahasiswa m ON ls.id_mahasiswa = m.id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS v_label_skor");
    }
};