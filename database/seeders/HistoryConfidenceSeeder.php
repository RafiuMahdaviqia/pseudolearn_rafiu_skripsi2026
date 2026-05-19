<?php

namespace Database\Seeders;

use App\Models\HistoryConfidence;
use App\Models\Mahasiswa;
use App\Models\Soal;
use App\Models\Ujian;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HistoryConfidenceSeeder extends Seeder
{
    public function run(): void
    {
        $mhs = Mahasiswa::query()->first();
        $soal = Soal::query()->orderBy('order')->first();
        $ujian = Ujian::query()->first();

        if (!$mhs || !$soal) {
            return;
        }

        HistoryConfidence::query()->firstOrCreate(
            [
                'id_mahasiswa' => $mhs->id,
                'id_soal' => $soal->id,
            ],
            [
                'id' => (string) Str::uuid(),
                'id_level' => $soal->id_level,
                'status_jawaban' => 'benar',
                'status_confidence' => 'yakin',
                'id_ujian' => $ujian?->id,
            ]
        );
    }
}
