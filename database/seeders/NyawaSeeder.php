<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use App\Models\Nyawa;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NyawaSeeder extends Seeder
{
    public function run(): void
    {
        $mhs = Mahasiswa::query()->first();
        if (!$mhs) {
            return;
        }

        $max = (int) (Setting::getValue('nyawa.max', 100));

        Nyawa::query()->updateOrCreate(
            ['id_mahasiswa' => $mhs->id],
            [
                'id' => (string) Str::uuid(),
                'id_user' => $mhs->id_user,
                'nyawa' => $max,
                'max_nyawa' => $max,
                'next_regen_at' => null,
            ]
        );
    }
}
