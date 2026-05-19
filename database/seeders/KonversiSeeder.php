<?php

namespace Database\Seeders;

use App\Models\Konversi;
use App\Models\Soal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KonversiSeeder extends Seeder
{
    public function run(): void
    {
        $soal = Soal::query()->orderBy('order')->first();
        if (!$soal) {
            return;
        }

        Konversi::query()->firstOrCreate(
            ['id_soal' => $soal->id],
            [
                'id' => (string) Str::uuid(),
                'id_level' => $soal->id_level,
                'jawaban' => ['contoh' => 'jawaban'],
                'output' => 'OK',
                'bobot' => 1,
            ]
        );
    }
}
