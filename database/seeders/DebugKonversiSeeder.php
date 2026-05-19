<?php

namespace Database\Seeders;

use App\Models\DebugKonversi;
use App\Models\Konversi;
use App\Models\Mahasiswa;
use App\Models\Soal;
use App\Models\UjianKonversi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DebugKonversiSeeder extends Seeder
{
    public function run(): void
    {
        $mhs = Mahasiswa::query()->first();
        $soal = Soal::query()->orderBy('order')->first();
        $konversi = Konversi::query()->first();
        $ujianKonversi = UjianKonversi::query()->first();

        if (!$mhs || !$soal || !$konversi) {
            return;
        }

        DebugKonversi::query()->firstOrCreate(
            [
                'id_mahasiswa' => $mhs->id,
                'id_soal_konversi' => $konversi->id,
            ],
            [
                'id' => (string) Str::uuid(),
                'id_level' => $soal->id_level,
                'id_soal' => $soal->id,
                'id_ujian_konversi' => $ujianKonversi?->id,
                'debug' => ['note' => 'seeded debug'],
            ]
        );
    }
}
