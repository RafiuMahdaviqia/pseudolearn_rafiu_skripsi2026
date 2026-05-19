<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SoalDelaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = database_path('seeders/data/soal_dela.sql');

        if (!File::exists($filePath)) {
            $this->command->error("File soal_dela.sql tidak ditemukan di $filePath");
            return;
        }

        $sql = File::get($filePath);

        try {
            DB::unprepared($sql);
            $this->command->info('Data soal_dela.sql berhasil dimasukkan ke database.');
        } catch (\Exception $e) {
            $this->command->error('Gagal menjalankan seeder soal_dela.sql: ' . $e->getMessage());
        }
    }
}