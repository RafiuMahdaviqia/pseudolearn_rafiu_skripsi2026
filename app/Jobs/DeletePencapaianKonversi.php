<?php

namespace App\Jobs;

use App\Models\Pencapaian;
use App\Models\Mahasiswa;
use App\Models\Level;
use App\Models\Soal;
use App\Models\Konversi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeletePencapaianKonversi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $konversi;
    protected $kelasId;

    public function __construct($konversi, $kelasId = null)
    {
        $this->konversi = $konversi;
        $this->kelasId = $kelasId;
    }

    public function handle()
    {
        $dataSoal = Soal::find($this->konversi->id_soal);
        $query = Pencapaian::where('id_soal', $dataSoal->id)->where('category', 'konversi');

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
