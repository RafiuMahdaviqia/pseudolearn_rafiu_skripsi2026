<?php

namespace App\Jobs;

use App\Models\Pencapaian;
use App\Models\Mahasiswa;
use App\Models\Level;
use App\Models\Soal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GeneratePencapaianSoal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $soal;
    protected $kelasId;

    public function __construct($soal, $kelasId = null)
    {
        $this->soal = $soal;
        $this->kelasId = $kelasId;
    }

    public function handle()
    {
        $query = Mahasiswa::query();

        if ($this->kelasId) {
            $query->where('id_kelas', $this->kelasId);
        }

        $mahasiswaList = $query->get();
        $soalData = Soal::find($this->soal->id);

        $insertData = [];
        foreach ($mahasiswaList as $mhs) {
            $insertData[] = [
                'id'           => (string) \Illuminate\Support\Str::uuid(),
                'id_mahasiswa' => $mhs->id,
                'id_level'     => $soalData->id_level,
                'id_soal'      => $soalData->id,
                'id_soal_konversi' => null,
                'category'     => 'soal',
                'img'          => Level::find($soalData->id_level)->image ?? 'default-level.png',
                'name'         => "Menyelesaikan Soal program " . $soalData->judul,
                'desc'         => "Kamu berhasil menyelesaikan soal program " . $soalData->judul,
                'progress'     => 0,
                'max_progress' => 1,
                'status'       => 0,
                'heart'        => 5,
                'date_claimed' => null,
                'created_at'   => now(),
                'updated_at'   => now(),
                'deleted_at'   => null,
            ];

            $insertData[] = [
                'id'           => (string) \Illuminate\Support\Str::uuid(),
                'id_mahasiswa' => $mhs->id,
                'id_level'     => $soalData->id_level,
                'id_soal'      => $soalData->id,
                'id_soal_konversi' => null,
                'category'     => 'badge',
                'img'          => 'assets/media/badge/ideal.png',
                'name'         => "Badge Sempurna Soal Program " . $soalData->judul,
                'desc'         => "Kamu mendapatkan badge sempurna berupa Ideal ataupun Normal",
                'progress'     => 0,
                'max_progress' => 1,
                'status'       => 0,
                'heart'        => 8,
                'date_claimed' => null,
                'created_at'   => now(),
                'updated_at'   => now(),
                'deleted_at'   => null,
            ];
        }

        if (!empty($insertData)) {
            Pencapaian::insert($insertData); // batch insert
        }
    }

}
