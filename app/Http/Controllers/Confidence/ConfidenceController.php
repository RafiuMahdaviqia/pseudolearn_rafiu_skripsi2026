<?php


namespace App\Http\Controllers\Confidence;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ConfidenceService;
use App\Models\Level;
use App\Models\Soal;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\HistoryConfidence;

class ConfidenceController extends Controller
{

    protected $confidenceService;
    protected $levelModel;
    protected $soalModel;
    protected $kelasModel;
    protected $mahasiswaModel;
    protected $model;


    public function __construct()
    {
        $this->confidenceService = new ConfidenceService();
        $this->levelModel = new Level();
        $this->soalModel = new Soal();
        $this->kelasModel = new Kelas();
        $this->mahasiswaModel = new Mahasiswa();
        $this->model = new HistoryConfidence();
    }

    public function index()
    {
        $list_kelas = $this->kelasModel->get(['id', 'name', 'angkatan'])->toArray();

        $list_kelas = collect($list_kelas)->prepend(['id' => '', 'name' => 'Semua Kelas', 'angkatan' => '']);

        $list_kelas = collect($list_kelas)->map(function ($item) {
            return [
                'id' => $item['id'],
                'name' => $item['name'],
                'angkatan' => $item['angkatan']
            ];
        })->values()->toArray();

        $list_level = $this->levelModel->orderBy('order', 'asc')->get(['id', 'name'])
            ->pluck('name', 'id')
            ->toArray();

        // $list_level = collect($list_level)->prepend('Semua Level', '');

        $list_level = collect($list_level)->map(function ($name, $id) {
            return [
                'id' => $id,
                'name' => $name
            ];
        })->values()->toArray();

        return view('pages.confidence.index', [
            'title' => 'Confidence Tag',
            'list_kelas' => $list_kelas,
            'list_level' => $list_level
        ]);
    }

    public function table(Request $request)
    {
        $data = $this->confidenceService->table($request);
        return $data;
    }

    public function detailSoal($id)
    {
        $levelId = request()->query('level');
        $soalId = request()->query('soal');
        $idMahasiswa = $id;

        $dataMahasiswa = $this->mahasiswaModel->setView('v_mahasiswa')->find($idMahasiswa);

        $yakinSalah = $this->model
        ->where('status_confidence', 1)
        ->where('status_jawaban', 0)
        ->where('id_mahasiswa', $idMahasiswa);

        if (!is_null($levelId) && $levelId !== '') {
            $yakinSalah = $yakinSalah->where('id_level', $levelId);
        }
        if (!is_null($soalId) && $soalId !== '') {
            $yakinSalah = $yakinSalah->where('id_soal', $soalId);
        }

        $yakinSalahCount = $yakinSalah->count();

        $yakinBenar = $this->model
        ->where('status_confidence', 1)
        ->where('status_jawaban', 1)
        ->where('id_mahasiswa', $idMahasiswa);

        if (!is_null($levelId) && $levelId !== '') {
            $yakinBenar = $yakinBenar->where('id_level', $levelId);
        }
        if (!is_null($soalId) && $soalId !== '') {
            $yakinBenar = $yakinBenar->where('id_soal', $soalId);
        }

        $yakinBenarCount = $yakinBenar->count();

        $tidakYakinSalah = $this->model
        ->where('status_confidence', 0)
        ->where('status_jawaban', 0)
        ->where('id_mahasiswa', $idMahasiswa);

        if (!is_null($levelId) && $levelId !== '') {
            $tidakYakinSalah = $tidakYakinSalah->where('id_level', $levelId);
        }
        if (!is_null($soalId) && $soalId !== '') {
            $tidakYakinSalah = $tidakYakinSalah->where('id_soal', $soalId);
        }

        $tidakYakinSalahCount = $tidakYakinSalah->count();

        $tidakYakinBenar = $this->model
        ->where('status_confidence', 0)
        ->where('status_jawaban', 1)
        ->where('id_mahasiswa', $idMahasiswa);

        if (!is_null($levelId) && $levelId !== '') {
            $tidakYakinBenar = $tidakYakinBenar->where('id_level', $levelId);
        }
        if (!is_null($soalId) && $soalId !== '') {
            $tidakYakinBenar = $tidakYakinBenar->where('id_soal', $soalId);
        }

        $tidakYakinBenarCount = $tidakYakinBenar->count();

        return view('pages.confidence.detailSoal', [
            'title' => 'Detail Confidence Soal',
            'yakinSalahCount' => $yakinSalahCount,
            'yakinBenarCount' => $yakinBenarCount,
            'tidakYakinSalahCount' => $tidakYakinSalahCount,
            'tidakYakinBenarCount' => $tidakYakinBenarCount,
            'mahasiswa' => $dataMahasiswa,
            'level' => $this->levelModel->find($levelId),
            'soal' => $this->soalModel->find($soalId)
        ]);
    }

    public function tableConfidence(Request $request)
    {
        $data = $this->confidenceService->tableConfidence($request);
        return $data;
    }
}
