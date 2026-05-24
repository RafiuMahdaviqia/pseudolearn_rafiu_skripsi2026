<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->where('is_admin', 0)->first();
        if (!$user) {
            return;
        }

        $kelas = Kelas::query()->first();

        Mahasiswa::query()->updateOrCreate(
            ['id_user' => $user->id],
            [
                'id' => (string) Str::uuid(),
                'id_kelas' => $kelas?->id,
                'nim' => '20260001',
                'name' => $user->name,
                'jenis_kelamin' => 'L',
                'open_panduan' => 0,
            ]
        );
    }
}
