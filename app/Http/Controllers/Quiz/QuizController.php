<?php


namespace App\Http\Controllers\Quiz;

use App\Models\Soal;
use App\Models\Level;
use App\Models\Nyawa;
use App\Models\Ujian;
use App\Models\Konversi;
use App\Models\LabelSkor;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use App\Models\UjianKonversi;
use App\Services\SoalService;
use App\Services\LevelService;
use App\Services\KonversiService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class QuizController extends Controller
{
    protected $levelService;
    protected $soalService;
    protected $konversiService;
    protected $soalModel;
    protected $konversiModel;
    protected $mahasiswaModel;
    protected $ujianModel;
    protected $labelSkorModel;
    protected $ujianKonversiModel;
    protected $levelModel;

    public function __construct()
    {
        $this->levelService = new LevelService();
        $this->soalService = new SoalService();
        $this->konversiService = new KonversiService();
        $this->soalModel = new Soal();
        $this->konversiModel = new Konversi();
        $this->mahasiswaModel = new Mahasiswa();
        $this->ujianModel = new Ujian();
        $this->labelSkorModel = new LabelSkor();
        $this->ujianKonversiModel = new UjianKonversi();
        $this->levelModel = new Level();
    }

    public function index()
    {
        $dataLevelResponse = $this->levelService->getData();

        if ($dataLevelResponse instanceof \Illuminate\Http\JsonResponse) {
            $dataLevel = $dataLevelResponse->getData(true);
        } else {
            $dataLevel = $dataLevelResponse;
        }

        $userId = Auth::id();
        $mahasiswa = $this->mahasiswaModel->where('id_user', $userId)->first();

        $levelCompletion = [];
        foreach ($dataLevel as $i => $level) {
            $levelId = $level['id'];

            // Total aktif (status = 1)
            $totalSoal     = $this->soalModel->where('id_level', $levelId)->where('status', 1)->count();
            $totalKonversi = $this->konversiModel->setView('v_konversi')->where('id_level', $levelId)->where('status', 1)->count();

            // Selesai (distinct)
            $completedSoal = $this->ujianModel
                ->where('id_mahasiswa', $mahasiswa->id)
                ->where('id_level', $levelId)
                ->where('status', 1)
                ->distinct('id_soal')
                ->count('id_soal');

            $completedKonversi = $this->ujianKonversiModel
                ->where('id_mahasiswa', $mahasiswa->id)
                ->where('id_level', $levelId)
                ->distinct('id_soal_konversi')
                ->count('id_soal_konversi');

            // Algopoin per level
            $algopoinPerLevel = $this->labelSkorModel
                ->where('id_mahasiswa', $mahasiswa->id)
                ->whereNull('id_soal')
                ->whereNull('label')
                ->where('id_level', $levelId)
                ->sum('skor');

            // Hanya soal & konversi yang masih "aktif" (belum dikerjakan)
            $activeSoal = $this->soalModel
                ->where('id_level', $levelId)
                ->where('status', 1)
                ->whereNotIn('id', function ($q) use ($mahasiswa, $levelId) {
                    $q->select('id_soal')
                      ->from((new Ujian)->getTable())
                      ->where('id_mahasiswa', $mahasiswa->id)
                      ->where('id_level', $levelId)
                      ->where('status', 1);
                })
                ->orderBy('order', 'asc')
                ->first();

            $activeKonversi = $this->konversiModel
                ->setView('v_konversi')
                ->where('id_level', $levelId)
                ->where('status', 1)
                ->whereNotIn('id', function ($q) use ($mahasiswa, $levelId) {
                    $q->select('id_soal_konversi')
                      ->from((new UjianKonversi)->getTable())
                      ->where('id_mahasiswa', $mahasiswa->id)
                      ->where('id_level', $levelId);
                })
                ->orderBy('order', 'asc')
                ->first();

            $remainingSoal = max(0, $totalSoal - $completedSoal);
            $remainingKonversi = max(0, $totalKonversi - $completedKonversi);

            $allSoalDone     = ($totalSoal == 0) || ($completedSoal >= $totalSoal);
            $allKonversiDone = ($totalKonversi == 0) || ($completedKonversi >= $totalKonversi);
            $hasAlgopoin     = $algopoinPerLevel > 0;

            $isLevelCompleted = $allSoalDone && $allKonversiDone && $hasAlgopoin;
            $levelCompletion[$i] = $isLevelCompleted;

            // Original data
            $dataLevel[$i]['jumlahSoalPseudocode'] = $totalSoal;
            $dataLevel[$i]['jumlahSoalKonversi'] = $totalKonversi;
            $dataLevel[$i]['jumlahSoalPseudocodeSelesai'] = $completedSoal;
            $dataLevel[$i]['jumlahSoalKonversiSelesai'] = $completedKonversi;
            $dataLevel[$i]['algopoin'] = $algopoinPerLevel;
            $dataLevel[$i]['isLevelCompleted'] = $isLevelCompleted;

            // Hanya yang aktif (belum dikerjakan)
            $dataLevel[$i]['jumlahSoalPseudocodeAktif'] = $remainingSoal;
            $dataLevel[$i]['jumlahSoalKonversiAktif'] = $remainingKonversi;
            $dataLevel[$i]['activeSoal'] = $activeSoal ? [
                'id' => $activeSoal->id,
                'judul' => $activeSoal->judul,
                'order' => $activeSoal->order
            ] : null;
            $dataLevel[$i]['activeKonversi'] = $activeKonversi ? [
                'id' => $activeKonversi->id,
                'judul' => $activeKonversi->judul_soal ?? $activeKonversi->judul ?? null,
                'order' => $activeKonversi->order
            ] : null;
        }

        // Locking logic (tidak diubah, hanya tetapkan)
        foreach ($dataLevel as $i => $level) {
            $manualActive = intval($level['manual_active']) === 1;

            if ($manualActive) {
                $dataLevel[$i]['isLocked'] = false;
                continue;
            }

            if ($i === 0) {
                $dataLevel[$i]['isLocked'] = false;
                continue;
            }

            $prevLevel = $dataLevel[$i - 1];
            $prevIsManual = intval($prevLevel['manual_active']) === 1;
            $canUnlock = !$prevIsManual && !empty($levelCompletion[$i - 1]);

            $dataLevel[$i]['isLocked'] = !$canUnlock;
        }

        $algopoin = $this->labelSkorModel
            ->where('id_mahasiswa', $mahasiswa->id)
            ->whereNull('id_soal')
            ->whereNull('label')
            ->sum('skor');

        // Hitung badge hanya untuk soal yang masih aktif (status = 1) dan memiliki id_soal (bukan record avg level)
        $algobadge = $this->labelSkorModel
            ->where('id_mahasiswa', $mahasiswa->id)
            ->whereNotNull('id_soal')
            ->whereIn('id_soal', function ($q) {
            $q->select('id')
              ->from((new Soal)->getTable())
              ->where('status', 1);
            })
            ->count();

        $nyawa = Nyawa::where('id_user', $userId)->first();

        // Check and regenerate lives (1 life per 10 minutes)
        $nyawa->checkAndRegenerate();

        return view('pages.quiz.index', [
            'title' => 'Quiz',
            'dataLevel' => $dataLevel,
            'algopoin' => $algopoin,
            'algobadge' => $algobadge,
            'lives' => $nyawa->nyawa,
            'max_lives' => $nyawa->max_nyawa,
            'next_regen_at' => $nyawa->next_regen_at
        ]);
    }

    public function questionList(Request $request)
{
    $levelId = $request->query('level');

    $idUser = Auth::id();
    $idMahasiswa = $this->mahasiswaModel->where('id_user', $idUser)->value('id');

    $soalList = $this->soalModel
        ->where('id_level', $levelId)
        ->where('status', 1)
        ->orderBy('order', 'asc')
        ->get();

    $result = [];
    $visibleLimit = 5;
    $firstActiveSet = false;
    $pairCount = 0;

    foreach ($soalList as $soal) {
        if ($pairCount >= $visibleLimit) break;

        // ambil konversi berdasarkan relasi yang valid
        $konversi = $this->konversiModel
            ->setView('v_konversi')
            ->where('id_soal', $soal->id)
            ->first();

        // cek status soal
        $isDone = $this->ujianModel
            ->where('id_mahasiswa', $idMahasiswa)
            ->where('id_soal', $soal->id)
            ->where('status', 1)
            ->exists();

        // tentukan status
        if ($isDone) {
            $status = 'done';
        } else {
            if (!$firstActiveSet) {
                $status = 'active';
                $firstActiveSet = true;
            } else {
                $status = 'locked';
            }
        }

        // ambil badge
        $badge = $this->labelSkorModel
            ->where('id_mahasiswa', $idMahasiswa)
            ->where('id_level', $levelId)
            ->where('id_soal', $soal->id)
            ->value('label');

        // =========================
        // PSEUDOCODE (SOAL)
        // =========================
        $result[] = [
            'type' => 'soal',
            'id' => $soal->id,
            'judul' => ($status === 'locked') ? null : $soal->judul,
            'difficulty' => $soal->difficulty,
            'status' => $status,
            'badge' => $badge
        ];

        // =========================
        // KONVERSI (PASTI ADA SLOT)
        // =========================
        if ($konversi) {
            $result[] = [
                'type' => 'konversi',
                'id' => $konversi->id,
                'judul' => ($status === 'locked')
                    ? null
                    : ($konversi->judul_soal ?? $konversi->judul ?? null),
                'difficulty' => $soal->difficulty,
                'status' => $status
            ];
        } else {
            // fallback biar zigzag tidak rusak
            $result[] = [
                'type' => 'konversi',
                'id' => null,
                'judul' => null,
                'difficulty' => $soal->difficulty,
                'status' => 'locked'
            ];
        }

        $pairCount++;
    }

    $algopoin = $this->labelSkorModel
        ->where('id_mahasiswa', $idMahasiswa)
        ->whereNull('id_soal')
        ->whereNull('label')
        ->where('id_level', $levelId)
        ->sum('skor');

    $dataLevel = $this->levelModel->find($levelId);

    $jumlahSoalKonversi = $this->konversiModel
        ->where('id_level', $levelId)
        ->where('status', 1)
        ->count();

    $nyawa = Nyawa::where('id_user', $idUser)->first();
    $nyawa->checkAndRegenerate();

    return view('pages.quiz.question-list', [
        'title' => 'List Soal',
        'dataSoal' => $result,
        'algopoin' => $algopoin,
        'levelId' => $levelId,
        'nilaiKonversiList' => [],
        'dataLevel' => $dataLevel,
        'jumlahSoalKonversi' => $jumlahSoalKonversi,
        'lives' => $nyawa->nyawa,
        'max_lives' => $nyawa->max_nyawa,
        'next_regen_at' => $nyawa->next_regen_at
    ]);
}

    private function getNextDifficulty($label)
    {
        if (in_array($label, ['Ideal', 'Normal'])) {
            return 'medium';
        } elseif ($label == 'Struggling') {
            return 'easy';
        } elseif ($label == 'Gaming the System') {
            return 'easy';
        }
        return 'easy';
    }

    public function calculateAvgSkor(Request $request)
    {
        $levelId = $request->input('level_id');
        $idUser = Auth::id();
        $idMahasiswa = $this->mahasiswaModel->where('id_user', $idUser)->value('id');

        // Ambil semua soal pada level ini
        $soalIds = $this->soalModel->where('id_level', $levelId)->where('status', 1)->pluck('id')->toArray();

        // Ambil labelSkor untuk soal-soal tersebut
        $labelSkorSoal = $this->labelSkorModel
            ->where('id_mahasiswa', $idMahasiswa)
            ->where('id_level', $levelId)
            ->whereIn('id_soal', $soalIds)
            ->pluck('skor', 'id_soal')
            ->toArray();

        // Cek apakah semua soal sudah dikerjakan (ada skor untuk semua soal)
        if (count($soalIds) === 0 || count($labelSkorSoal) < count($soalIds)) {
            return response()->json(['message' => 'Belum memenuhi kriteria perhitungan rata-rata skor.']);
        }

        // Hitung rata-rata skor
        $totalSkor = array_sum($labelSkorSoal);
        $jumlahSoal = count($soalIds);
        $averageSkor = $jumlahSoal > 0 ? $totalSkor / $jumlahSoal : 0;

        // Cek apakah sudah ada data labelSkor untuk level ini (id_soal = null, label = null)
        $existing = $this->labelSkorModel
            ->where('id_mahasiswa', $idMahasiswa)
            ->where('id_level', $levelId)
            ->whereNull('id_soal')
            ->whereNull('label')
            ->first();

        if ($existing) {
            if ($existing->skor == $averageSkor) {
                return response()->json(['message' => 'Tidak ada update, skor sama.', 'avgSkor' => $averageSkor]);
            } else {
                $existing->skor = $averageSkor;
                $existing->updated_at = now();
                $existing->save();
                return response()->json(['message' => 'Skor diperbarui.', 'avgSkor' => $averageSkor]);
            }
        } else {
            $insertData = [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'id_level' => $levelId,
                'id_soal' => null,
                'id_mahasiswa' => $idMahasiswa,
                'label' => null,
                'skor' => $averageSkor,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $this->labelSkorModel->insert($insertData);
            return response()->json(['message' => 'Skor berhasil disimpan.', 'avgSkor' => $averageSkor]);
        }
    }
}
