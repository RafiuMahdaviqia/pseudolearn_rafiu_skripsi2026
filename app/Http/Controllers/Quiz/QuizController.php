<?php


namespace App\Http\Controllers\Quiz;

use App\Models\Soal;
use App\Models\Level;
use App\Models\Nyawa;
use App\Models\Ujian;
use App\Models\BankSoalKonversi;
use App\Models\LabelSkor;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use App\Models\UjianKode;
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
    protected $bankSoalKonversiModel;
    protected $mahasiswaModel;
    protected $ujianModel;
    protected $labelSkorModel;
    protected $ujianKodeModel;
    protected $levelModel;

    public function __construct()
    {
        $this->levelService = new LevelService();
        $this->soalService = new SoalService();
        $this->konversiService = new KonversiService();
        $this->soalModel = new Soal();
        $this->bankSoalKonversiModel = new BankSoalKonversi();
        $this->mahasiswaModel = new Mahasiswa();
        $this->ujianModel = new Ujian();
        $this->labelSkorModel = new LabelSkor();
        $this->ujianKodeModel = new UjianKode();
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
            $totalKonversi = $this->bankSoalKonversiModel->setView('v_bank_soal_konversi')->where('id_level', $levelId)->where('status', 1)->count();

            // Selesai
            $completedSoal = $this->ujianModel
                ->where('id_mahasiswa', $mahasiswa->id)
                ->where('id_level', $levelId)
                ->where('status', 1)
                ->distinct('id_soal')
                ->count('id_soal');

            $completedKonversi = $this->ujianKodeModel
                ->where('id_mahasiswa', $mahasiswa->id)
                ->where('id_level', $levelId)
                ->distinct('id_bank_soal_konversi')
                ->count('id_bank_soal_konversi');

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
                ->orderByRaw("FIELD(difficulty, 'easy', 'medium', 'hard') ASC")
                ->first();

            $activeKonversi = $this->bankSoalKonversiModel
                ->setView('v_bank_soal_konversi')
                ->where('id_level', $levelId)
                ->where('status', 1)
                ->whereNotIn('id', function ($q) use ($mahasiswa, $levelId) {
                    $q->select('id_bank_soal_konversi')
                      ->from((new UjianKode)->getTable())
                      ->where('id_mahasiswa', $mahasiswa->id)
                      ->where('id_level', $levelId);
                })
                ->orderByRaw("FIELD(difficulty, 'easy', 'medium', 'hard') ASC")
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
        $dataSoal = $this->soalModel->where('id_level', $levelId)->active()->orderByRaw("FIELD(difficulty, 'easy', 'medium', 'hard') ASC")->get()->toArray();
        $dataKonversi = $this->bankSoalKonversiModel->setView('v_bank_soal_konversi')->where('id_level', $levelId)->where('status', 1)->orderByRaw("FIELD(difficulty, 'easy', 'medium', 'hard') ASC")->get()->toArray();

        $idUser = Auth::id();
        $idMahasiswa = $this->mahasiswaModel->where('id_user', $idUser)->value('id');
        $dataUjian = $this->ujianModel->where('id_mahasiswa', $idMahasiswa)
            ->where('id_level', $levelId)
            ->where('status', 1)
            ->get()
            ->toArray();

        $algopoin = $this->labelSkorModel
                            ->where('id_mahasiswa', $idMahasiswa)
                            ->whereNull('id_soal')
                            ->whereNull('label')
                            ->where('id_level', $levelId)
                            ->sum('skor');

        // Index konversi by id_soal (single, not array)
        $konversiBySoal = [];
        foreach ($dataKonversi as $konversi) {
            $konversiBySoal[$konversi['id_soal']] = $konversi;
            $dataUjianKonversi = $this->ujianKodeModel->where('id_mahasiswa', $idMahasiswa)
                ->where('id_level', $levelId)
                ->where('id_bank_soal_konversi', $konversi['id'])
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
            $nilaiKonversiList = $this->ujianKodeModel
                ->setView('v_ujian_kode')
                ->where('id_mahasiswa', $idMahasiswa)
                ->where('id_level', $levelId)
                ->whereIn('id_bank_soal_konversi', $konversiIds)
                ->whereNotNull('nilai')
                ->orderBy('created_at', 'asc')
                ->pluck('nilai', 'judul_soal') // atau 'judul_soal' jika tetap ingin pakai judul
                ->toArray();
        }

        $dataLevel = $this->levelModel->find($levelId);
        $jumlahSoalKonversi = $this->bankSoalKonversiModel->where('id_level', $levelId)->where('status', 1)->count();

        $idUser = Auth::id();
        $nyawa = Nyawa::where('id_user', $idUser)->first();

        // Check and regenerate lives (1 life per 10 minutes)
        $nyawa->checkAndRegenerate();

        return view('pages.quiz.question-list', [
            'title' => 'List Soal',
            'dataSoal' => $result,
            'algopoin' => $algopoin,
            'levelId' => $levelId,
            'nilaiKonversiList' => $nilaiKonversiList,
            'dataLevel' => $dataLevel,
            'jumlahSoalKonversi' => $jumlahSoalKonversi,
            'lives' => $nyawa->nyawa,
            'max_lives' => $nyawa->max_nyawa,
            'next_regen_at' => $nyawa->next_regen_at
        ]);
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
