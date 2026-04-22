<?php

namespace App\Http\Controllers\ARS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ArsReportService;
use App\Models\Soal;
use App\Models\Konversi;
use App\Models\ArsResult;
use App\Models\Level;
use App\Models\Kelas;
use App\Models\Mahasiswa;

class ArsController extends Controller
{
    protected $arsReportService;
    protected $levelModel;
    protected $soalModel;
    protected $kelasModel;
    protected $mahasiswaModel;

    public function __construct()
    {
        $this->arsReportService = new ArsReportService();
        $this->levelModel = new Level();
        $this->soalModel = new Soal();
        $this->kelasModel = new Kelas();
        $this->mahasiswaModel = new Mahasiswa();
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
        $data = $this->arsReportService->table($request);
        return $data;
    }

    public function detail($id)
    {
        // ambil data mahasiswa
        $mahasiswa = $this->mahasiswaModel
            ->setView('v_mahasiswa')
            ->where('id', $id)
            ->first();

        // list level untuk dropdown
        $list_level = $this->levelModel
            ->orderBy('order', 'asc')
            ->get(['id', 'name'])
            ->toArray();

        // sementara default (nanti bisa dihitung dari query ARS)
        $totalArs = 0;
        $jumlahSoalTambahan = 0;
        $totalWaktu = "00:00:00";

        return view('pages.ARS.detailArs', [
            'title' => 'Detail ARS',
            'mahasiswa' => $mahasiswa,
            'list_level' => $list_level,
            'totalArs' => $totalArs,
            'jumlahSoalTambahan' => $jumlahSoalTambahan,
            'totalWaktu' => $totalWaktu
        ]);
    }

    public function tableArsLog(Request $request)
    {
        $data = $this->arsReportService->tableArsLog($request);
        return $data;
    }
}