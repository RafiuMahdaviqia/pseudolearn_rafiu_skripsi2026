<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use App\Models\Soal;
use App\Models\Ujian;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UjianSeeder extends Seeder
{
    public function run(): void
    {
        $mhs = Mahasiswa::query()->first();
        $soal = Soal::query()->orderBy('order')->first();

        if (!$mhs || !$soal) {
            return;
        }

        Ujian::query()->firstOrCreate(
            [
                'id_mahasiswa' => $mhs->id,
                'id_level' => $soal->id_level,
                'id_soal' => $soal->id,
            ],
            [
                'id' => (string) Str::uuid(),
                'waktu' => now()->timestamp,
                'status' => 'done',
            ]
        );
    }
}
