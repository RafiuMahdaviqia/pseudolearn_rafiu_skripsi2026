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

    public function __construct(
        BankSoalKonversiService $bankSoalService,
        LevelModel $levelModel,
        BankSoalKonversi $bankSoalModel
    ) {
        $this->bankSoalService = $bankSoalService;
        $this->levelModel      = $levelModel;
        $this->bankSoalModel   = $bankSoalModel;
    }

    public function index()
    {
        $list_level = $this->levelModel->get(['id', 'name'])
            ->pluck('name', 'id')
            ->toArray();

        $list_level = collect($list_level)
            ->prepend('Semua Level', '')
            ->map(fn($name, $id) => ['id' => $id, 'name' => $name])
            ->values()
            ->toArray();

        return view('pages.bankSoalKonversi.index', [
            'title'      => 'Bank Soal Konversi',
            'list_level' => $list_level,
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

        $list_level = collect($list_level)
            ->map(fn($name, $id) => ['id' => $id, 'name' => $name])
            ->values()
            ->toArray();

        // Kirim sebagai array agar json_encode di blade aman
        $data = $id ? $this->bankSoalModel->find($id)?->toArray() : null;

        return view('pages.bankSoalKonversi.form', [
            'title'  => 'Form Bank Soal Konversi',
            'data'   => $data,
            'levels' => $list_level,
        ]);
    }

    public function getSoalByLevel(Request $request)
    {
        $request->validate([
            'level_id' => 'required|string',
        ]);

        return $this->bankSoalService->getSoalByLevel($request->level_id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'level_id' => 'required|string',
            'soal_id' => 'required|string',
            'jawaban' => 'required|string',
            'output' => 'required|string',
        ]);

        $data = $this->bankSoalService->store($request);

        if ($request->expectsJson()) {
            return response()->json($data);
        }

        return redirect()
            ->route('bank-soal-konversi.index')
            ->with('success', 'Bank soal konversi berhasil disimpan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'level_id' => 'required|string',
            'soal_id' => 'required|string',
            'jawaban' => 'required|string',
            'output' => 'required|string',
        ]);

        $updated = $this->bankSoalService->update($request, $id);

        if ($request->expectsJson()) {
            return response()->json(['success' => (bool) $updated]);
        }

        return redirect()
            ->route('bank-soal-konversi.index')
            ->with('success', 'Bank soal konversi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        return $this->bankSoalService->destroy($id);
    }

    public function runKonversi(Request $request)
    {
        return $this->bankSoalService->runKonversi($request);
    }

    public function order()
    {
        $dataLevel = $this->levelModel->orderBy('order', 'asc')->get();

        return view('pages.bankSoalKonversi.order', [
            'title' => 'Urutan Bank Soal Konversi',
            'dataLevel' => $dataLevel,
        ]);
    }

    public function getByLevelForOrder(Request $request)
    {
        $request->validate([
            'level_id' => 'required|string',
        ]);

        return response()->json(
            $this->bankSoalService->getOrderListByLevel($request->query('level_id'))
        );
    }

    // public function saveOrder(Request $request)
    // {
    //     return $this->bankSoalService->saveOrder($request);
    // }
}
