<?php

namespace Database\Seeders;

use App\Models\LogData;
use App\Models\Mahasiswa;
use App\Models\Soal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LogDataSeeder extends Seeder
{
    public function run(): void
    {
        $mhs = Mahasiswa::query()->first();
        $soal = Soal::query()->orderBy('order')->first();

        if (!$mhs || !$soal) {
            return;
        }

        LogData::query()->firstOrCreate(
            [
                'id_mahasiswa' => $mhs->id,
                'id_soal' => $soal->id,
                'index' => 1,
            ],
            [
                'id' => (string) Str::uuid(),
                'itemText' => 'Menambahkan elemen ke antrian',
                'timer_second' => 10,
                'type' => 'pseudo',
                'variabel' => json_encode(['front' => 0, 'rear' => 1]),
            ]
        );
    }
}
