<?php


namespace App\Http\Controllers\Overlapping;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OverlappingService;
use App\Models\Level;
use App\Models\Soal;
use App\Models\Kelas;

class OverlappingController extends Controller
{

    protected $overlappingService;
    protected $levelModel;
    protected $soalModel;
    protected $kelasModel;

    public function __construct()
    {
        $this->overlappingService = new OverlappingService();
        $this->levelModel = new Level();
        $this->soalModel = new Soal();
        $this->kelasModel = new Kelas();
    }

    public function index()
    {
        $list_level = $this->levelModel->get(['id', 'name'])
            ->pluck('name', 'id')
            ->toArray();

        $list_level = collect($list_level)->prepend('Semua Level', '');

        $list_level = collect($list_level)->map(function ($name, $id) {
            return [
                'id' => $id,
                'name' => $name
            ];
        })->values()->toArray();

        return view('pages.overlapping.index', [
            'title' => 'Overlapping Analysis',
            'list_level' => $list_level
        ]);
    }

    public function tableSoal(Request $request)
    {
        $data = $this->overlappingService->tableSoal($request);
        return response()->json($data);
    }

    public function analysis($id)
    {
        $soal = $this->soalModel->find($id);

        $list_kelas = $this->kelasModel->get(['id', 'name', 'angkatan'])->toArray();

        $list_kelas = collect($list_kelas)->prepend(['id' => '', 'name' => 'Semua Kelas', 'angkatan' => '']);

        $list_kelas = collect($list_kelas)->map(function ($item) {
            return [
                'id' => $item['id'],
                'name' => $item['name'],
                'angkatan' => $item['angkatan']
            ];
        })->values()->toArray();

        return view('pages.overlapping.analysis', [
            'title' => 'Overlapping Analysis',
            'soal' => $soal,
            'list_kelas' => $list_kelas
        ]);
    }

    public function data(Request $request)
    {
        $data = $this->overlappingService->getAnalysisData($request);

        return $data;
    }

    public function detail(Request $request)
    {
        $id_soal = $request->query('id_soal');
        $index = $request->query('index');
        $type = $request->query('type');
        $value = $request->query('value');

        $list_kelas = $this->kelasModel->get(['id', 'name', 'angkatan'])->toArray();

        $list_kelas = collect($list_kelas)->prepend(['id' => '', 'name' => 'Semua Kelas', 'angkatan' => '']);

        $list_kelas = collect($list_kelas)->map(function ($item) {
            return [
                'id' => $item['id'],
                'name' => $item['name'],
                'angkatan' => $item['angkatan']
            ];
        })->values()->toArray();

        return view('pages.overlapping.detail', [
            'title' => 'Tabel Detail ' . $type,
            'id_soal' => $id_soal,
            'index' => $index,
            'type' => $type,
            'value' => $value,
            'list_kelas' => $list_kelas
        ]);
    }

    public function tableDetail(Request $request)
    {
        $data = $this->overlappingService->tableDetail($request);
        return response()->json($data);
    }

}
