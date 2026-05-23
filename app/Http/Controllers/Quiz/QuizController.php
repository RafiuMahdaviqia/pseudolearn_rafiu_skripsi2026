<?php

namespace App\Http\Controllers\Quiz;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\LabelSkor;
use App\Models\Mahasiswa;
use App\Models\Nyawa;
use App\Models\Soal;
use App\Models\Ujian;
use App\Models\UjianKode;
use App\Services\KonversiService;
use App\Services\LevelService;
use App\Services\SoalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuizController extends Controller
{
    protected $levelService;
    protected $soalService;
    protected $konversiService;
    protected $soalModel;
    protected $mahasiswaModel;
    protected $ujianModel;
    protected $labelSkorModel;
    protected $ujianKodeModel;
    protected $levelModel;

    protected $visibleLimit = 5;

    public function __construct()
    {
        $this->levelService = new LevelService();
        $this->soalService = new SoalService();
        $this->konversiService = new KonversiService();
        $this->soalModel = new Soal();
        $this->mahasiswaModel = new Mahasiswa();
        $this->ujianModel = new Ujian();
        $this->labelSkorModel = new LabelSkor();
        $this->ujianKodeModel = new UjianKode();
        $this->levelModel = new Level();
    }

    public function index()
    {
        $dataLevelResponse = $this->levelService->getData();
        $dataLevel = $dataLevelResponse instanceof JsonResponse ? $dataLevelResponse->getData(true) : $dataLevelResponse;

        $userId = Auth::id();
        $mahasiswa = $this->mahasiswaModel->where('id_user', $userId)->first();
        $levelCompletion = [];

        foreach ($dataLevel as $i => $level) {
            $levelId = $level['id'];

            $totalSoal = min(
                $this->soalModel->where('id_level', $levelId)->where('status', 1)->count(),
                $this->visibleLimit
            );
            $totalKonversi = min(
                DB::table('bank_soal_konversi')->where('id_level', $levelId)->count(),
                $this->visibleLimit
            );

            $completedSoal = $this->ujianModel
                ->where('id_mahasiswa', $mahasiswa->id)
                ->where('id_level', $levelId)
                ->where('status', 1)
                ->distinct('id_soal')
                ->count('id_soal');

            $completedKonversi = DB::table('ujian_kode')
                ->where('id_mahasiswa', $mahasiswa->id)
                ->where('id_level', $levelId)
                ->distinct('id_bank_soal_konversi')
                ->count('id_bank_soal_konversi');

            $algopoinPerLevel = $this->labelSkorModel
                ->where('id_mahasiswa', $mahasiswa->id)
                ->whereNull('id_soal')
                ->whereNull('label')
                ->where('id_level', $levelId)
                ->sum('skor');

            $allSoalDone = ($totalSoal == 0) || ($completedSoal >= $totalSoal);
            $allKonversiDone = ($totalKonversi == 0) || ($completedKonversi >= $totalKonversi);
            
            $isLevelCompleted = $allSoalDone && $allKonversiDone && ($algopoinPerLevel > 0);
            $levelCompletion[$i] = $isLevelCompleted;

            $dataLevel[$i]['jumlahSoalPseudocode'] = $totalSoal;
            $dataLevel[$i]['jumlahSoalKonversi'] = $totalKonversi;
            $dataLevel[$i]['jumlahSoalPseudocodeSelesai'] = $completedSoal;
            $dataLevel[$i]['jumlahSoalKonversiSelesai'] = $completedKonversi;
            $dataLevel[$i]['algopoin'] = $algopoinPerLevel;
            $dataLevel[$i]['isLevelCompleted'] = $isLevelCompleted;
            $dataLevel[$i]['jumlahSoalPseudocodeAktif'] = max(0, $totalSoal - $completedSoal);
            $dataLevel[$i]['jumlahSoalKonversiAktif'] = max(0, $totalKonversi - $completedKonversi);
        }

        // 🔥 LOGIKA KUNCIAN LEVEL: BUKA PAKSA SESUAI STATUS DOSEN
        foreach ($dataLevel as $i => $level) {
            $isActive = intval($level['manual_active']) === 1;

            if ($isActive) {
                // Jika AKTIF di Admin -> Buka Gembok (Mahasiswa bebas loncat ke level ini)
                $dataLevel[$i]['isLocked'] = false;
            } else {
                // Jika TIDAK AKTIF di Admin -> Kunci Mutlak
                $dataLevel[$i]['isLocked'] = true;
            }
        }

        $algopoin = $this->labelSkorModel
            ->where('id_mahasiswa', $mahasiswa->id)
            ->whereNull('id_soal')
            ->whereNull('label')
            ->sum('skor');

        $algobadge = $this->labelSkorModel
            ->where('id_mahasiswa', $mahasiswa->id)
            ->whereNotNull('id_soal')
            ->whereIn('id_soal', function ($q) {
                $q->select('id')->from((new Soal)->getTable())->where('status', 1);
            })->count();

        $nyawa = Nyawa::where('id_user', $userId)->first();
        if ($nyawa) $nyawa->checkAndRegenerate();

        return view('pages.quiz.index', [
            'title' => 'Quiz',
            'dataLevel' => $dataLevel,
            'algopoin' => $algopoin,
            'algobadge' => $algobadge,
            'lives' => $nyawa->nyawa ?? 0,
            'max_lives' => $nyawa->max_nyawa ?? 0,
            'next_regen_at' => $nyawa->next_regen_at ?? null,
        ]);
    }


    public function questionList(Request $request)
    {
        $levelId = $request->query('level');
        $level = $this->levelModel->find($levelId);
        abort_if(!$level, 404, 'Level not found.');

        // 🔥 PROTEKSI URL MUTLAK
        if (intval($level->manual_active) === 0) {
            return redirect()->route('quiz.index')->with('error', 'Level ini sedang dinonaktifkan oleh Dosen.');
        }
        // Hapus pengecekan order/level sebelumnya di sini jika ada.

        $user = Auth::user();
        abort_if($user === null, 401);

        $mahasiswa = $user->mahasiswa()->first();
        abort_if($mahasiswa === null, 404, 'Mahasiswa not found.');

        $limitPasang = $this->visibleLimit ?? 5;

        // 1. RIWAYAT
        $riwayatUjian = $this->ujianModel
            ->where('id_mahasiswa', $mahasiswa->id)
            ->where('id_level', $levelId)
            ->whereNotNull('id_soal')
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy('id_soal');

        $historySoalIds = $riwayatUjian->map(fn($items) => $items->first()->created_at)
            ->sortBy(fn($date) => $date ? $date->getTimestamp() : 0)
            ->keys();

        if ($historySoalIds->count() > $limitPasang) {
            $historySoalIds = $historySoalIds->take($limitPasang);
        }

        // 2. DDA (Target 1 Soal Berikutnya)
        $appendSoalId = null;
        if ($historySoalIds->count() < $limitPasang) {
            $targetDifficulty = \App\Enums\SoalDifficulty::EASY; 

            $latestSoalId = $historySoalIds->last();
            if ($latestSoalId) {
                $latestSoal = $this->soalModel->find($latestSoalId);
                $currentDifficulty = \App\Enums\SoalDifficulty::tryFrom($latestSoal->difficulty) ?? \App\Enums\SoalDifficulty::EASY;

                $latestLabel = $this->labelSkorModel
                    ->where('id_mahasiswa', $mahasiswa->id)
                    ->where('id_level', $levelId)
                    ->where('id_soal', $latestSoalId)
                    ->whereNotNull('label')
                    ->orderBy('created_at', 'desc')
                    ->value('label');

                $clusterLabel = \App\Enums\ClusterLabel::tryFrom(strtolower((string) $latestLabel));

                if ($clusterLabel) {
                    $nextDiffIndex = match ($clusterLabel) {
                        \App\Enums\ClusterLabel::IDEAL, \App\Enums\ClusterLabel::NORMAL => $currentDifficulty->index() + 1,
                        \App\Enums\ClusterLabel::STRUGGLING, \App\Enums\ClusterLabel::GAMING_THE_SYSTEM => $currentDifficulty->index(),
                    };
                    $targetDifficulty = \App\Enums\SoalDifficulty::fromIndex($nextDiffIndex) ?? $currentDifficulty;
                }
            }

            $appendSoalId = $this->soalModel
                ->where('id_level', $levelId)
                ->where('status', 1)
                ->where('difficulty', $targetDifficulty->value)
                ->whereNotIn('id', $historySoalIds->toArray())
                ->inRandomOrder()
                ->value('id');

            if (!$appendSoalId) {
                $appendSoalId = $this->soalModel
                    ->where('id_level', $levelId)
                    ->where('status', 1)
                    ->whereNotIn('id', $historySoalIds->toArray())
                    ->inRandomOrder()
                    ->value('id');
            }
        }

        $orderedSoalIds = $historySoalIds->toArray();
        if ($appendSoalId) {
            $orderedSoalIds[] = $appendSoalId;
        }

        // 🔥 PERBAIKAN FATAL: MENGISI SISA KOTAK AGAR UI TETAP 10 PASANG
        $kurang = $limitPasang - count($orderedSoalIds);
        if ($kurang > 0) {
            $placeholderIds = $this->soalModel
                ->where('id_level', $levelId)
                ->where('status', 1)
                ->whereNotIn('id', $orderedSoalIds)
                ->orderBy('order', 'asc')
                ->limit($kurang)
                ->pluck('id')
                ->toArray();
            
            $orderedSoalIds = array_merge($orderedSoalIds, $placeholderIds);
        }

        // AMBIL DATA KONVERSI 
        $konversiBySoal = DB::table('bank_soal_konversi as bsk')
            ->leftJoin('soal as s', 's.id', '=', 'bsk.id_soal')
            ->where('bsk.id_level', $levelId)
            ->whereIn('bsk.id_soal', $orderedSoalIds)
            ->select([
                'bsk.id',
                'bsk.id_soal',
                's.judul as judul_soal',
                'bsk.difficulty',
            ])
            ->get()
            ->keyBy('id_soal');

        // MAP STATUS
        $ujianDoneMap = $this->ujianModel
            ->where('id_mahasiswa', $mahasiswa->id)
            ->where('id_level', $levelId)
            ->whereIn('id_soal', $orderedSoalIds)
            ->where('status', 1)
            ->pluck('id_soal')
            ->mapWithKeys(fn ($id) => [$id => true])
            ->toArray();

        $ujianKonversiDoneMap = DB::table('ujian_kode')
            ->where('id_mahasiswa', $mahasiswa->id)
            ->where('id_level', $levelId)
            ->pluck('id_bank_soal_konversi')
            ->mapWithKeys(fn ($id) => [$id => true])
            ->toArray();

        $badgeBySoal = empty($orderedSoalIds) ? [] : $this->labelSkorModel
            ->where('id_mahasiswa', $mahasiswa->id)
            ->where('id_level', $levelId)
            ->whereIn('id_soal', $orderedSoalIds)
            ->pluck('label', 'id_soal')
            ->toArray();

        // 3. BANGUN STRUKTUR ZIG-ZAG 
        $result = [];
        $unlockNext = true;

        foreach ($orderedSoalIds as $soalId) {
            $soal = $this->soalModel->find($soalId);
            if (!$soal) continue;

            $konversi = $konversiBySoal->get($soal->id);

            $isPseudoDone = $this->ujianModel
                ->where('id_mahasiswa', $mahasiswa->id)
                ->where('id_soal', $soal->id)
                ->where('status', 1)
                ->exists();

            $isKonversiDone = false;
            if ($konversi) {
                $isKonversiDone = DB::table('ujian_kode')
                    ->where('id_mahasiswa', $mahasiswa->id)
                    ->where('id_bank_soal_konversi', $konversi->id)
                    ->exists();
            }

            $pseudoStatus   = !$unlockNext ? 'locked' : ($isPseudoDone ? 'done' : 'active');
            $konversiStatus = !$isPseudoDone ? 'locked' : ($isKonversiDone ? 'done' : 'active');

            $badge = $this->labelSkorModel
                ->where('id_mahasiswa', $mahasiswa->id)
                ->where('id_level', $levelId)
                ->where('id_soal', $soal->id)
                ->value('label');

            // KOTAK KIRI
            $result[] = [
                'type' => 'soal',
                'id' => $soal->id,
                'judul' => ($pseudoStatus === 'locked') ? null : $soal->judul,
                'difficulty' => $soal->difficulty,
                'status' => $pseudoStatus,
                'badge' => $badge,
                'is_tambahan' => false,
            ];

            // KOTAK KANAN
            if ($konversi) {
                $result[] = [
                    'type' => 'konversi',
                    'id' => $konversi->id,
                    'judul' => ($konversiStatus === 'locked') ? null : 'Konversi: ' . ($konversi->judul_soal ?? $soal->judul),
                    'difficulty' => $soal->difficulty,
                    'status' => $konversiStatus,
                    'is_tambahan' => false,
                ];
            } else {
                $result[] = [
                    'type' => 'konversi',
                    'id' => 'dummy-'.$soal->id,
                    'judul' => null, 
                    'difficulty' => $soal->difficulty,
                    'status' => 'locked', 
                    'is_tambahan' => false,
                ];
            }

            if (!$isPseudoDone || (!$isKonversiDone && $konversi)) {
                $unlockNext = false;
            } elseif (!$konversi) {
                $unlockNext = false; 
            }
        }

        // 4. KUNCIAN LINEAR (Agar kotak tidak loncat)
        $firstActiveSet = false;
        foreach ($result as $index => $row) {
            if ($row['status'] === 'done') continue;

            if (str_starts_with((string)$row['id'], 'dummy-')) {
                $result[$index]['status'] = 'locked';
                continue;
            }

            if (! $firstActiveSet) {
                $result[$index]['status'] = 'active';
                $firstActiveSet = true;
                continue;
            }

            $result[$index]['status'] = 'locked';
        }

        // 5. AMBIL NILAI 
        $konversiIds = array_values(array_unique(array_column(array_filter($result, fn ($item) => $item['type'] === 'konversi' && !str_starts_with((string)$item['id'], 'dummy-')), 'id')));
        $nilaiKonversiList = [];

        if (!empty($konversiIds)) {
            $nilaiRows = DB::table('ujian_kode')
                ->where('id_mahasiswa', $mahasiswa->id)
                ->where('id_level', $levelId)
                ->whereIn('id_bank_soal_konversi', $konversiIds)
                ->whereNotNull('nilai')
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($nilaiRows as $row) {
                $konversiItem = collect($result)->firstWhere('id', $row->id_bank_soal_konversi);
                if ($konversiItem && $konversiItem['judul'] !== null) {
                    $nilaiKonversiList[$konversiItem['judul']] = $row->nilai;
                }
            }
        }

        $algopoin = $this->labelSkorModel
            ->where('id_mahasiswa', $mahasiswa->id)
            ->whereNull('id_soal')
            ->whereNull('label')
            ->where('id_level', $levelId)
            ->sum('skor');

        $algobadge = $this->labelSkorModel
            ->where('id_mahasiswa', $mahasiswa->id)
            ->whereNotNull('id_soal')
            ->count();

        $jumlahSoalKonversi = count(array_filter($result, fn($r) => $r['type'] === 'soal'));

        $nyawa = Nyawa::where('id_user', $user->id)->first();
        if ($nyawa) {
            $nyawa->checkAndRegenerate();
        }

        return view('pages.quiz.question-list', [
            'title' => 'List Soal',
            'dataSoal' => $result,
            'algopoin' => $algopoin,
            'levelId' => $level->id,
            'nilaiKonversiList' => $nilaiKonversiList,
            'dataLevel' => $level,
            'jumlahSoalKonversi' => $jumlahSoalKonversi,
            'lives' => $nyawa?->nyawa ?? 0,
            'max_lives' => $nyawa?->max_nyawa ?? 0,
            'next_regen_at' => $nyawa?->next_regen_at ?? null,
        ]);
    }

    public function calculateAvgSkor(Request $request)
    {
        $levelId = $request->input('level_id');
        $userId = Auth::id();
        $idMahasiswa = $this->mahasiswaModel->where('id_user', $userId)->value('id');
        $soalIds = $this->soalModel->where('id_level', $levelId)->where('status', 1)->pluck('id')->toArray();

        $labelSkorSoal = $this->labelSkorModel
            ->where('id_mahasiswa', $idMahasiswa)
            ->where('id_level', $levelId)
            ->whereIn('id_soal', $soalIds)
            ->pluck('skor', 'id_soal')
            ->toArray();

        if (count($soalIds) === 0 || count($labelSkorSoal) < count($soalIds)) {
            return response()->json(['message' => 'Belum memenuhi kriteria perhitungan rata-rata skor.']);
        }

        $totalSkor = array_sum($labelSkorSoal);
        $jumlahSoal = count($soalIds);
        $averageSkor = $jumlahSoal > 0 ? $totalSkor / $jumlahSoal : 0;

        $existing = $this->labelSkorModel
            ->where('id_mahasiswa', $idMahasiswa)
            ->where('id_level', $levelId)
            ->whereNull('id_soal')
            ->whereNull('label')
            ->first();

        if ($existing) {
            if ($existing->skor == $averageSkor) {
                return response()->json(['message' => 'Tidak ada update, skor sama.', 'avgSkor' => $averageSkor]);
            }
            $existing->skor = $averageSkor;
            $existing->updated_at = now();
            $existing->save();
            return response()->json(['message' => 'Skor diperbarui.', 'avgSkor' => $averageSkor]);
        }

        $insertData = [
            'id' => (string) Str::uuid(),
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