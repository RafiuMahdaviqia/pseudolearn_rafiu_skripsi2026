<?php


namespace App\Http\Controllers\Quiz;

use App\Enums\ClusterLabel;
use App\Enums\SoalDifficulty;
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
use Illuminate\Contracts\View\View;
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


    // Filter ujian_kode by mahasiswa id,
    private function scopeUjianKodeMahasiswa($query, string $idMahasiswa, $idUser)
    {
        return $query->where(function ($q) use ($idMahasiswa, $idUser) {
            $q->where('id_mahasiswa', $idMahasiswa)
                ->orWhere('id_mahasiswa', $idUser);
        });
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

            $completedKonversi = $this->scopeUjianKodeMahasiswa(
                $this->ujianKodeModel,
                $mahasiswa->id,
                $userId
            )
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
                ->orderBy('difficulty', 'asc')
                ->first();

            $activeKonversi = $this->bankSoalKonversiModel
                ->setView('v_bank_soal_konversi')
                ->where('id_level', $levelId)
                ->where('status', 1)
                ->whereNotIn('id', function ($q) use ($mahasiswa, $userId, $levelId) {
                    $q->select('id_bank_soal_konversi')
                      ->from((new UjianKode)->getTable())
                      ->where(function ($sub) use ($mahasiswa, $userId) {
                          $sub->where('id_mahasiswa', $mahasiswa->id)
                              ->orWhere('id_mahasiswa', $userId);
                      })
                      ->where('id_level', $levelId);
                })
                ->orderBy('difficulty', 'asc')
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
        // 1) Ambil level dari query string, lalu muat semua soal aktif dan konversi aktif pada level itu.
        $levelId = $request->query('level');
        $dataSoal = $this->soalModel->where('id_level', $levelId)->where('status', 1)->orderBy('difficulty', 'asc')->get()->toArray();
        $dataKonversi = $this->bankSoalKonversiModel->setView('v_bank_soal_konversi')->where('id_level', $levelId)->where('status', 1)->orderBy('difficulty', 'asc')->get()->toArray();

        $idUser = Auth::id();
        $idMahasiswa = $this->mahasiswaModel->where('id_user', $idUser)->value('id');
        $dataUjian = $this->ujianModel->where('id_mahasiswa', $idMahasiswa)
            ->where('id_level', $levelId)
            ->where('status', 1)
            ->get()
            ->toArray();

        // 3) Hitung algopoin level dari record agregat (id_soal null, label null).
        $algopoin = $this->labelSkorModel
            ->where('id_mahasiswa', $idMahasiswa)
            ->whereNull('id_soal')
            ->whereNull('label')
            ->where('id_level', $levelId)
            ->sum('skor');

        // 4) Buat index konversi berdasarkan id_soal agar mudah dipasangkan ke soal utama.
        $konversiBySoal = [];
        foreach ($dataKonversi as $konversi) {
            $konversiBySoal[$konversi['id_soal']] = $konversi;
            $dataUjianKonversi = $this->scopeUjianKodeMahasiswa(
                $this->ujianKodeModel,
                $idMahasiswa,
                $idUser
            )
                ->where('id_level', $levelId)
                ->where('id_bank_soal_konversi', $konversi['id'])
                ->first();
            if ($dataUjianKonversi) {
                $konversiBySoal[$konversi['id_soal']]['ujianKonversi'] = $dataUjianKonversi->toArray();
            } else {
                $konversiBySoal[$konversi['id_soal']]['ujianKonversi'] = null;
            }
        }

        // 5) Ubah list ujian jadi map cepat: id_soal => true.
        $ujianBySoal = [];
        foreach ($dataUjian as $ujian) {
            $ujianBySoal[$ujian['id_soal']] = true;
        }

        // 6) Bentuk payload final: soal utama + item konversi (sebagai entri terpisah).
        $result = [];
        foreach ($dataSoal as $soal) {
            $soalId = $soal['id'];

            // Entri soal utama (pseudocode).
            $soalEntry = $soal;
            $soalEntry['type'] = 'soal';
            $soalEntry['konversi'] = $konversiBySoal[$soalId] ?? null;
            $soalEntry['ujianKonversi'] = null; // hanya untuk konversi
            $soalEntry['status'] = isset($ujianBySoal[$soalId]) ? 'done' : 'locked';

            // Badge diambil dari label skor berdasarkan mahasiswa + level + soal.
            $badge = $this->labelSkorModel
                ->where('id_mahasiswa', $idMahasiswa)
                ->where('id_level', $levelId)
                ->where('id_soal', $soalId)
                ->value('label');
            $soalEntry['badge'] = $badge ? $badge : null;

            $result[] = $soalEntry;

            // Tambahkan entri konversi milik soal ini (jika tersedia).
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
        // 7) Normalisasi status agar progres linear:
        //    - status done tetap done,
        //    - item pertama yang belum done menjadi active,
        //    - sisanya locked.
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

        // 8) Ambil nilai konversi yang valid untuk level ini (berdasarkan id konversi yang tampil di list).
        $konversiIds = array_column($dataKonversi, 'id');

        if (empty($konversiIds)) {
            $nilaiKonversiList = [];
        } else {
            $judulByKonversiId = collect($dataKonversi)->mapWithKeys(function ($row) {
                $judul = $row['judul_soal'] ?? $row['judul'] ?? 'Soal konversi';
                return [$row['id'] => $judul];
            });

            $ujianKodeRows = $this->scopeUjianKodeMahasiswa(
                $this->ujianKodeModel,
                $idMahasiswa,
                $idUser
            )
                ->where('id_level', $levelId)
                ->whereIn('id_bank_soal_konversi', $konversiIds)
                ->whereNotNull('nilai')
                ->orderBy('created_at', 'asc')
                ->get(['id_bank_soal_konversi', 'nilai']);

            $nilaiKonversiList = [];
            foreach ($ujianKodeRows as $row) {
                $judul = $judulByKonversiId[$row->id_bank_soal_konversi] ?? 'Soal konversi';
                $nilaiKonversiList[$judul] = $row->nilai;
            }
        }

        $dataLevel = $this->levelModel->find($levelId);
        $jumlahSoalKonversi = $this->bankSoalKonversiModel->where('id_level', $levelId)->where('status', 1)->count();

        $idUser = Auth::id();
        $nyawa = Nyawa::where('id_user', $idUser)->first();

        // 10) Regenerasi nyawa jika sudah waktunya (1 nyawa per 10 menit).
        $nyawa->checkAndRegenerate();

        // 11) Kirim data akhir ke halaman daftar soal.

        // dd($result);

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

    public function listQuestion(Request $request): View
    {
        $validated = $request->validate([
            'level' => ['required'],
        ]);

        $level = Level::find($validated['level']);
        abort_if($level === null, 404, 'Level not found.');

        $user = Auth::getUser();
        abort_if($user === null, 401);

        $mahasiswa = $user->mahasiswa()->first();
        abort_if($mahasiswa === null, 404, 'Mahasiswa not found.');

        $targetDifficulty = SoalDifficulty::EASY;

        $latestUjian = Ujian::query()
            ->where('id_level', $level->id)
            ->where('id_mahasiswa', $mahasiswa->id)
            ->whereNotNull('id_soal')
            ->orderBy('created_at', 'desc')
            ->first(['id_soal']);

        if ($latestUjian !== null && !empty($latestUjian->id_soal)) {
            $latestSoalDifficulty = Soal::query()
                ->whereKey($latestUjian->id_soal)
                ->value('difficulty');

            $currentDifficulty = SoalDifficulty::tryFrom((string) $latestSoalDifficulty) ?? SoalDifficulty::EASY;

            $latestLabel = LabelSkor::query()
                ->where('id_mahasiswa', $mahasiswa->id)
                ->where('id_level', $level->id)
                ->where('id_soal', $latestUjian->id_soal)
                ->whereNotNull('label')
                ->orderBy('created_at', 'desc')
                ->value('label');

            $clusterLabel = ClusterLabel::tryFrom((string) $latestLabel);

            if ($clusterLabel === null) {
                $targetDifficulty = $currentDifficulty;
            } else {
                $nextDifficultyIndex = match ($clusterLabel) {
                    ClusterLabel::IDEAL, ClusterLabel::NORMAL => $currentDifficulty->index() + 1,
                    ClusterLabel::STRUGGLING, ClusterLabel::GAMING_THE_SYSTEM => $currentDifficulty->index(),
                };

                $targetDifficulty = SoalDifficulty::fromIndex($nextDifficultyIndex) ?? $currentDifficulty;
            }
        }

        $difficulty = $targetDifficulty->value;

        $allSoal = Soal::query()
            ->where('id_level', $level->id)
            ->where('status', 1)
            ->orderBy('order', 'asc')
            ->get();

        $allSoalById = $allSoal->keyBy('id');

        $doneSoalIds = Ujian::query()
            ->where('id_mahasiswa', $mahasiswa->id)
            ->where('id_level', $level->id)
            ->whereNotNull('id_soal')
            ->where('status', 1)
            ->pluck('id_soal')
            ->all();

        $doneSoalMap = $doneSoalIds === [] ? [] : array_fill_keys($doneSoalIds, true);

        $shouldIncreaseLimit = false;
        $baseLimit = (int) ($level->limit_soal ?? 0);
        $extraLimit = $shouldIncreaseLimit ? (int) ($level->limit_ars ?? 0) : 0;
        $effectiveLimit = max(0, $baseLimit + $extraLimit);

        $ujianBySoal = Ujian::query()
            ->where('id_mahasiswa', $mahasiswa->id)
            ->where('id_level', $level->id)
            ->whereNotNull('id_soal')
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy('id_soal');

        $firstAttemptBySoal = $ujianBySoal->map(fn($items) => $items->first()->created_at);

        $historySoalIdsOrdered = $firstAttemptBySoal
            ->sortBy(fn($createdAt) => $createdAt ? $createdAt->getTimestamp() : 0) // oldest first
            ->keys()
            ->values();

        if ($historySoalIdsOrdered->count() > $effectiveLimit) {
            $historySoalIdsOrdered = $historySoalIdsOrdered
                ->take($effectiveLimit)
                ->values();
        }

        $historyCount = $historySoalIdsOrdered->count();
        $shouldAppendNew = $historyCount < $effectiveLimit;

        $appendSoalId = null;
        if ($shouldAppendNew) {
            $candidateSoalIds = $allSoal
                ->where('difficulty', $difficulty)
                ->pluck('id')
                ->values();

            $historySoalIdSet = $historySoalIdsOrdered->flip();

            if ($candidateSoalIds->isNotEmpty()) {
                $availableSoalIds = $candidateSoalIds
                    ->reject(fn($soalId) => $historySoalIdSet->has($soalId))
                    ->values();

                if ($availableSoalIds->isNotEmpty()) {
                    $appendSoalId = $availableSoalIds->shuffle()->first();
                } else {
                    $repeatCandidates = $candidateSoalIds
                        ->filter(fn($soalId) => isset($doneSoalMap[$soalId]))
                        ->values();

                    if ($repeatCandidates->isEmpty()) {
                        $repeatCandidates = $historySoalIdsOrdered
                            ->filter(fn($soalId) => isset($doneSoalMap[$soalId]))
                            ->values();
                    }

                    if ($repeatCandidates->isNotEmpty()) {
                        $appendSoalId = $repeatCandidates->shuffle()->first();
                    }
                }
            } else {
                $repeatCandidates = $historySoalIdsOrdered
                    ->filter(fn($soalId) => isset($doneSoalMap[$soalId]))
                    ->values();

                if ($repeatCandidates->isNotEmpty()) {
                    $appendSoalId = $repeatCandidates->shuffle()->first();
                }
            }
        }

        $orderedSoalIds = $historySoalIdsOrdered;
        if ($appendSoalId !== null) {
            $orderedSoalIds = $orderedSoalIds
                ->concat([$appendSoalId])
                ->values();
        }

        $orderedSoalIdsArray = $orderedSoalIds->all();

        $dataKonversi = [];
        if ($orderedSoalIdsArray !== []) {
            $dataKonversi = $this->konversiModel
                ->newQuery()
                ->select([
                    'konversi.id',
                    'konversi.id_level',
                    'konversi.id_soal',
                    's.judul as judul_soal',
                    's.soal as soal_name',
                    'konversi.jawaban',
                    'konversi.output',
                    'konversi.bobot',
                    'konversi.created_at',
                    'konversi.updated_at',
                    's.status',
                    's.order',
                ])
                ->join('soal as s', 's.id', '=', 'konversi.id_soal')
                ->where('konversi.id_level', $level->id)
                ->where('s.status', 1)
                ->whereIn('konversi.id_soal', $orderedSoalIdsArray)
                ->orderBy('s.order', 'asc')
                ->get()
                ->toArray();
        }

        $konversiIds = array_values(array_unique(array_column($dataKonversi, 'id')));
        $ujianKonversiById = [];

        if ($konversiIds !== []) {
            $ujianKonversiById = $this->ujianKonversiModel
                ->where('id_mahasiswa', $mahasiswa->id)
                ->where('id_level', $level->id)
                ->whereIn('id_soal_konversi', $konversiIds)
                ->orderBy('created_at', 'asc')
                ->get()
                ->keyBy('id_soal_konversi')
                ->map(fn($item) => $item->toArray())
                ->toArray();
        }

        $konversiBySoal = [];
        $konversiById = [];
        foreach ($dataKonversi as $konversi) {
            $konversi['ujianKonversi'] = $ujianKonversiById[$konversi['id']] ?? null;
            $konversiBySoal[$konversi['id_soal']] = $konversi;
            $konversiById[$konversi['id']] = $konversi;
        }

        $badgeBySoal = [];
        if ($orderedSoalIdsArray !== []) {
            $badgeBySoal = LabelSkor::query()
                ->where('id_mahasiswa', $mahasiswa->id)
                ->where('id_level', $level->id)
                ->whereIn('id_soal', $orderedSoalIdsArray)
                ->pluck('label', 'id_soal')
                ->toArray();
        }

        $result = [];
        foreach ($orderedSoalIdsArray as $soalId) {
            $soalModel = $allSoalById->get($soalId);
            if (! $soalModel) {
                continue;
            }

            $soalPayload = $soalModel->toArray();

            $soalEntry = $soalPayload;
            $soalEntry['type'] = 'soal';
            $soalEntry['konversi'] = $konversiBySoal[$soalId] ?? null;
            $soalEntry['ujianKonversi'] = null;
            $soalEntry['status'] = isset($doneSoalMap[$soalId]) ? 'done' : 'locked';
            $soalEntry['badge'] = $badgeBySoal[$soalId] ?? null;
            $result[] = $soalEntry;

            if (isset($konversiBySoal[$soalId])) {
                $konversi = $konversiBySoal[$soalId];
                $konversiEntry = $konversi;
                $konversiEntry['type'] = 'konversi';
                $konversiEntry['soal'] = $soalPayload;
                $konversiEntry['badge'] = null;
                $konversiEntry['status'] = $konversi['ujianKonversi'] ? 'done' : 'locked';
                $result[] = $konversiEntry;
            }
        }

        $firstActiveSet = false;
        foreach ($result as $index => $row) {
            if ($row['status'] === 'done') {
                continue;
            }

            if (! $firstActiveSet) {
                $result[$index]['status'] = 'active';
                $firstActiveSet = true;
                continue;
            }

            $result[$index]['status'] = 'locked';
        }

        $algopoin = LabelSkor::query()
            ->where('id_mahasiswa', $mahasiswa->id)
            ->whereNull('id_soal')
            ->whereNull('label')
            ->where('id_level', $level->id)
            ->sum('skor');

        $konversiIdsForNilai = array_keys($konversiById);
        $nilaiKonversiList = [];

        if ($konversiIdsForNilai !== []) {
            $nilaiRows = $this->ujianKonversiModel
                ->where('id_mahasiswa', $mahasiswa->id)
                ->where('id_level', $level->id)
                ->whereIn('id_soal_konversi', $konversiIdsForNilai)
                ->whereNotNull('nilai')
                ->orderBy('created_at', 'asc')
                ->get(['id_soal_konversi', 'nilai']);

            foreach ($nilaiRows as $row) {
                $konversi = $konversiById[$row->id_soal_konversi] ?? null;
                if ($konversi === null) {
                    continue;
                }

                $judul = $konversi['judul_soal']
                    ?? $konversi['judul']
                    ?? ($konversi['soal']['judul'] ?? ('Konversi ' . $row->id_soal_konversi));
                $nilaiKonversiList[$judul] = $row->nilai;
            }
        }

        $jumlahSoalKonversi = count($dataKonversi);
        $nyawa = Nyawa::where('id_user', $user->id)->first();

        // Check and regenerate lives (1 life per 10 minutes)
        if ($nyawa) {
            $nyawa->checkAndRegenerate();
        }

        return view('pages.quiz.question-list', [
            'title' => 'List Soal',
            'dataSoal' => $result,
            'algopoin' => $algopoin,
            'levelId' => $level->id,
            'dataLevel' => $level,
            'nilaiKonversiList' => $nilaiKonversiList,
            'jumlahSoalKonversi' => $jumlahSoalKonversi,
            'lives' => $nyawa?->nyawa ?? 0,
            'max_lives' => $nyawa?->max_nyawa ?? 0,
            'next_regen_at' => $nyawa?->next_regen_at
        ]);
    }
}
