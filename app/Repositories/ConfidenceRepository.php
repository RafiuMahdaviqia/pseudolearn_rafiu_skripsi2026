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

/**
 * Class KelasRepository.
 * 
 * @package namespace App\Repositories;
 */
class ConfidenceRepository extends BaseRepository
{

    protected $model;
    protected $levelModel;
    protected $mahasiswaModel;
    protected $soalModel;

    public function __construct()
    {
        $this->model = new HistoryConfidence();
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

            $yakinSalah = $this->model
            ->where('status_confidence', 1)
            ->where('status_jawaban', 0)
            ->where('id_mahasiswa', $id_mahasiswa);

            if (!is_null($level) && $level !== '') {
                $yakinSalah = $yakinSalah->where('id_level', $level);
            }
            if (!is_null($soal) && $soal !== '') {
                $yakinSalah = $yakinSalah->where('id_soal', $soal);
            }

            $yakinSalahCount = $yakinSalah->count();

            $yakinBenar = $this->model
            ->where('status_confidence', 1)
            ->where('status_jawaban', 1)
            ->where('id_mahasiswa', $id_mahasiswa);

            if (!is_null($level) && $level !== '') {
                $yakinBenar = $yakinBenar->where('id_level', $level);
            }
            if (!is_null($soal) && $soal !== '') {
                $yakinBenar = $yakinBenar->where('id_soal', $soal);
            }

            $yakinBenarCount = $yakinBenar->count();

            $tidakYakinSalah = $this->model
            ->where('status_confidence', 0)
            ->where('status_jawaban', 0)
            ->where('id_mahasiswa', $id_mahasiswa);

            if (!is_null($level) && $level !== '') {
                $tidakYakinSalah = $tidakYakinSalah->where('id_level', $level);
            }
            if (!is_null($soal) && $soal !== '') {
                $tidakYakinSalah = $tidakYakinSalah->where('id_soal', $soal);
            }

            $tidakYakinSalahCount = $tidakYakinSalah->count();

            $tidakYakinBenar = $this->model
            ->where('status_confidence', 0)
            ->where('status_jawaban', 1)
            ->where('id_mahasiswa', $id_mahasiswa);

            if (!is_null($level) && $level !== '') {
                $tidakYakinBenar = $tidakYakinBenar->where('id_level', $level);
            }
            if (!is_null($soal) && $soal !== '') {
                $tidakYakinBenar = $tidakYakinBenar->where('id_soal', $soal);
            }

            $tidakYakinBenarCount = $tidakYakinBenar->count();

            $row->yakinSalah = $yakinSalahCount;
            $row->yakinBenar = $yakinBenarCount;
            $row->tidakYakinSalah = $tidakYakinSalahCount;
            $row->tidakYakinBenar = $tidakYakinBenarCount;

            return $row;
        });

        return [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $recordsTotal,
            "recordsFiltered" => $recordsTotal,
            "data"            => $data,
        ];
    }

    public function tableConfidence($request)
    {
        $query = $this->model->setView('v_history_confidence')
                                ->where('id_mahasiswa', $request->input('idMahasiswa'))
                                ->orderBy('created_at', 'desc');

        $id_level = $request->input('idLevel');
        $id_soal = $request->input('idSoal');

        if (!is_null($id_level) && $id_level !== '') {
            $query = $query->where('id_level', $id_level);
        }
        if (!is_null($id_soal) && $id_soal !== '') {
            $query = $query->where('id_soal', $id_soal);
        }

        // Hitung total records sebelum limit/filter
        $recordsTotal = $query->count();

        // Filter + paging DataTables
        $start  = $request->input('start', 0);
        $length = $request->input('length', 10);

        $data = $query->skip($start)->take($length)->get();

        // Konversi kolom status_jawaban dan status_confidence
        $data = $data->map(function ($item) {
            // status_jawaban: 0 = salah, 1 = benar
            $item->status_jawaban_text = $item->status_jawaban == 1 ? 'benar' : 'salah';
            // status_confidence: 0 = tidak yakin, 1 = yakin
            $item->status_confidence_text = $item->status_confidence == 1 ? 'yakin' : 'tidak yakin';
            return $item;
        });

        return [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $recordsTotal,
            "recordsFiltered" => $recordsTotal, // kalau ada filter tambahan, bisa dihitung ulang
            "data"            => $data,
        ];
    }
}
