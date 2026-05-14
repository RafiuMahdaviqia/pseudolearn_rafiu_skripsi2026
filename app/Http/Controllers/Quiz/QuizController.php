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
use Illuminate\Support\Facades\Log;
use App\Models\ArsResult;
use Illuminate\Http\JsonResponse;
use \Illuminate\Support\Str;


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
    protected $visibleLimit = 5;

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

        if ($dataLevelResponse instanceof JsonResponse) {
            $dataLevel = $dataLevelResponse->getData(true);
        } else {
            $dataLevel = $dataLevelResponse;
        }

        $userId = Auth::id();
        $mahasiswa = $this->mahasiswaModel->where('id_user', $userId)->first();
        $levelCompletion = [];

        foreach ($dataLevel as $i => $level) {
            $levelId = $level['id'];

            // Total aktif
            $totalSoal = min(
                $this->soalModel->where('id_level', $levelId)->where('status', 1)->count(),
                $this->visibleLimit
            );

            $totalKonversi = min(
                $this->konversiModel->setView('v_konversi')
                    ->where('id_level', $levelId)
                    ->where('status', 1)
                    ->count(),
                $this->visibleLimit
            );

            // Distinct
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

            $algopoinPerLevel = $this->labelSkorModel
                ->where('id_mahasiswa', $mahasiswa->id)
                ->whereNull('id_soal')
                ->whereNull('label')
                ->where('id_level', $levelId)
                ->sum('skor');

            // Pseudo & konversi aktif blm dikerjakan
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

            $dataLevel[$i]['jumlahSoalPseudocode'] = $totalSoal;
            $dataLevel[$i]['jumlahSoalKonversi'] = $totalKonversi;
            $dataLevel[$i]['jumlahSoalPseudocodeSelesai'] = $completedSoal;
            $dataLevel[$i]['jumlahSoalKonversiSelesai'] = $completedKonversi;
            $dataLevel[$i]['algopoin'] = $algopoinPerLevel;
            $dataLevel[$i]['isLevelCompleted'] = $isLevelCompleted;

            // Aktif blm dikerjakan
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

        // Locking
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

        // Hitung badge soal dan id_soal aktif
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
        $visibleLimit = $this->visibleLimit;
        $pairCount = 0;
        $unlockNext = true;

        foreach ($soalList as $soal) {
            if ($pairCount >= $visibleLimit) break;

            $konversi = $this->konversiModel
                ->setView('v_konversi')
                ->where('id_soal', $soal->id)
                ->first();

            $isPseudoDone = $this->ujianModel
                ->where('id_mahasiswa', $idMahasiswa)
                ->where('id_soal', $soal->id)
                ->where('status', 1)
                ->exists();

            $isKonversiDone = false;
            if ($konversi) {
                $isKonversiDone = $this->ujianKonversiModel
                    ->where('id_mahasiswa', $idMahasiswa)
                    ->where('id_soal_konversi', $konversi->id)
                    ->exists();
            }

            if (!$unlockNext) {
                $pseudoStatus = 'locked';
            } elseif ($isPseudoDone) {
                $pseudoStatus = 'done';
            } else {
                $pseudoStatus = 'active';
            }

            if (!$isPseudoDone) {
                $konversiStatus = 'locked';
            } elseif ($isKonversiDone) {
                $konversiStatus = 'done';
            } else {
                $konversiStatus = 'active';
            }

            $badge = $this->labelSkorModel
                ->where('id_mahasiswa', $idMahasiswa)
                ->where('id_level', $levelId)
                ->where('id_soal', $soal->id)
                ->value('label');

            $result[] = [
                'type'       => 'soal',
                'id'         => $soal->id,
                'judul'      => ($pseudoStatus === 'locked') ? null : $soal->judul,
                'difficulty' => $soal->difficulty,
                'status'     => $pseudoStatus,
                'badge'      => $badge
            ];

            if ($konversi) {
                $result[] = [
                    'type'       => 'konversi',
                    'id'         => $konversi->id,
                    'judul'      => ($konversiStatus === 'locked') ? null : ($konversi->judul_soal ?? $konversi->judul ?? null),
                    'difficulty' => $soal->difficulty,
                    'status'     => $konversiStatus
                ];
            }

            if (!$isPseudoDone || !$isKonversiDone) {
                $unlockNext = false;
            }

            $pairCount++;
        }

        // Progress ARS
        $allMainDone    = collect($result)->every(fn($r) => $r['status'] === 'done');
        $totalMainPairs = collect($result)->where('type', 'soal')->count();

        if ($allMainDone && $totalMainPairs >= $visibleLimit) {

            $arsService = new \App\Services\ArsReportService();
            $arsData    = $arsService->processArs($idMahasiswa, $levelId);

            Log::info('ARS DEBUG', [
                'total_pair'  => $arsData['total_pair'],
                'total_ars'   => $arsData['total_ars'],
                'lastDiff'    => collect($arsData['data'])->last()['difficulty'] ?? null,
                'pseudoLabel' => collect($arsData['data'])->last()['pseudo']['label'] ?? null,
            ]);

            $lastPair       = collect($arsData['data'])->last();
            $lastDifficulty = $lastPair['difficulty'] ?? 'easy';
            $pseudoLabel    = $lastPair['pseudo']['label'] ?? 'Struggling';
            $konversiLabel  = $lastPair['konversi']['label'] ?? 'Struggling';

            $isStable = in_array($pseudoLabel, ['Ideal', 'Normal']) &&
                        in_array($konversiLabel, ['Ideal', 'Normal']);

            $result = array_values(collect($result)
                ->filter(fn($r) => !isset($r['is_tambahan']) || $r['is_tambahan'] === false)
                ->toArray());

            // Soal ARS finish
            $arsResultDone = \App\Models\ArsResult::where('id_mahasiswa', $idMahasiswa)
                ->where('id_level', $levelId)
                ->whereNotNull('pseudo_label')
                ->whereNotNull('konversi_label')
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($arsResultDone as $arsItem) {
                $soalArs = $this->soalModel->find($arsItem->id_soal);
                if (!$soalArs) continue;

                $konversiArs = $this->konversiModel
                    ->setView('v_konversi')
                    ->where('id_soal', $soalArs->id)
                    ->first();

                $result[] = [
                    'type'        => 'soal',
                    'id'          => $soalArs->id,
                    'judul'       => $soalArs->judul,
                    'difficulty'  => $soalArs->difficulty,
                    'status'      => 'done',
                    'badge'       => null,
                    'is_tambahan' => true,
                    'batch'       => $arsItem->ars_batch,
                ];

                if ($konversiArs) {
                    $result[] = [
                        'type'        => 'konversi',
                        'id'          => $konversiArs->id,
                        'judul'       => $konversiArs->judul_soal ?? $konversiArs->judul ?? null,
                        'difficulty'  => $soalArs->difficulty,
                        'status'      => 'done',
                        'is_tambahan' => true,
                        'batch'       => $arsItem->ars_batch,
                    ];
                }
            }

            // Soal ARS belum selesai
            $arsResultAktif = ArsResult::where('id_mahasiswa', $idMahasiswa)
                ->where('id_level', $levelId)
                ->whereNull('konversi_label')
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($arsResultAktif as $arsItem) {
                $soalArs = $this->soalModel->find($arsItem->id_soal);
                if (!$soalArs) continue;

                $konversiArs = $this->konversiModel
                    ->setView('v_konversi')
                    ->where('id_soal', $soalArs->id)
                    ->first();

                $isPseudoDone = $this->ujianModel
                    ->where('id_mahasiswa', $idMahasiswa)
                    ->where('id_soal', $soalArs->id)
                    ->where('status', 1)
                    ->exists();

                $isKonversiDone = false;
                if ($konversiArs) {
                    $isKonversiDone = $this->ujianKonversiModel
                        ->where('id_mahasiswa', $idMahasiswa)
                        ->where('id_soal_konversi', $konversiArs->id)
                        ->exists();
                }

                $result[] = [
                    'type'        => 'soal',
                    'id'          => $soalArs->id,
                    'judul'       => $soalArs->judul,
                    'difficulty'  => $soalArs->difficulty,
                    'status'      => $isPseudoDone ? 'done' : 'active',
                    'badge'       => null,
                    'is_tambahan' => true,
                    'batch'       => $arsItem->ars_batch,
                ];

                if ($konversiArs) {
                    $result[] = [
                        'type'        => 'konversi',
                        'id'          => $konversiArs->id,
                        'judul'       => $konversiArs->judul_soal ?? $konversiArs->judul ?? null,
                        'difficulty'  => $soalArs->difficulty,
                        'status'      => !$isPseudoDone ? 'locked' : ($isKonversiDone ? 'done' : 'active'),
                        'is_tambahan' => true,
                        'batch'       => $arsItem->ars_batch,
                    ];
                }
            }

            // Tahan soal baru jika belum selesai
            $adaYangBelumSelesai = ArsResult::where('id_mahasiswa', $idMahasiswa)
                ->where('id_level', $levelId)
                ->whereNull('konversi_label')
                ->exists();

            if ($isStable && $lastDifficulty === 'hard') {

            } elseif (!$adaYangBelumSelesai && !$isStable && $lastDifficulty === 'hard') {
                $soalTambahan = $this->appendSoalTambahan($result, $idMahasiswa, $levelId, 'hard', true, $arsData['batch_count']);

                if ($soalTambahan) {
                    $exists = ArsResult::where('id_mahasiswa', $idMahasiswa)
                        ->where('id_level', $levelId)
                        ->where('id_soal', $soalTambahan['id'])
                        ->exists();

                    if (!$exists) {
                        $jumlahSoalTambahan = ArsResult::where('id_mahasiswa', $idMahasiswa)
                            ->where('id_level', $levelId)
                            ->count();

                        ArsResult::create([
                            'id'           => Str::uuid(),
                            'id_mahasiswa' => $idMahasiswa,
                            'id_level'     => $levelId,
                            'id_soal'      => $soalTambahan['id'],
                            'ars_batch'    => floor($jumlahSoalTambahan / 5) + 1,
                            'difficulty'   => $soalTambahan['difficulty'],
                        ]);
                    }
                }

            } elseif (!$adaYangBelumSelesai && $isStable && $lastDifficulty !== 'hard') {
                $nextDifficulty = $this->getProgressDifficulty($lastDifficulty);
                $this->appendSoalTambahan($result, $idMahasiswa, $levelId, $nextDifficulty, false, $arsData['batch_count']);

            } else {
                if (!$adaYangBelumSelesai && $arsData['total_ars'] > 0) {
                    $nextDifficulty = $this->getNextDifficulty($lastDifficulty);
                    $soalTambahan   = $this->appendSoalTambahan($result, $idMahasiswa, $levelId, $nextDifficulty, true, $arsData['batch_count']);

                    if ($soalTambahan) {
                        $exists = ArsResult::where('id_mahasiswa', $idMahasiswa)
                            ->where('id_level', $levelId)
                            ->where('id_soal', $soalTambahan['id'])
                            ->exists();

                        if (!$exists) {
                            $jumlahSoalTambahan = ArsResult::where('id_mahasiswa', $idMahasiswa)
                                ->where('id_level', $levelId)
                                ->count();

                            ArsResult::create([
                                'id'           => Str::uuid(),
                                'id_mahasiswa' => $idMahasiswa,
                                'id_level'     => $levelId,
                                'id_soal'      => $soalTambahan['id'],
                                'ars_batch'    => floor($jumlahSoalTambahan / 5) + 1,
                                'difficulty'   => $soalTambahan['difficulty'],
                            ]);
                        }
                    }
                }
            }
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
            'title'             => 'List Soal',
            'dataSoal'          => $result,
            'algopoin'          => $algopoin,
            'levelId'           => $levelId,
            'nilaiKonversiList' => [],
            'dataLevel'         => $dataLevel,
            'jumlahSoalKonversi' => $jumlahSoalKonversi,
            'lives'             => $nyawa->nyawa,
            'max_lives'         => $nyawa->max_nyawa,
            'next_regen_at'     => $nyawa->next_regen_at
        ]);
    }

    private function getProgressDifficulty($currentDifficulty)
    {
        // naik difficulty
        return match(strtolower($currentDifficulty)) {
            'easy'   => 'medium',
            'medium' => 'hard',
            default  => null // hard = stop
        };
    }    

    private function getNextDifficulty($lastDifficulty)
    {
        // soal tambahan ARS = difficulty sama dengan pair terakhir yang bermasalah
        return match(strtolower($lastDifficulty)) {
            'easy'   => 'easy',
            'medium' => 'medium',
            'hard'   => 'hard',
            default  => 'easy'
        };
    }

    private function appendSoalTambahan(&$result, $idMahasiswa, $levelId, $difficulty, $isArs, $batch)
    {
        if (!$difficulty) return null;

        // Id soal utama 1-10
        $excludeIds = collect($result)
            ->where('type', 'soal')
            ->pluck('id')
            ->toArray();

        // Soal tambahan diberikan tapi belum selesai
        $arsResultBelumSelesai = ArsResult::where('id_mahasiswa', $idMahasiswa)
            ->where('id_level', $levelId)
            ->whereNull('konversi_label')
            ->whereNull('pseudo_label')
            ->first();

        if ($arsResultBelumSelesai) {
            $soalTambahan = $this->soalModel->find($arsResultBelumSelesai->id_soal);
        } else {
            $soalTambahan = $this->soalModel
                ->where('id_level', $levelId)
                ->where('difficulty', $difficulty)
                ->where('status', 1)
                ->whereNotIn('id', function ($q) use ($idMahasiswa, $levelId) {
                    $q->select('id_soal')
                    ->from('ars_result')
                    ->where('id_mahasiswa', $idMahasiswa)
                    ->where('id_level', $levelId);
                })
                ->whereNotIn('id', $excludeIds)
                ->orderBy('order', 'asc')
                ->first();
        }

        Log::info('APPEND SOAL TAMBAHAN', [
            'difficulty'  => $difficulty,
            'found'       => $soalTambahan?->id,
            'judul'       => $soalTambahan?->judul,
            'excludeIds'  => $excludeIds,
        ]);

        if (!$soalTambahan) return null;

        $konversiTambahan = $this->konversiModel
            ->setView('v_konversi')
            ->where('id_soal', $soalTambahan->id)
            ->first();

        $isPseudoDone = $this->ujianModel
            ->where('id_mahasiswa', $idMahasiswa)
            ->where('id_soal', $soalTambahan->id)
            ->where('status', 1)
            ->exists();

        $isKonversiDone = false;
        if ($konversiTambahan) {
            $isKonversiDone = $this->ujianKonversiModel
                ->where('id_mahasiswa', $idMahasiswa)
                ->where('id_soal_konversi', $konversiTambahan->id)
                ->exists();
        }

        $result[] = [
            'type'        => 'soal',
            'id'          => $soalTambahan->id,
            'judul'       => $soalTambahan->judul,
            'difficulty'  => $soalTambahan->difficulty,
            'status'      => $isPseudoDone ? 'done' : 'active',
            'badge'       => null,
            'is_tambahan' => $isArs,
            'batch'       => $batch,
        ];

        if ($konversiTambahan) {
            $result[] = [
                'type'        => 'konversi',
                'id'          => $konversiTambahan->id,
                'judul'       => $konversiTambahan->judul_soal ?? $konversiTambahan->judul ?? null,
                'difficulty'  => $soalTambahan->difficulty,
                'status'      => !$isPseudoDone ? 'locked' : ($isKonversiDone ? 'done' : 'active'),
                'is_tambahan' => $isArs,
                'batch'       => $batch,
            ];
        }

        return [
            'id'         => $soalTambahan->id,
            'difficulty' => $soalTambahan->difficulty,
        ];
    }

    public function calculateAvgSkor(Request $request)
    {
        $levelId = $request->input('level_id');
        $idUser = Auth::id();
        $idMahasiswa = $this->mahasiswaModel->where('id_user', $idUser)->value('id');
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
            } else {
                $existing->skor = $averageSkor;
                $existing->updated_at = now();
                $existing->save();
                return response()->json(['message' => 'Skor diperbarui.', 'avgSkor' => $averageSkor]);
            }
        } else {
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
}
