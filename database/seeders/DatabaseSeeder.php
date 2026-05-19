<?php

namespace Database\Seeders;

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
            UserSeeder::class,
            KelasSeeder::class,
            LevelSeeder::class,
            SoalSeeder::class,
            MahasiswaSeeder::class,
            SettingsSeeder::class,
            GuideSeeder::class,
            NyawaSeeder::class,
            UjianSeeder::class,
            KonversiSeeder::class,
            UjianKonversiSeeder::class,
            LogDataSeeder::class,
            HistoryJawabanSeeder::class,
            HistoryConfidenceSeeder::class,
            LabelSkorSeeder::class,
            PencapaianSeeder::class,
            ArsResultSeeder::class,
            UjianKodeSeeder::class,
            LogUjianKodeSeeder::class,
            DebugKonversiSeeder::class,
            NilaiTestSeeder::class,
            ChatbotAccessLogCleanupSeeder::class,
            ChatbotSeeder::class,
            PatchAfifahSeeder::class,
            PatchDelaSeeder::class,
        ]);
    }
}
