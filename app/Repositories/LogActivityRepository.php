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
 * Class KelasRepository.
 * 
 * @package namespace App\Repositories;
 */
class LogActivityRepository extends BaseRepository
{

    protected $model;
    protected $levelModel;
    protected $mahasiswaModel;
    protected $soalModel;
    protected $ujianModel;
    protected $logDataModel;
    protected $historyConfidenceModel;

    public function __construct()
    {
        $this->model = new HistoryConfidence();
        $this->levelModel = new Level();
        $this->mahasiswaModel = new Mahasiswa();
        $this->soalModel = new Soal();
        $this->ujianModel = new Ujian();
        $this->logDataModel = new LogData();
        $this->historyConfidenceModel = new HistoryConfidence();
    }

    /**
     * Specify the model class name.
     *
     * @return string
     */
    public function model()
    {
        return $this->model;
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
        $query = $this->mahasiswaModel->setView('v_mahasiswa')->orderBy('name', 'asc');

        $kelas = $request->input('kelas');
        if (!is_null($kelas) && $kelas !== '') {
            $query = $query->where('id_kelas', $kelas);
        }

        if (!empty($request->input('search.value'))) {
            $searchValue = $request->input('search.value');
            $query = $query->where('name', 'like', '%' . $searchValue . '%');
        }

        $level = $request->input('level');
        $soal = $request->input('soal');

        // Hitung total records sebelum limit/filter
        $recordsTotal = $query->count();

        // Filter + paging DataTables
        $start  = $request->input('start', 0);
        $length = $request->input('length', 10);

        $data = $query->skip($start)->take($length)->get();

        // Tambahkan data tambahan per mahasiswa
        $data = $data->map(function ($row) use ($level, $soal) {
            $id_mahasiswa = $row['id'];

            $ujianQuery = $this->ujianModel->setView('v_ujian')
                ->where('id_mahasiswa', $id_mahasiswa);
            if (!is_null($level) && $level !== '') {
                $ujianQuery = $ujianQuery->where('id_level', $level);
            }
            if (!is_null($soal) && $soal !== '') {
                $ujianQuery = $ujianQuery->where('id_soal', $soal);
            }
            $totalWaktuDetik = $ujianQuery->sum('waktu');

            $logDataQuery = $this->logDataModel->setView('v_log_data')
                ->where('id_mahasiswa', $id_mahasiswa);
            if (!is_null($level) && $level !== '') {
                $logDataQuery = $logDataQuery->where('id_level', $level);
            }
            if (!is_null($soal) && $soal !== '') {
                $logDataQuery = $logDataQuery->where('id_soal', $soal);
            }
            $totalDrag = $logDataQuery->count();

            $totalSubmit = $ujianQuery->count();

            $hours = floor($totalWaktuDetik / 3600);
            $minutes = floor(($totalWaktuDetik % 3600) / 60);
            $seconds = $totalWaktuDetik % 60;
            $totalWaktu = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

            $row['totalWaktu'] = $totalWaktu;
            $row['totalDrag'] = $totalDrag;
            $row['totalSubmit'] = $totalSubmit;

            return $row;
        });

        return [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $recordsTotal,
            "recordsFiltered" => $recordsTotal,
            "data"            => $data,
        ];
    }

    public function tableDetailLog($request)
    {
        $id_mahasiswa = $request->input('idMahasiswa');
        $id_level = $request->input('idLevel');
        $id_soal = $request->input('idSoal');

        // Ambil semua soal
        $query = $this->historyConfidenceModel->setView('v_history_confidence')
                    ->where('id_mahasiswa', $id_mahasiswa)
                    ->orderBy('created_at', 'asc');
        
        if (!is_null($id_level) && $id_level !== '') {
            $query = $query->where('id_level', $id_level);
        }
        if (!is_null($id_soal) && $id_soal !== '') {
            $query = $query->where('id_soal', $id_soal);
        }

        // Hitung total records sebelum limit/filter
        $recordsTotal = $query->count();

        // if (!empty($request->input('search.value'))) {
        //     $searchValue = $request->input('search.value');
        //     $query = $query->where('name', 'like', '%' . $searchValue . '%');
        // }

        // Filter + paging DataTables
        $start  = $request->input('start', 0);
        $length = $request->input('length', 10);

        $data = $query->skip($start)->take($length)->get();

        return [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $recordsTotal,
            "recordsFiltered" => $recordsTotal, // kalau ada filter tambahan, bisa dihitung ulang
            "data"            => $data,
        ];
    }
}
