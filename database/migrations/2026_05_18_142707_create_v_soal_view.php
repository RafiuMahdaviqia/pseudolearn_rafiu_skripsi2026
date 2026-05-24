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
        // Membuat atau menimpa SQL View v_soal sesuai dengan cetak biru gambar
        DB::statement("
            CREATE OR REPLACE VIEW v_soal AS
            SELECT 
                s.id AS id,
                s.judul AS judul,
                s.soal AS soal,
                s.id_level AS id_level,
                l.name AS level_name,
                s.created_at AS created_at,
                s.deleted_at AS deleted_at
            FROM soal s
            LEFT JOIN level l ON s.id_level = l.id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS v_soal");
    }
};