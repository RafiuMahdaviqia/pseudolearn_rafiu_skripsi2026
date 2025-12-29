<?php


namespace App\Http\Controllers\Konversi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\KonversiService;
use App\Models\Level as LevelModel;
use App\Models\Soal as SoalModel;
use App\Models\Konversi;

class KonversiController extends Controller
{
    protected $konversiService;
    protected $levelModel;
    protected $soalModel;
    protected $konversiModel;

    public function __construct()
    {
        $this->konversiService = new KonversiService();
        $this->levelModel = new LevelModel();
        $this->soalModel = new SoalModel();
        $this->konversiModel = new Konversi();
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

        return view('pages.konversi.index', [
            'title' => 'Soal Konversi',
            'list_level' => $list_level
        ]);
    }

    public function table(Request $request)
    {
        $data = $this->konversiService->table($request);
        return $data;
    }

    public function form($id = null)
    {
        $list_level = $this->levelModel->get(['id', 'name'])
            ->pluck('name', 'id')
            ->toArray();

        $list_level = collect($list_level)->map(function ($name, $id) {
            return [
                'id' => $id,
                'name' => $name
            ];
        })->values()->toArray();

        $data = null;
        if ($id) {
            $data = $this->konversiModel->find($id);
        }

        return view('pages.konversi.form', [
            'title' => 'Form Konversi',
            'data' => $data,
            'levels' => $list_level
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->konversiService->store($request);
        return $data;
    }

    public function getSoalByLevel(Request $request)
    {
        $opr = $this->konversiService->getSoalByLevel($request);

        return $opr;
    }

    public function destroy($id)
    {
        $data = $this->konversiService->destroy($id);
        return $data;
    }

    public function runKonversi(Request $request)
    {
        $data = $this->konversiService->runKonversi($request);
        return $data;
    }

    public function update(Request $request)
    {
        $data = $this->konversiService->update($request);
        return $data;
    }
}