<?php


namespace App\Http\Controllers\Labeling;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\LabelingService;
use App\Models\Level;
use App\Models\Soal;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\HistoryConfidence;

class LabelingController extends Controller
{

    protected $labelingService;
    protected $levelModel;
    protected $soalModel;
    protected $kelasModel;
    protected $mahasiswaModel;
    protected $model;


    public function __construct()
    {
        $this->labelingService = new LabelingService();
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

        return view('pages.labeling.index', [
            'title' => 'Clustering Labeling',
            'list_kelas' => $list_kelas,
            'list_level' => $list_level
        ]);
    }

    public function table(Request $request)
    {
        $data = $this->labelingService->table($request);
        return $data;
    }

    public function updateTest(Request $request)
    {
        $opr = $this->labelingService->updateTest($request);
        return $opr;
    }

    public function calculateManual(Request $request)
    {
        $opr = $this->labelingService->calculateManual($request);
        return $opr;
    }
}
