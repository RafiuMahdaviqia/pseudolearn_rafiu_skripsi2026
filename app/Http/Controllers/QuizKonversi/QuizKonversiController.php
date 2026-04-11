<?php


namespace App\Http\Controllers\QuizKonversi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Level;
use App\Models\Soal;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\HistoryConfidence;
use App\Services\BankSoalBankSoalBankSoalKonversiService;
use App\Models\Konversi;
use App\Models\UjianKonversi;
use App\Models\DebugKonversi;

class QuizKonversiController extends Controller
{

    // protected $levelModel;
    // protected $soalModel;
    // protected $kelasModel;
    // protected $mahasiswaModel;
    // protected $model;
    // protected $BankSoalKonversiService;
    // protected $konversiModel;
    // protected $ujianKonversiModel;
    // protected $debugKonversiModel;


    // public function __construct()
    // {
    //     $this->levelModel = new Level();
    //     $this->soalModel = new Soal();
    //     $this->kelasModel = new Kelas();
    //     $this->mahasiswaModel = new Mahasiswa();
    //     $this->model = new HistoryConfidence();
    //     $this->BankSoalKonversiService = new BankSoalKonversiService();
    //     $this->konversiModel = new Konversi();
    //     $this->ujianKonversiModel = new UjianKonversi();
    //     $this->debugKonversiModel = new DebugKonversi();
    // }

    // public function index()
    // {
    //     $list_kelas = $this->kelasModel->get(['id', 'name', 'angkatan'])->toArray();

    //     $list_kelas = collect($list_kelas)->prepend(['id' => '', 'name' => 'Semua Kelas', 'angkatan' => '']);

    //     $list_kelas = collect($list_kelas)->map(function ($item) {
    //         return [
    //             'id' => $item['id'],
    //             'name' => $item['name'],
    //             'angkatan' => $item['angkatan']
    //         ];
    //     })->values()->toArray();

    //     $list_level = $this->levelModel->orderBy('order', 'asc')->get(['id', 'name'])
    //         ->pluck('name', 'id')
    //         ->toArray();

    //     // $list_level = collect($list_level)->prepend('Semua Level', '');

    //     $list_level = collect($list_level)->map(function ($name, $id) {
    //         return [
    //             'id' => $id,
    //             'name' => $name
    //         ];
    //     })->values()->toArray();

    //     return view('pages.ujianKonversi.index', [
    //         'title' => 'Ujian Konversi',
    //         'list_kelas' => $list_kelas,
    //         'list_level' => $list_level
    //     ]);
    // }

    // public function table(Request $request)
    // {
    //     $opr = $this->BankSoalKonversiService->tableUjianKonversi($request);

    //     return $opr;
    // }

    // public function detail($id)
    // {
    //     // Ambil data mahasiswa berdasarkan $id
    //     $mahasiswa = $this->mahasiswaModel->setView('v_mahasiswa')->find($id);
    //     $levelId = request()->query('level');
    //     $soalId = request()->query('soal');

    //     // Ambil data level jika $levelId diberikan
    //     $level = null;
    //     if ($levelId) {
    //         $level = $this->levelModel->find($levelId);
    //     }

    //     // Ambil data soal jika $soalId diberikan
    //     $soal = null;
    //     if ($soalId) {
    //         $soal = $this->soalModel->find($soalId);
    //     }

    //     $soalKonversi = $this->konversiModel->where('id_soal', $soalId)->first();

    //     return view('pages.ujianKonversi.detail', [
    //         'mahasiswa' => $mahasiswa,
    //         'level' => $level,
    //         'soal' => $soal,
    //         'konversi' => $soalKonversi,
    //         'title' => 'Detail Ujian Konversi'
    //     ]);
    // }

    // public function tableDetail(Request $request)
    // {
    //     $opr = $this->BankSBankSoalKonversiService->tableDetail($request);

    //     return $opr;
    // }

    // public function detailKonversi($id)
    // {
    //     // Ambil parameter dari query string
    //     $idMahasiswa = request()->query('id_mahasiswa');
    //     $idLevel     = request()->query('id_level');
    //     $idSoalParam = request()->query('id_soal');

    //     $dataMahasiswa = $this->mahasiswaModel->setView('v_mahasiswa')->find($idMahasiswa);
    //     if (!$dataMahasiswa) {
    //         return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan.');
    //     }

    //     // Ambil data konversi berdasarkan $id
    //     $konversi = $this->ujianKonversiModel->setView('v_ujian_konversi')->where('id', $id)->first();

    //     if (!$konversi) {
    //         return redirect()->back()->with('error', 'Data konversi tidak ditemukan.');
    //     }

    //     // Tentukan id soal (utamakan dari query, fallback ke data konversi)
    //     $soalId = $idSoalParam ?: $konversi->id_soal;

    //     // Ambil data soal terkait
    //     $soal = $soalId ? $this->soalModel->find($soalId) : null;

    //     // Ambil data debug konversi terkait
    //     $debugKonversi = $this->debugKonversiModel->where('id_ujian_konversi', $konversi->id)->first();

    //     return view('pages.ujianKonversi.detailKonversi', [
    //         'konversi'      => $konversi,
    //         'soal'          => $soal,
    //         'debugKonversi' => $debugKonversi,
    //         'id_mahasiswa'  => $idMahasiswa,
    //         'id_level'      => $idLevel,
    //         'id_soal'       => $soalId,
    //         'title'         => 'Detail Konversi',
    //         'dataMahasiswa' => $dataMahasiswa
    //     ]);
    // }
}
