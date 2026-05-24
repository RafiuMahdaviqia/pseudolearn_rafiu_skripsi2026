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
        // Membuat atau menimpa SQL View v_mahasiswa sesuai dengan cetak biru gambar
        DB::statement("
            CREATE OR REPLACE VIEW v_mahasiswa AS
            SELECT 
                m.id AS id,
                m.id_user AS id_user,
                m.id_kelas AS id_kelas,
                k.name AS kelas_name,
                k.angkatan AS angkatan,
                m.nim AS nim,
                u.name AS name,
                u.email AS email,
                m.jenis_kelamin AS jenis_kelamin,
                u.avatar AS avatar,
                m.open_panduan AS open_panduan,
                m.created_at AS created_at,
                m.updated_at AS updated_at,
                u.deleted_at AS deleted_at
            FROM mahasiswa m
            JOIN users u ON m.id_user = u.id
            LEFT JOIN kelas k ON m.id_kelas = k.id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS v_mahasiswa");
    }
};