<?php

namespace Database\Seeders;

use App\Models\BankSoalKonversi;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // 1. Data Master (Induk)
            UserSeeder::class,
            KelasSeeder::class,
            LevelSeeder::class, // Harus di depan
            
            // 2. Data Konten (Soal & Konversi)
            SoalQueueSeeder::class, 
            BankSoalKonversiSeeder::class,
            PatchAfifahSeeder::class, // Cukup satu kali
            PatchDelaSeeder::class,   // Pastikan ini aman dari tabel 'konversi' lama
            PatchSoalSeeder::class,
            
            // 3. Data Mahasiswa & Master Lainnya
            MahasiswaSeeder::class,
            SettingsSeeder::class,
            GuideSeeder::class,
            
            // 4. Data Transaksional (Anak)
            NyawaSeeder::class,
            UjianSeeder::class,
            UjianKodeSeeder::class,
            LabelSkorSeeder::class,
            ArsResultSeeder::class,
            PencapaianSeeder::class,
            
            // 5. Data Log & Maintenance
            LogDataSeeder::class,
            HistoryJawabanSeeder::class,
            HistoryConfidenceSeeder::class,
            LogUjianKodeSeeder::class,
            NilaiTestSeeder::class,
            ChatbotAccessLogCleanupSeeder::class,
            ChatbotSeeder::class,
        ]);
    }
}
