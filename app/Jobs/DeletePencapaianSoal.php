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

class DeletePencapaianSoal implements ShouldQueue
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
        $query = Pencapaian::where('id_soal', $this->soal->id)->whereIn('category', ['soal', 'badge', 'konversi']);

        if ($this->kelasId !== null) {
            $mahasiswaIds = Mahasiswa::where('id_kelas', $this->kelasId)->pluck('id');
            if ($mahasiswaIds->isEmpty()) {
                return;
            }
            $query->whereIn('id_mahasiswa', $mahasiswaIds);
        }

        $query->delete();
    }

}
