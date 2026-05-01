<?php

namespace App\Http\Controllers\LogUjianKode;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Level;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\BankSoalKonversi;
use App\Models\UjianKode;
use App\Services\UjianKodeService;

class LogUjianKodeController extends Controller
{
    protected $levelModel;
    protected $kelasModel;
    protected $mahasiswaModel;
    protected $bankSoalKonversiModel;
    protected $ujianKodeModel;
    protected $ujianKodeService;

    public function __construct()
    {
        $this->levelModel            = new Level();
        $this->kelasModel            = new Kelas();
        $this->mahasiswaModel        = new Mahasiswa();
        $this->bankSoalKonversiModel = new BankSoalKonversi();
        $this->ujianKodeModel        = new UjianKode();
        $this->ujianKodeService      = new UjianKodeService();
    }

    public function index()
    {
        $list_kelas = $this->kelasModel->get(['id', 'name', 'angkatan'])->toArray();

        $list_kelas = collect($list_kelas)
            ->prepend(['id' => '', 'name' => 'Semua Kelas', 'angkatan' => ''])
            ->map(fn($item) => [
                'id'       => $item['id'],
                'name'     => $item['name'],
                'angkatan' => $item['angkatan'],
            ])
            ->values()
            ->toArray();

        $list_level = $this->levelModel->orderBy('order', 'asc')->get(['id', 'name'])
            ->map(fn($item) => ['id' => $item->id, 'name' => $item->name])
            ->values()
            ->toArray();

        return view('pages.logUjianKode.index', [
            'title'      => 'Log Ujian Kode',
            'list_kelas' => $list_kelas,
            'list_level' => $list_level,
        ]);
    }

    public function table(Request $request)
    {
        return $this->ujianKodeService->tableUjianKode($request);
    }

    public function detail($id)
    {
        $mahasiswa = $this->mahasiswaModel
            ->setView('v_mahasiswa')
            ->where('id_user', $id)
            ->first();
        $levelId   = request()->query('level');
        $soalId    = request()->query('soal');

        $level = $levelId ? $this->levelModel->find($levelId) : null;
        $soal  = $soalId  ? $this->bankSoalKonversiModel->find($soalId) : null;

        $list_level = $this->levelModel->orderBy('order', 'asc')->get(['id', 'name'])
            ->map(fn($item) => ['id' => $item->id, 'name' => $item->name])
            ->values()
            ->toArray();

        return view('pages.logUjianKode.detail', [
            'title' => 'Detail Log Ujian Kode',
            'mahasiswa' => $mahasiswa,
            'level' => $level,
            'soal' => $soal,
            'id_user' => $id,
            'list_level' => $list_level,
        ]);
    }

    public function tableDetail(Request $request)
    {
        return $this->ujianKodeService->tableDetail($request);
    }

    public function detailKode($id)
    {
        $idMahasiswa = request()->query('id_mahasiswa');
        $idLevel     = request()->query('id_level');
        $idSoal      = request()->query('id_soal');

        $dataMahasiswa = $this->mahasiswaModel->setView('v_mahasiswa')->find($idMahasiswa);

        if (!$dataMahasiswa) {
            return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        $ujianKode = $this->ujianKodeModel->setView('v_ujian_kode')->where('id', $id)->first();

        if (!$ujianKode) {
            return redirect()->back()->with('error', 'Data ujian kode tidak ditemukan.');
        }

        return view('pages.logUjianKode.detailKode', [
            'title'         => 'Detail Kode',
            'ujianKode'     => $ujianKode,
            'dataMahasiswa' => $dataMahasiswa,
            'id_mahasiswa'  => $idMahasiswa,
            'id_level'      => $idLevel,
            'id_soal'       => $idSoal,
        ]);
    }
}
