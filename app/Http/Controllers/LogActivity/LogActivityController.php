<?php


namespace App\Http\Controllers\LogActivity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\LogActivityService;
use App\Models\Level;
use App\Models\Soal;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\Ujian;
use App\Models\LogData;

class LogActivityController extends Controller
{

    protected $logActivityService;
    protected $levelModel;
    protected $soalModel;
    protected $kelasModel;
    protected $mahasiswaModel;
    protected $ujianModel;
    protected $logDataModel;


    public function __construct()
    {
        $this->logActivityService = new LogActivityService();
        $this->levelModel = new Level();
        $this->soalModel = new Soal();
        $this->kelasModel = new Kelas();
        $this->mahasiswaModel = new Mahasiswa();
        $this->ujianModel = new Ujian();
        $this->logDataModel = new LogData();
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

        return view('pages.logActivity.index', [
            'title' => 'Log Aktivitas',
            'list_kelas' => $list_kelas,
            'list_level' => $list_level
        ]);
    }

    public function table(Request $request)
    {
        $data = $this->logActivityService->table($request);
        return $data;
    }

    public function detailSoal($id)
    {
        $levelId = request()->query('level');
        $soalId = request()->query('soal');
        $idMahasiswa = $id;

        $dataMahasiswa = $this->mahasiswaModel->setView('v_mahasiswa')->find($idMahasiswa);

        $ujianQuery = $this->ujianModel->setView('v_ujian')
                ->where('id_mahasiswa', $idMahasiswa);
        if (!is_null($levelId) && $levelId !== '') {
            $ujianQuery = $ujianQuery->where('id_level', $levelId);
        }
        if (!is_null($soalId) && $soalId !== '') {
            $ujianQuery = $ujianQuery->where('id_soal', $soalId);
        }
        $totalWaktuDetik = $ujianQuery->sum('waktu');

        $logDataQuery = $this->logDataModel->setView('v_log_data')
            ->where('id_mahasiswa', $idMahasiswa);
        if (!is_null($levelId) && $levelId !== '') {
            $logDataQuery = $logDataQuery->where('id_level', $levelId);
        }
        if (!is_null($soalId) && $soalId !== '') {
            $logDataQuery = $logDataQuery->where('id_soal', $soalId);
        }
        $totalDrag = $logDataQuery->count();

        $totalSubmit = $ujianQuery->count();

        // Convert seconds to HH:MM:SS
        $hours = floor($totalWaktuDetik / 3600);
        $minutes = floor(($totalWaktuDetik % 3600) / 60);
        $seconds = $totalWaktuDetik % 60;
        $totalWaktu = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

        return view('pages.logActivity.detailSoal', [
            'title' => 'Detail Log Aktivitas',
            'mahasiswa' => $dataMahasiswa,
            'totalWaktu' => $totalWaktu,
            'totalDrag' => $totalDrag,
            'totalSubmit' => $totalSubmit,
            'level' => $this->levelModel->find($levelId),
            'soal' => $this->soalModel->find($soalId)
        ]);
    }

    public function tableDetailLog(Request $request)
    {
        $data = $this->logActivityService->tableDetailLog($request);
        return $data;
    }

    public function getSoalByLevel(Request $request)
    {
        $levelId = $request->query('level_id');

        // Ambil semua soal sesuai level
        $soal = $this->soalModel
            ->where('id_level', $levelId)
            ->orderBy('order', 'asc')
            ->get(['id', 'judul']);

        return response()->json($soal);
    }
}
