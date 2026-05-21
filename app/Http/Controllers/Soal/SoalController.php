<?php


namespace App\Http\Controllers\Soal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Level;
use App\Services\SoalService;
use App\Models\Soal;

class SoalController extends Controller
{
    protected $levelModel;
    protected $soalService;
    protected $soalModel;

    public function __construct()
    {
        $this->levelModel = new Level();
        $this->soalService = new SoalService();
        $this->soalModel = new Soal();
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

        return view('pages.soal.index', [
            'title' => 'Soal',
            'list_level' => $list_level
        ]);
    }

    public function order()
    {
        $dataLevel = $this->levelModel->orderBy('order', 'asc')->get();

        return view('pages.soal.order', [
            'title' => 'Urutan Soal',
            'dataLevel' => $dataLevel
        ]);
    }

    public function table(Request $request)
    {
        $opr = $this->soalService->table($request);
        return $opr;
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
            $data = $this->soalModel->find($id);
        }

        return view('pages.soal.form', [
            'title' => 'Form Soal',
            'data' => $data,
            'levels' => $list_level
        ]);
    }

    public function store(Request $request)
    {
        $opr = $this->soalService->store($request);
        return $opr;
    }

    public function destroy($id)
    {
        $opr = $this->soalService->destroy($id);
        return $opr;
    }

    public function getById($id)
    {
        $soal = $this->soalModel->find($id);

        if (!$soal) {
            return response()->json([], 404);
        }

        return response()->json([
            'id' => $soal->id,
            'judul' => $soal->judul ?? null,
            'soal' => $soal->soal ?? null,
            'output' => $soal->output ?? null,
            'kunci_tipe_data' => $soal->kunci_tipe_data ?? null,
            'kunci_algoritma' => $soal->kunci_algoritma ?? null,
            'jawaban' => $soal->jawaban ?? null,
        ]);
    }


    public function saveOrder(Request $request)
    {
        $opr = $this->soalService->saveOrder($request);
        return $opr;
    }

    public function updateStatusSoal(Request $request)
    {
        $opr = $this->soalService->updateStatusSoal($request);
        return $opr;
    }
}
