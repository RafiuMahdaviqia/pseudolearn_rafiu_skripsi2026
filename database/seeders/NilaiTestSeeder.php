<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use App\Models\NilaiTest;
use App\Models\Soal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NilaiTestSeeder extends Seeder
{
    public function run(): void
    {
        $mhs = Mahasiswa::query()->first();
        $soal = Soal::query()->orderBy('order')->first();

        if (!$mhs || !$soal) {
            return;
        }

        NilaiTest::query()->updateOrCreate(
            [
                'id_mahasiswa' => $mhs->id,
                'id_level' => $soal->id_level,
            ],
            [
                'id' => (string) Str::uuid(),
                'pre_test' => 40,
                'post_test' => 80,
            ]
        );
    }
}
