<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PatchAfifahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/patch_afifah.sql');

        if (File::exists($path)) {
            // Karena ini data master, kita jalankan langsung file SQL-nya
            // (Penonaktifan Foreign Key sudah kita taruh di dalam file SQL)
            
            $sql = File::get($path);
            DB::unprepared($sql);

            $this->command->info('Data Master ARS dari Afifah BERHASIL disuntikkan!');
        } else {
            $this->command->error('Gagal: File patch_afifah.sql tidak ditemukan.');
        }
    }
}