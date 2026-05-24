<?php

namespace Database\Seeders;

use App\Models\LabelSkor;
use App\Models\Mahasiswa;
use App\Models\Soal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LabelSkorSeeder extends Seeder
{
    public function run(): void
    {
        $mhs = Mahasiswa::query()->first();
        $soal = Soal::query()->orderBy('order')->first();

        if (!$mhs || !$soal) {
            return;
        }

        LabelSkor::query()->firstOrCreate(
            [
                'id_mahasiswa' => $mhs->id,
                'id_soal' => $soal->id,
            ],
            [
                'id' => (string) Str::uuid(),
                'id_level' => $soal->id_level,
                'label' => 'Sangat Baik',
                'skor' => 100,
            ]
        );
    }
}
