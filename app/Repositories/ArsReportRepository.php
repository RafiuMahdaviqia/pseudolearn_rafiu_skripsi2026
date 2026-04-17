<?php

namespace App\Repositories;

use App\Core\BaseResponse;
use App\Models\HistoryConfidence;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\HistoryJawaban;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Models\Level;
use App\Models\Mahasiswa;
use App\Models\Soal;
use App\Models\Ujian;
use App\Models\LogData;

/**
 * Class ArsReportRepository.
 * 
 * @package namespace App\Repositories;
 */
class ArsReportRepository extends BaseRepository
{
    protected $levelModel;
    protected $mahasiswaModel;
    protected $soalModel;

    public function __construct()
    {
        $this->levelModel = new Level();
        $this->mahasiswaModel = new Mahasiswa();
        $this->soalModel = new Soal();
    }

    /**
     * Specify the model class name.
     *
     * @return string
     */
    public function model()
    {
        return Mahasiswa::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function boot()
    {
        // Add your boot logic here
    }

    public function table($request)
    {
        $query = $this->mahasiswaModel
            ->setView('v_mahasiswa')
            ->orderBy('name', 'asc');

        $kelas = $request->input('kelas');
        if (!empty($kelas)) {
            $query->where('id_kelas', $kelas);
        }

        $recordsTotal = $query->count();

        $start  = $request->input('start', 0);
        $length = $request->input('length', 10);

        $data = $query->skip($start)->take($length)->get();

        $data = $data->map(function ($row) {

            $row->totalArs = 0;
            $row->jumlahSoalTambahan = 0;
            $row->totalWaktu = "00:00:00";

            return $row;
        });

        return [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsTotal,
            "data" => $data,
        ];
    }
}