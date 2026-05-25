<?php

namespace Database\Seeders;

use App\Models\BankSoalKonversi;
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
        $bsk = BankSoalKonversi::query()->orderBy('created_at')->first();
        $soal = $bsk ? Soal::query()->find($bsk->id_soal) : null;

        if (!$mhs || !$soal) {
            return;
        }

        for ($index = 1; $index <= 18; $index++) {
            LogData::query()->firstOrCreate(
                [
                    'id_mahasiswa' => $mhs->id,
                    'id_soal' => $soal->id,
                    'index' => $index,
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
}
