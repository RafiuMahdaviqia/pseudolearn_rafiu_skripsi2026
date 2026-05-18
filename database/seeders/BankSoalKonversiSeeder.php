<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class BankSoalKonversiSeeder extends Seeder
{
    public function run(): void
    {
        // Kosongkan data lama agar tidak duplikat saat dijalankan ulang
        Schema::disableForeignKeyConstraints();
        try {
            DB::table('bank_soal_konversi')->truncate();
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        $path = database_path('seeders/data/soal_dela.sql');

        if (!File::exists($path)) {
            $this->command->error('File soal_dela.sql tidak ditemukan.');
            return;
        }

        try {
            // Baca dan eksekusi file SQL mentah
            $sql = File::get($path);
            DB::unprepared($sql);

            $this->command->info('Data soal konversi Dela berhasil dimasukkan!');
        } catch (\Throwable $e) {
            $this->command->error('Gagal memasukkan data dari soal_dela.sql: ' . $e->getMessage());
        }
    }
}
