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

class DeletePencapaianMahasiswa implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $mahasiswa;

    public function __construct($mahasiswa)
    {
        $this->mahasiswa = $mahasiswa;
    }

    public function handle()
    {
        $query = Pencapaian::where('id_mahasiswa', $this->mahasiswa->id);

        $query->delete();
    }

}
