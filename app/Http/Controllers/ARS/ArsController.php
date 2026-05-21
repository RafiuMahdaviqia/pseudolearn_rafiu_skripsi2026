<?php

namespace App\Http\Controllers\ARS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ArsReportService;
use App\Models\Level;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Exports\ArsExport;
use Maatwebsite\Excel\Facades\Excel;

class ArsController extends Controller
{
    protected $arsReportService;
    protected $levelModel;
    protected $kelasModel;
    protected $mahasiswaModel;

    public function __construct()
    {
        $this->arsReportService = new ArsReportService();
        $this->levelModel = new Level();
        $this->kelasModel = new Kelas();
        $this->mahasiswaModel = new Mahasiswa();
    }

    public function index()
    {
        $list_kelas = $this->kelasModel->get(['id', 'name', 'angkatan'])->toArray();
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

        $list_level = collect($list_level)->map(function ($name, $id) {
            return [
                'id' => $id,
                'name' => $name
            ];
        })->values()->toArray();

        return view('pages.ARS.index', [
            'title' => 'ARS Report',
            'list_kelas' => $list_kelas,
            'list_level' => $list_level
        ]);
    }

    public function table(Request $request)
    {
        try {
            $data = $this->arsReportService->table($request);

            return response()->json($this->arsReportService->table($request));

        } catch (\Throwable $e) {
            return response()->json([
                "error" => $e->getMessage(),
                "line" => $e->getLine()
            ], 500);
        }
    }

    public function detail($id)
    {
        $mahasiswa = $this->mahasiswaModel
            ->setView('v_mahasiswa')
            ->where('id', $id)
            ->first();

        $list_level = $this->levelModel
            ->orderBy('order', 'asc')
            ->get(['id', 'name']);

        //ARS Summary
        $ars = $this->arsReportService->getDetailArs($id, null);

        return view('pages.ARS.detailArs', [
            'title' => 'Detail ARS',
            'mahasiswa' => $mahasiswa,
            'list_level' => $list_level,

            'totalArs' => $ars['summary']['totalArs'],
            'jumlahSoalTambahan' => $ars['summary']['jumlahSoalTambahan'],
            'totalWaktu' => $ars['summary']['totalWaktu'],
        ]);
    }

    public function tableArsLog(Request $request)
    {
        return response()->json(
            $this->arsReportService->tableArsLog($request)
        );
    }

    public function runArs($idMahasiswa, Request $request)
    {
        $idLevel = $request->input('id_level');
        $ars = $this->arsReportService->processArs($idMahasiswa, $idLevel);

        return response()->json($ars);
    }

    public function getDetailArs(Request $request, $idMahasiswa)
    {
        $idLevel = $request->input('id_level');

        return response()->json(
            $this->arsReportService->getDetailArs($idMahasiswa, $idLevel)
        );
    }

    public function export(Request $request)
    {
        $kelasNama = null;

        if ($request->kelas) {
            $kelas = Kelas::find($request->kelas);
            if ($kelas) {
                $kelasNama = str_replace(' ', '_', $kelas->name);
            }
        }
        
        if ($kelasNama) {
            $filename = 'ARS_REPORT_' . $kelasNama . '_' . now()->timezone('Asia/Jakarta')->format('d-m-Y_H-i-s') . '.xlsx';
        } else {
            $filename = 'ARS_REPORT_' . now()->timezone('Asia/Jakarta')->format('d-m-Y_H-i-s') . '.xlsx';
        }

        return Excel::download(
            new ArsExport($request->kelas),
            $filename
        );
    }
}
