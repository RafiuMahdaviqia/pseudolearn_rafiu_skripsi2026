<?php

namespace Database\Seeders;

use App\Models\ChatbotAccessLog;
use App\Models\ChatbotLog;
use App\Models\Mahasiswa;
use App\Models\Soal;
use Illuminate\Database\Seeder;

class ChatbotSeeder extends Seeder
{
    public function run(): void
    {
        $mhs = Mahasiswa::query()->first();
        $soal = Soal::query()->orderBy('order')->first();

        if (!$mhs || !$soal) {
            return;
        }

        $access = ChatbotAccessLog::query()->create([
            'id_mahasiswa' => $mhs->id,
            'id_level' => $soal->id_level,
            'id_soal' => $soal->id,
            'type' => 'biasa',
            'opened_at' => now(),
            'closed_at' => null,
            'durasi_menit' => null,
        ]);

        ChatbotLog::query()->create([
            'id_mahasiswa' => $mhs->id,
            'access_id' => $access->id,
            'id_level' => $soal->id_level,
            'id_soal' => $soal->id,
            'type' => 'biasa',
            'pesan' => 'Halo, bantu saya memahami soal ini.',
            'respons' => 'Baik, mari kita pecah jadi langkah-langkah sederhana.',
        ]);
    }
}
