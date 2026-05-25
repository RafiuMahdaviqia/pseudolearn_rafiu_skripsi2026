<?php

namespace Database\Seeders;

use App\Models\BankSoalKonversi;
use App\Models\ArsResult;
use App\Models\Mahasiswa;
use App\Models\Soal;
use Illuminate\Database\Seeder;

class ArsResultSeeder extends Seeder
{
    public function run(): void
    {
        $mhs = Mahasiswa::query()->first();
        $bsk = BankSoalKonversi::query()->orderBy('created_at')->first();
        $soal = $bsk ? Soal::query()->find($bsk->id_soal) : null;

        if (!$mhs || !$soal) {
            return;
        }

        ArsResult::query()->updateOrCreate(
            [
                'id_mahasiswa' => $mhs->id,
                'id_level' => $soal->id_level,
                'id_soal' => $soal->id,
            ],
            [
                'ars_batch' => 1,
                'difficulty' => $soal->difficulty,
                'pseudo_langkah' => 3,
                'pseudo_durasi' => 120,
                'pseudo_label' => 'Struggling',
                'pseudo_score' => 30,
                'konversi_langkah' => 3,
                'konversi_durasi' => 60,
                'konversi_label' => 'Normal',
                'konversi_score' => 70,
            ]
        );
    }
}
