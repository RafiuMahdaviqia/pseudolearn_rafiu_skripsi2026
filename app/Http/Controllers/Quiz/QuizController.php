<?php

namespace App\Http\Controllers\Quiz;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\LabelSkor;
use App\Models\Mahasiswa;
use App\Models\ArsResult;
use App\Models\BankSoalKonversi;
use App\Models\Nyawa;
use App\Models\Soal;
use App\Models\Ujian;
use App\Models\UjianKode;
use App\Services\KonversiService;
use App\Services\LevelService;
use App\Services\SoalService;
use App\Services\ArsReportService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class QuizController extends Controller
{
    protected $levelService;
    protected $soalService;
    protected $konversiService;
    protected $arsReportService;
    protected $soalModel;
    protected $mahasiswaModel;
    protected $ujianModel;
    protected $labelSkorModel;
    protected $ujianKodeModel;
    protected $levelModel;
    protected $bankSoalKonversiModel;
    protected $visibleLimit = 5;

    public function __construct()
    {
        $this->levelService = new LevelService();
        $this->soalService = new SoalService();
        $this->konversiService = new KonversiService();
        $this->arsReportService = new ArsReportService();
        $this->soalModel = new Soal();
        $this->mahasiswaModel = new Mahasiswa();
        $this->ujianModel = new Ujian();
        $this->labelSkorModel = new LabelSkor();
        $this->ujianKodeModel = new UjianKode();
        $this->levelModel = new Level();
        $this->bankSoalKonversiModel = new BankSoalKonversi();
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

            $completedKonversi = $this->scopeUjianKodeMahasiswa(
                $this->ujianKodeModel,
                $mahasiswa->id,
                $userId
            )
                ->where('id_level', $levelId)
                ->distinct('id_bank_soal_konversi')
                ->count('id_bank_soal_konversi');

            $algopoinPerLevel = $this->labelSkorModel
                ->where('id_mahasiswa', $mahasiswa->id)
                ->whereNull('id_soal')
                ->whereNull('label')
                ->where('id_level', $levelId)
                ->sum('skor');

            $allSoalDone     = ($totalSoal == 0) || ($completedSoal >= $totalSoal);
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
        // Hapus pengecekan order/level sebelumnya di sini jika ada.

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

        // Limit display to maximum N pairs (soal + konversi per soal)
        // according to `visibleLimit` (default: 5 pairs).
        $limited = [];
        $pairCount = 0;
        foreach ($result as $row) {
            if ($row['type'] === 'soal') {
                if ($pairCount >= $this->visibleLimit) {
                    break;
                }
                $pairCount++;
            }
            $limited[] = $row;
        }
        $result = $limited;

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

        // Ambil level untuk mengetahui limit_soal
        $level = $this->levelModel->find($levelId);
        $effectiveLimit = max(1, (int) ($level->limit_soal ?? $this->visibleLimit));

        // Ambil semua soal pada level ini (urut berdasarkan order)
        $allSoal = $this->soalModel->where('id_level', $levelId)->where('status', 1)->orderBy('order', 'asc')->get();

        // Tentukan soal mana yang masuk hitungan:
        // 1. Soal yang sudah dikerjakan (ada di ujian), diurutkan berdasarkan first attempt
        // 2. Jika jumlah soal yang sudah dikerjakan < limit, tambahkan soal baru dari urutan order
        $ujianBySoal = $this->ujianModel
            ->where('id_mahasiswa', $idMahasiswa)
            ->where('id_level', $levelId)
            ->whereNotNull('id_soal')
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy('id_soal');

        $firstAttemptBySoal = $ujianBySoal->map(fn($items) => $items->first()->created_at);

        $historySoalIdsOrdered = $firstAttemptBySoal
            ->sortBy(fn($createdAt) => $createdAt ? $createdAt->getTimestamp() : 0)
            ->keys()
            ->values();

        if ($historySoalIdsOrdered->count() > $effectiveLimit) {
            $historySoalIdsOrdered = $historySoalIdsOrdered->take($effectiveLimit)->values();
        }

        $historyCount = $historySoalIdsOrdered->count();
        $soalIds = $historySoalIdsOrdered->toArray();

        // Jika masih kurang dari limit, tambahkan soal dari urutan order yang belum ada di history
        if ($historyCount < $effectiveLimit) {
            $existingIds = $historySoalIdsOrdered->flip();
            $candidates = $allSoal->reject(fn($s) => $existingIds->has($s->id))->pluck('id')->toArray();
            $needed = $effectiveLimit - $historyCount;
            $soalIds = array_merge($soalIds, array_slice($candidates, 0, $needed));
        }

        // Ambil labelSkor untuk soal-soal tersebut
        $labelSkorSoal = $this->labelSkorModel
            ->where('id_mahasiswa', $idMahasiswa)
            ->where('id_level', $levelId)
            ->whereIn('id_soal', $soalIds)
            ->pluck('skor', 'id_soal')
            ->toArray();

        // Cek apakah semua soal dalam batas limit sudah dikerjakan
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
        $levelId = $request->query('level');
        if ($levelId !== null && $request->query('level') !== $levelId) {
            $request->query->set('level', $levelId);
        }

        return $this->questionList($request);
    }


    /**
     * Get the next difficulty in progression (easy → medium → hard).
     */
    private function getProgressDifficulty($currentDifficulty)
    {
        return match(strtolower($currentDifficulty)) {
            'easy'   => 'medium',
            'medium' => 'hard',
            default  => null
        };
    }

    /**
     * Get the same difficulty for ARS additional questions.
     */
    private function getNextDifficulty($lastDifficulty)
    {
        return match(strtolower($lastDifficulty)) {
            'easy'   => 'easy',
            'medium' => 'medium',
            'hard'   => 'hard',
            default  => 'easy'
        };
    }

    /**
     * Append an additional soal (and its konversi) to the result array.
     */
    private function appendSoalTambahan(&$result, $idMahasiswa, $idUser, $levelId, $difficulty, $isArs, $batch)
    {
        if (!$difficulty) return null;

        // Exclude soal IDs already in the result
        $excludeIds = collect($result)
            ->where('type', 'soal')
            ->pluck('id')
            ->toArray();

        // Check if there's an unfinished ARS result
        $arsResultBelumSelesai = ArsResult::where('id_mahasiswa', $idMahasiswa)
            ->where('id_level', $levelId)
            ->whereNull('konversi_label')
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

        $konversiTambahan = $this->bankSoalKonversiModel
            ->where('id_soal', $soalTambahan->id)
            ->where('id_level', $levelId)
            ->first();

        $isPseudoDone = $this->ujianModel
            ->where('id_mahasiswa', $idMahasiswa)
            ->where('id_soal', $soalTambahan->id)
            ->where('status', 1)
            ->exists();

        $isKonversiDone = false;
        if ($konversiTambahan) {
            $isKonversiDone = $this->ujianKodeModel
                ->where('id_mahasiswa', $idUser)
                ->where('id_bank_soal_konversi', $konversiTambahan->id)
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
}
