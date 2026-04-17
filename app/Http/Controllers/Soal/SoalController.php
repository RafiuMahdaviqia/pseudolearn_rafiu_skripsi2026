<?php


namespace App\Http\Controllers\Soal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Level;
use App\Services\SoalService;
use App\Models\Soal;
use App\Http\Controllers\ARS\ArsController;
use Illuminate\Support\Facades\Auth;

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
        $opr = $this->soalService->getById($id);
        return $opr;
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

public function submit(Request $request)
{
    $user = Auth::user();
    $id_mahasiswa = $user->id;
    $answers = $request->answers;

    $ars = app(ArsController::class);

    // 🔹 Soal awal (jika $answers = null, hanya generate 10 soal easy)
    if (is_null($answers)) {
        $initialQuestions = $ars->submitAnswers($id_mahasiswa, null);
        return view('soal_awal', compact('initialQuestions'));
    }

    // 🔹 Submit jawaban & dapat soal berikutnya
    $nextQuestions = $ars->submitAnswers($id_mahasiswa, $answers);

    // 🔹 Soal tambahan jika performa buruk
    $soalTambahan = $ars->determineAdditional($id_mahasiswa);

    return view('soal_tambahan', compact('nextQuestions', 'soalTambahan'));
}

public function testArs()
{
    $id_mahasiswa = 1;
    $ars = app(ArsController::class);

    //Ambil soal awal
    $initialQuestions = $ars->submitAnswers($id_mahasiswa, null);

    //jawaban random untuk simulasi
    $answers = $initialQuestions->map(fn($q) => [
        'id_soal' => $q['id_soal'],
        'jenis_soal' => $q['jenis_soal'],
        'difficulty_sekarang' => $q['difficulty_sekarang'],
        'cluster' => collect(['struggling','gaming','normal','ideal'])->random()
    ])->toArray();

    //Submit jawaban
    $nextQuestions = $ars->submitAnswers($id_mahasiswa, $answers);

    //Summary & soal tambahan
    $summary = $ars->getPerformanceSummary($id_mahasiswa);
    $soalTambahan = $ars->determineAdditional($id_mahasiswa);

    dd([
        'initialQuestions' => $initialQuestions,
        'answers' => $answers,
        'nextQuestions' => $nextQuestions,
        'summary' => $summary,
        'soalTambahan' => $soalTambahan
    ]);
}
}



        /*$dataUjian = $this->ujianModel->where('id_mahasiswa', $idMahasiswa)
            ->where('id_level', $levelId)
            ->where('status', 1)
            ->get()
            ->toArray();*/

        /* // Index konversi by id_soal (single, not array)
        $konversiBySoal = [];
        foreach ($dataKonversi as $konversi) {
            $konversiBySoal[$konversi['id_soal']] = $konversi;
            $dataUjianKonversi = $this->ujianKonversiModel->where('id_mahasiswa', $idMahasiswa)
                ->where('id_level', $levelId)
                ->where('id_soal_konversi', $konversi['id'])
                ->first();
            if ($dataUjianKonversi) {
                $konversiBySoal[$konversi['id_soal']]['ujianKonversi'] = $dataUjianKonversi->toArray();
            } else {
                $konversiBySoal[$konversi['id_soal']]['ujianKonversi'] = null;
            }
        }

        // Index ujian by id_soal for quick lookup
        $ujianBySoal = [];
        foreach ($dataUjian as $ujian) {
            $ujianBySoal[$ujian['id_soal']] = true;
        }

        // Gabungkan soal dan konversi sebagai dua entri berbeda dalam result
        $result = [];
        foreach ($dataSoal as $soal) {
            $soalId = $soal['id'];
            // Soal pseudocode
            $soalEntry = $soal;
            $soalEntry['type'] = 'soal';
            $soalEntry['konversi'] = $konversiBySoal[$soalId] ?? null;
            $soalEntry['ujianKonversi'] = null; // hanya untuk konversi
            $soalEntry['status'] = isset($ujianBySoal[$soalId]) ? 'done' : 'locked';

            // Ambil badge berdasarkan id_mahasiswa, id_level, id_soal
            $badge = $this->labelSkorModel
                ->where('id_mahasiswa', $idMahasiswa)
                ->where('id_level', $levelId)
                ->where('id_soal', $soalId)
                ->value('label');
            $soalEntry['badge'] = $badge ? $badge : null;

            $result[] = $soalEntry;

            // Soal konversi (jika ada)
            if (isset($konversiBySoal[$soalId])) {
                $konversi = $konversiBySoal[$soalId];
                $konversiEntry = $konversi;
                $konversiEntry['type'] = 'konversi';
                $konversiEntry['soal'] = $soal; // referensi soal
                $konversiEntry['badge'] = null; // badge hanya untuk soal utama
                $konversiEntry['status'] = $konversi['ujianKonversi'] ? 'done' : 'locked';
                $result[] = $konversiEntry;
            }
        }

        // Atur status final: 
        // 1. Semua 'done' tetap done
        // 2. Satu soal/konversi pertama yang tidak done => active
        // 3. Sisanya => locked
        $firstActiveSet = false;
        foreach ($result as $i => $row) {
            if ($row['status'] === 'done') {
                continue;
            }
            if (!$firstActiveSet) {
                $result[$i]['status'] = 'active';
                $firstActiveSet = true;
            } else {
                $result[$i]['status'] = 'locked';
            }
        }

        // Ambil hanya nilai konversi untuk id konversi yang ada di $dataKonversi
        $konversiIds = array_column($dataKonversi, 'id');

        if (empty($konversiIds)) {
            $nilaiKonversiList = [];
        } else {
            $nilaiKonversiList = $this->ujianKonversiModel
                ->setView('v_ujian_konversi')
                ->where('id_mahasiswa', $idMahasiswa)
                ->where('id_level', $levelId)
                ->whereIn('id_soal_konversi', $konversiIds)
                ->whereNotNull('nilai')
                ->orderBy('created_at', 'asc')
                ->pluck('nilai', 'judul_soal') // atau 'judul_soal' jika tetap ingin pakai judul
                ->toArray();
        }*/
