<?php


namespace App\Http\Controllers\Scoring;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ScoringService;
use App\Models\Level;
use App\Models\Soal;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\HistoryConfidence;

class ScoringController extends Controller
{

    protected $scoringService;
    protected $levelModel;
    protected $soalModel;
    protected $kelasModel;
    protected $mahasiswaModel;
    protected $model;


    public function __construct()
    {
        $this->scoringService = new ScoringService();
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

        return view('pages.scoring.index', [
            'title' => 'Clustering Scoring',
            'list_kelas' => $list_kelas,
            'list_level' => $list_level
        ]);
    }

    public function table(Request $request)
    {
        $data = $this->scoringService->table($request);
        return $data;
    }

    public function updateTest(Request $request)
    {
        $opr = $this->scoringService->updateTest($request);
        return $opr;
    }

    public function calculateManual(Request $request)
    {
        $opr = $this->scoringService->calculateManual($request);
        return $opr;
    }

    public function calculateAverage(Request $request)
    {
        $opr = $this->scoringService->calculateAverage($request);
        return $opr;
    }
}
