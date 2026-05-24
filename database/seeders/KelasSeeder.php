<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        Kelas::query()->updateOrCreate(
            ['name' => 'Kelas A'],
            [
                'id' => (string) Str::uuid(),
                'angkatan' => '2026',
            ]
        );
    }
}
