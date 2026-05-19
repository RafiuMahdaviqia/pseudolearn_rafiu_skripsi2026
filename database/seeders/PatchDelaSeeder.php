<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PatchDelaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/patch_dela.sql');

        if (File::exists($path)) {
            // 1. Matikan Foreign Key Check sementara agar tidak error saat menimpa data
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // 2. Kosongkan ketiga tabel agar tidak terjadi duplikat ID
            DB::table('log_ujian_kode')->truncate();
            DB::table('ujian_kode')->truncate();
            DB::table('bank_soal_konversi')->truncate();

            // 3. Baca dan jalankan perintah SQL gabungan dari Dela
            $sql = File::get($path);
            DB::unprepared($sql);

            // 4. Nyalakan kembali Foreign Key Check untuk keamanan database
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $this->command->info('Patch data Bank Soal, Ujian Kode, dan Log dari Dela BERHASIL diterapkan!');
        } else {
            $this->command->error('Gagal: File patch_dela.sql tidak ditemukan di folder database/seeders/data/');
        }
    }
}