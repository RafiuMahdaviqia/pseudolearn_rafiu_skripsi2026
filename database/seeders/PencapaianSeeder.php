<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use App\Models\Pencapaian;
use App\Models\Soal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PencapaianSeeder extends Seeder
{
    public function run(): void
    {
        $mhs = Mahasiswa::query()->first();
        $soal = Soal::query()->orderBy('order')->first();

        if (!$mhs || !$soal) {
            return;
        }

        Pencapaian::query()->firstOrCreate(
            [
                'id_mahasiswa' => $mhs->id,
                'category' => 'badge',
                'name' => 'Pemula',
            ],
            [
                'id' => (string) Str::uuid(),
                'id_level' => $soal->id_level,
                'id_soal' => $soal->id,
                'img' => null,
                'desc' => 'Menyelesaikan soal pertama.',
                'progress' => 1,
                'max_progress' => 1,
                'status' => 1,
                'heart' => 1,
                'date_claimed' => null,
            ]
        );
    }
}
