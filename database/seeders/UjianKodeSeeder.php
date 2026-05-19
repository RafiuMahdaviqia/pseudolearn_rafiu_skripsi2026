<?php

namespace Database\Seeders;

use App\Models\BankSoalKonversi;
use App\Models\UjianKode;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UjianKodeSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->where('is_admin', 0)->first();
        $bsk = BankSoalKonversi::query()->orderBy('created_at')->first();

        if (!$user || !$bsk) {
            return;
        }

        UjianKode::query()->firstOrCreate(
            [
                'id_bank_soal_konversi' => $bsk->id,
                'id_mahasiswa' => $user->id,
            ],
            [
                'id' => (string) Str::uuid(),
                'id_level' => $bsk->id_level,
                'jawaban' => $bsk->jawaban,
                'output' => $bsk->output,
                'nilai' => 100,
                'waktu' => 60,
            ]
        );
    }
}
