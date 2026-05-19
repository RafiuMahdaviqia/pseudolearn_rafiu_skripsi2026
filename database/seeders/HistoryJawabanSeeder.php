<?php

namespace Database\Seeders;

use App\Models\HistoryJawaban;
use App\Models\Mahasiswa;
use App\Models\Soal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HistoryJawabanSeeder extends Seeder
{
    public function run(): void
    {
        $mhs = Mahasiswa::query()->first();
        $soal = Soal::query()->orderBy('order')->first();

        if (!$mhs || !$soal) {
            return;
        }

        HistoryJawaban::query()->firstOrCreate(
            [
                'id_mahasiswa' => $mhs->id,
                'id_soal' => $soal->id,
            ],
            [
                'id' => (string) Str::uuid(),
                'id_level' => $soal->id_level,
                'index_tipe_data' => 0,
                'tipe_data' => 'array',
                'index_algoritma' => 0,
                'algoritma' => 'queue',
                'status' => 'benar',
            ]
        );
    }
}
