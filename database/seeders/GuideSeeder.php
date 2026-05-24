<?php

namespace Database\Seeders;

use App\Models\Guide;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GuideSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['order' => 1, 'judul' => 'Mulai Belajar', 'desc' => 'Pilih level dan mulai mengerjakan soal.', 'img' => null],
            ['order' => 2, 'judul' => 'Kerjakan Pseudo', 'desc' => 'Selesaikan pseudo-code dan lihat langkah.', 'img' => null],
            ['order' => 3, 'judul' => 'Lanjut Konversi', 'desc' => 'Konversikan solusi dan cek nilai.', 'img' => null],
        ];

        foreach ($rows as $row) {
            Guide::query()->firstOrCreate(
                ['order' => $row['order']],
                array_merge($row, ['id' => (string) Str::uuid()])
            );
        }
    }
}
