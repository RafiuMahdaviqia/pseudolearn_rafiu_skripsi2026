<?php

namespace App\Http\Controllers\BankSoalKonversi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BankSoalKonversiService;
use App\Models\Level as LevelModel;
use App\Models\BankSoalKonversi;

class BankSoalKonversiController extends Controller
{
    protected $bankSoalService;
    protected $levelModel;
    protected $bankSoalModel;

    public function __construct()
    {
        $this->bankSoalService = new BankSoalKonversiService();
        $this->levelModel = new LevelModel();
        $this->bankSoalModel = new BankSoalKonversi();
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

        return view('pages.bankSoalKonversi.index', [
            'title' => 'Bank Soal Konversi',
            'list_level' => $list_level
        ]);
    }

    public function table(Request $request)
    {
        return $this->bankSoalService->table($request);
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
            $data = $this->bankSoalModel->find($id);
        }

        return view('pages.bankSoalKonversi.form', [
            'title' => 'Form Bank Soal Konversi',
            'data' => $data,
            'levels' => $list_level
        ]);
    }

    public function store(Request $request)
    {
        return $this->bankSoalService->store($request);
    }

    public function update(Request $request)
    {
        return $this->bankSoalService->update($request);
    }

    public function destroy($id)
    {
        return $this->bankSoalService->destroy($id);
    }
}
