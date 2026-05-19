<?php

namespace Database\Seeders;

use App\Models\Konversi;
use App\Models\Mahasiswa;
use App\Models\UjianKonversi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UjianKonversiSeeder extends Seeder
{
    public function run(): void
    {
        $mhs = Mahasiswa::query()->first();
        $konversi = Konversi::query()->first();

        if (!$mhs || !$konversi) {
            return;
        }

        UjianKonversi::query()->firstOrCreate(
            [
                'id_mahasiswa' => $mhs->id,
                'id_level' => $konversi->id_level,
                'id_soal_konversi' => $konversi->id,
            ],
            [
                'id' => (string) Str::uuid(),
                'jawaban' => ['contoh' => 'jawaban konversi'],
                'output' => 'OK',
                'waktu' => 60,
                'nilai' => 100,
            ]
        );
    }
}
