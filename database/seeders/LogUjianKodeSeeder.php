<?php

namespace Database\Seeders;

use App\Models\BankSoalKonversi;
use App\Models\LogUjianKode;
use App\Models\Mahasiswa;
use App\Models\Soal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LogUjianKodeSeeder extends Seeder
{
    public function run(): void
    {
        $mhs = Mahasiswa::query()->first();
        $bsk = BankSoalKonversi::query()->orderBy('created_at')->first();
        $soal = $bsk ? Soal::query()->find($bsk->id_soal) : null;

        if (!$mhs || !$bsk) {
            return;
        }

        LogUjianKode::query()->firstOrCreate(
            [
                'id_mahasiswa' => $mhs->id,
                'id_bank_soal_konversi' => $bsk->id,
                'index' => 1,
            ],
            [
                'id' => (string) Str::uuid(),
                'id_level' => $bsk->id_level,
                'item_text' => 'Compile & run',
            ]
        );
    }
}
