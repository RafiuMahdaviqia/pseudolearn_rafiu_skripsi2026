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

class GeneratePencapaianMahasiswa implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $mahasiswa;
    protected $level_id;

    public function __construct($mahasiswa, $level_id = null)
    {
        $this->mahasiswa = $mahasiswa;
        $this->level_id = $level_id;
    }

    public function handle()
    {
        $query = Soal::query();

        if ($this->level_id) {
            $query->where('id_level', $this->level_id);
        }

        $soalList = $query->get();
        $konversiList = Konversi::where('id_level', $this->level_id)->get();
        $id_mahasiswa = is_array($this->mahasiswa) ? ($this->mahasiswa['id'] ?? null) : ($this->mahasiswa->id ?? null);

        $insertData = [];
        foreach ($soalList as $soal) {
            $insertData[] = [
                'id'           => (string) \Illuminate\Support\Str::uuid(),
                'id_mahasiswa' => $id_mahasiswa,
                'id_level'     => $this->level_id,
                'id_soal'      => $soal->id,
                'id_soal_konversi' => null,
                'category'     => 'soal',
                'img'          => Level::find($this->level_id)->image ?? 'default-level.png',
                'name'         => "Menyelesaikan Soal program " . $soal->judul,
                'desc'         => "Kamu berhasil menyelesaikan soal program " . $soal->judul,
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
                'id_mahasiswa' => $id_mahasiswa,
                'id_level'     => $this->level_id,
                'id_soal'      => $soal->id,
                'id_soal_konversi' => null,
                'category'     => 'badge',
                'img'          => 'assets/media/badge/ideal.png',
                'name'         => "Badge Sempurna Soal Program " . $soal->judul,
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

        foreach ($konversiList as $konversi) {
            $soalData = Soal::where('id', $konversi->id_soal)->first();
            $insertData[] = [
                'id'           => (string) \Illuminate\Support\Str::uuid(),
                'id_mahasiswa' => $id_mahasiswa,
                'id_level'     => $this->level_id,
                'id_soal'      => $konversi->id_soal,
                'id_soal_konversi' => $konversi->id,
                'category'     => 'konversi',
                'img'          => Level::find($this->level_id)->image ?? 'default-level.png',
                'name'         => "Menyelesaikan Soal Konversi " . $soalData->judul,
                'desc'         => "Kamu berhasil menyelesaikan soal konversi " . $soalData->judul,
                'progress'     => 0,
                'max_progress' => 1,
                'status'       => 0,
                'heart'        => 5,
                'date_claimed' => null,
                'created_at'   => now(),
                'updated_at'   => now(),
                'deleted_at'   => null,
            ];
        }

        for ($i=0; $i < 3; $i++) { 
            $textName = '';
            switch ($i) {
                case 0:
                    $textName = 'Satu';
                    break;
                case 1:
                    $textName = 'Dua';
                    break;
                case 2:
                    $textName = 'Tiga';
                    break;
                
                default:
                    $textName = '';
                    break;
            }

            $insertData[] = [
                'id'           => (string) \Illuminate\Support\Str::uuid(),
                'id_mahasiswa' => $id_mahasiswa,
                'id_level'     => null,
                'id_soal'      => null,
                'id_soal_konversi' => null,
                'category'     => 'leaderboard',
                'img'          => 'assets/media/badge/icon_juara' . $i+1 . '.png',
                'name'         => "Juara " . $textName,
                'desc'         => "Mendapatkan juara " . $i+1 . " pada papan peringkat",
                'progress'     => 0,
                'max_progress' => 1,
                'status'       => 0,
                'heart'        => 10,
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
