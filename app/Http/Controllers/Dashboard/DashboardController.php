<?php

namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use App\Models\Soal;
use App\Models\User;
use App\Models\Guide;
use App\Models\Kelas;
use App\Models\Nyawa;
use App\Models\Ujian;
use App\Models\Setting;
use App\Models\LabelSkor;
use App\Models\Mahasiswa;
use App\Models\Pencapaian;
use Illuminate\Http\Request;
use App\Models\HistoryConfidence;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\LeaderboardService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $labelSkorModel;
    protected $leaderboardService;

    public function __construct()
    {
        $this->labelSkorModel = new LabelSkor();
        $this->leaderboardService = new LeaderboardService();
    }

    public function index()
    {
        $isAdmin = Auth::check() && Auth::user()->is_admin;
        $userId = Auth::id();
        if ($isAdmin == 0) {
            $mahasiswa = Mahasiswa::where('id_user', $userId)->first();
            $id_kelas = $mahasiswa ? $mahasiswa->id_kelas : null;
            $kelas = $id_kelas ? Kelas::find($id_kelas) : null;
            $name_kelas = $kelas ? $kelas->name : 'Tidak ada kelas';

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

            $getRankByIdMahasiswa = $this->leaderboardService->getRankByIdMahasiswa($mahasiswa->id);
            $idUser = Auth::id();
            $nyawa = Nyawa::where('id_user', $idUser)->first();

            // Check and regenerate lives (1 life per 10 minutes)
            $nyawa->checkAndRegenerate();

            $dataReturn = [
                'title' => 'Dashboard', // Judul untuk ditampilkan di navbar
                'isAdmin' => $isAdmin,
                'name_kelas' => $name_kelas,
                'id_kelas' => $id_kelas,
                'algopoin' => $algopoin,
                'algobadge' => $algobadge,
                'leaderboard' => $getRankByIdMahasiswa,
                'lives' => $nyawa->nyawa,
                'max_lives' => $nyawa->max_nyawa,
                'next_regen_at' => $nyawa->next_regen_at,
            ];
        } else {
            $dataReturn = [
                'title' => 'Dashboard', // Judul untuk ditampilkan di navbar
                'isAdmin' => $isAdmin,
            ];
        }


        return view('pages.dashboard.index', $dataReturn);
    }

     public function pencapaian()
    {
        $nyawa = Nyawa::where('id_user', Auth::id())->first();

        // Check and regenerate lives (1 life per 10 minutes)
        $nyawa->checkAndRegenerate();

        return view('pages.dashboard.pencapaian', [
            'title' => 'Pencapaian',
            'lives' => $nyawa->nyawa,
            'max_lives' => $nyawa->max_nyawa,
            'next_regen_at' => $nyawa->next_regen_at
        ]);
    }

    public function getPencapaian(Request $request)
    {
        $category = $request->query('category');
        $userId = Auth::id();
        $mahasiswa = Mahasiswa::where('id_user', $userId)->first();

        if (!$mahasiswa) {
            return response()->json([]);
        }

        $pcTable = (new Pencapaian)->getTable();
        $soalTable = (new Soal)->getTable();

        $levelTable = 'level';

        $data = Pencapaian::query()
            ->from($pcTable)
            ->where("$pcTable.id_mahasiswa", $mahasiswa->id)
            ->when($category, function ($q) use ($category, $pcTable) {
            $q->where("$pcTable.category", $category);
            })
            ->leftJoin($soalTable, "$pcTable.id_soal", '=', "$soalTable.id")
            ->leftJoin($levelTable, "$soalTable.id_level", '=', "$levelTable.id")
            // Hanya soal aktif (status = 1) atau pencapaian tanpa soal
            ->where(function ($q) use ($pcTable, $soalTable) {
            $q->whereNull("$pcTable.id_soal")
              ->orWhere("$soalTable.status", 1);
            })
            // Urutkan status (1=claimable, 2=claimed, 0=not eligible)
            ->orderByRaw("FIELD($pcTable.status, 1, 0, 2)")
            // Lalu urutkan berdasarkan urutan level
            ->orderBy("$levelTable.order", 'asc')
            // Lalu berdasarkan created_at soal
            ->orderBy("$soalTable.created_at", 'asc')
            ->select("$pcTable.*")
            ->get();

        return response()->json($data);
    }

    public function claimPencapaian(Request $request)
    {
        $userId = Auth::id();
        $mahasiswa = Mahasiswa::where('id_user', $userId)->first();
    
        if (!$mahasiswa) {
            return response()->json(['message' => 'Mahasiswa tidak ditemukan.'], 404);
        }
    
        $pencapaian = Pencapaian::where('id', $request->id)
            ->where('id_mahasiswa', $mahasiswa->id)
            ->first();
    
        if (!$pencapaian) {
            return response()->json(['message' => 'Pencapaian tidak ditemukan.'], 404);
        }
    
        if ($pencapaian->status !== 1) {
            return response()->json(['message' => 'Pencapaian tidak dapat diklaim.'], 400);
        }
    
        try {
            DB::beginTransaction();
    
            // Update status pencapaian menjadi diklaim (2)
            $pencapaian->status = 2;
            $pencapaian->progress = $pencapaian->max_progress;
            $pencapaian->date_claimed = now();
            $pencapaian->save();
    
            // Tambahkan heart/nyawa ke mahasiswa jika ada
            $dataNyawa = Nyawa::where('id_user', $userId)->first();
            $category = $pencapaian->category;

            if($category == 'leaderboard'){
                $heart = 10;
            } elseif($category == 'badge'){
                $heart = 8;
            } elseif($category == 'soal'){
                $heart = 5;
            } elseif($category == 'konversi'){
                $heart = 5;
            } else{
                $heart = 0;
            }

            if ($heart > 0 && $dataNyawa) {
                $newNyawa = min($dataNyawa->nyawa + $heart, $dataNyawa->max_nyawa);
                $dataNyawa->nyawa = $newNyawa;
                $dataNyawa->save();
            }
    
            DB::commit();
    
            return response()->json([
                'lives' => $dataNyawa->nyawa,
                'max_lives' => $dataNyawa->max_nyawa,
                'message' => 'Pencapaian berhasil diklaim.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengklaim pencapaian.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function dashboardPencapaianList(Request $request)
    {
        $userId = Auth::id();
        $user = User::find($userId);
        if($user->is_admin) {
            return response()->json([]);
        }

        $mahasiswa = Mahasiswa::where('id_user', $userId)->first();
    
        if (!$mahasiswa) {
            return response()->json([]);
        }
    
        $pcTable = (new Pencapaian)->getTable();
        $soalTable = (new Soal)->getTable();
        $levelTable = 'level';
    
        $query = Pencapaian::query()
            ->from($pcTable)
            ->where("$pcTable.id_mahasiswa", $mahasiswa->id)
            ->whereIn('category', ['soal', 'konversi', 'badge'])
            ->leftJoin($soalTable, "$pcTable.id_soal", '=', "$soalTable.id")
            ->leftJoin($levelTable, "$soalTable.id_level", '=', "$levelTable.id")
            // Ambil hanya soal aktif (status = 1), tetapi tetap sertakan record yang tidak terkait soal
            ->where(function ($q) use ($pcTable, $soalTable) {
            $q->whereNull("$pcTable.id_soal")
              ->orWhere("$soalTable.status", 1);
            });
    
        // Tambahkan filter status jika ada di request
        if ($request->has('status')) {
            $query->where("$pcTable.status", $request->status);
        }

        // Tambahkan filter Limit jika ada di request
        if ($request->has('limit')) {
            $query->limit($request->limit);
        }
    
        $data = $query
            ->orderByRaw("FIELD($pcTable.status, 1, 0, 2)")
            ->orderBy("$pcTable.updated_at", 'asc')
            ->select("$pcTable.*")
            // ->limit(5)
            ->get();
        
        $notClaimedCount = Pencapaian::query()
            ->leftJoin('soal', 'soal.id', '=', 'pencapaian.id_soal')
            ->where('pencapaian.id_mahasiswa', $mahasiswa->id)
            ->where('pencapaian.status', 1)
            ->whereIn('pencapaian.category', ['soal', 'konversi', 'badge'])
            // Hanya hitung yang tanpa soal atau soal yang aktif
            ->where(function ($q) {
            $q->whereNull('pencapaian.id_soal')
              ->orWhere('soal.status', 1);
            })
            ->count();

        $data = $data->map(function ($item) use ($notClaimedCount) {
            return [
            'id' => $item->id,
            'name' => $item->name ?? '',
            'desc' => $item->desc ?? '',
            'img' => $item->img ?? '',
            'status' => (int) ($item->status ?? 0),
            'max_progress' => (int) ($item->max_progress ?? 0),
            'progress' => (int) ($item->progress ?? 0),
            'heart' => $item->heart ?? '',
            'countPencapaian' => (int) $notClaimedCount,
            'category' => $item->category ?? 'badge',
            ];
        });
    
        return response()->json($data);
    }

    public function getPencapaianById(Request $request)
    {
        $pencapaianId = $request->query('pencapaian_id');
        $badgeId = $request->query('badge_id');
        $konversiId = $request->query('konversi_id');
        $userId = Auth::id();
        $mahasiswa = Mahasiswa::where('id_user', $userId)->first();

        if (!$mahasiswa) {
            return response()->json(null);
        }

        $pencapaian = Pencapaian::where('id', $pencapaianId)
            ->where('id_mahasiswa', $mahasiswa->id)
            ->first();

        $badge = Pencapaian::where('id', $badgeId)
            ->where('id_mahasiswa', $mahasiswa->id)
            ->first();

        $konversi = Pencapaian::where('id', $konversiId)
            ->where('id_mahasiswa', $mahasiswa->id)
            ->first();


        return response()->json([
            'pencapaian' => $pencapaian ? [
                'id' => $pencapaian->id,
                'name' => $pencapaian->name,
                'desc' => $pencapaian->desc,
                'category' => $pencapaian->category,
            ] : null,
            'badge' => $badge ? [
                'id' => $badge->id,
                'name' => $badge->name,
                'desc' => $badge->desc,
                'category' => $badge->category,
            ] : null,
            'konversi' => $konversi ? [
                'id' => $konversi->id,
                'name' => $konversi->name,
                'desc' => $konversi->desc,
                'category' => $konversi->category,
            ] : null,
        ]);
    }

    public function dataFilterAdmin(Request $request)
    {
        $filterKelas = Kelas::orderBy('name', 'asc')->get(['id', 'name']);

        return response()->json([
            'filter_kelas' => $filterKelas,
        ]);
    }

    public function chartLabeling(Request $request)
    {
        $kelasId = $request->query('kelas_id');
        $allowedLabels = ['Ideal', 'Normal', 'Struggling', 'Gaming the System'];

        $baseQuery = DB::table('label_skor')
            ->join('mahasiswa', 'mahasiswa.id', '=', 'label_skor.id_mahasiswa')
            ->join('kelas', 'kelas.id', '=', 'mahasiswa.id_kelas')
            ->whereIn('label_skor.label', $allowedLabels);

        if (!empty($kelasId)) {
            $baseQuery->where('kelas.id', $kelasId);
        }

        $rows = $baseQuery
            ->select('label_skor.label', DB::raw('COUNT(label_skor.id) as total'))
            ->groupBy('label_skor.label')
            ->get();

        $result = array_fill_keys($allowedLabels, 0);
        foreach ($rows as $row) {
            $result[$row->label] = (int) $row->total;
        }

        return response()->json($result);
    }

    public function chartScoring(Request $request)
    {
        $kelasId = $request->query('kelas_id');
        $allowedScores = [90, 70, 50, 30];

        $baseQuery = DB::table('label_skor')
            ->join('mahasiswa', 'mahasiswa.id', '=', 'label_skor.id_mahasiswa')
            ->join('kelas', 'kelas.id', '=', 'mahasiswa.id_kelas')
            ->whereNotNull('label_skor.id_soal')
            ->whereNotNull('label_skor.label')
            ->whereIn('label_skor.skor', $allowedScores);

        if (!empty($kelasId)) {
            $baseQuery->where('kelas.id', $kelasId);
        }

        $rows = $baseQuery
            ->select('label_skor.skor', DB::raw('COUNT(label_skor.id) as total'))
            ->groupBy('label_skor.skor')
            ->get();

        $result = array_fill_keys($allowedScores, 0);
        foreach ($rows as $row) {
            $result[(int)$row->skor] = (int)$row->total;
        }

        return response()->json($result);
    }

    public function chartConfidence(Request $request)
    {
        $kelasId = $request->query('kelas_id');

        // Jika kelasId diberikan, kembalikan total agregat (logika lama)
        if ($kelasId) {
            $row = HistoryConfidence::query()
                ->join('mahasiswa', 'history_confidence.id_mahasiswa', '=', 'mahasiswa.id')
                ->join('kelas', 'kelas.id', '=', 'mahasiswa.id_kelas')
                ->where('kelas.id', $kelasId)
                ->selectRaw("
                    SUM(CASE WHEN history_confidence.status_jawaban = 1 AND history_confidence.status_confidence = 1 THEN 1 ELSE 0 END) AS benar_yakin,
                    SUM(CASE WHEN history_confidence.status_jawaban = 1 AND history_confidence.status_confidence = 0 THEN 1 ELSE 0 END) AS benar_tidak_yakin,
                    SUM(CASE WHEN history_confidence.status_jawaban = 0 AND history_confidence.status_confidence = 1 THEN 1 ELSE 0 END) AS salah_yakin,
                    SUM(CASE WHEN history_confidence.status_jawaban = 0 AND history_confidence.status_confidence = 0 THEN 1 ELSE 0 END) AS salah_tidak_yakin
                ")
                ->first();

            return response()->json([
                'benar_yakin' => (int) ($row->benar_yakin ?? 0),
                'benar_tidak_yakin' => (int) ($row->benar_tidak_yakin ?? 0),
                'salah_yakin' => (int) ($row->salah_yakin ?? 0),
                'salah_tidak_yakin' => (int) ($row->salah_tidak_yakin ?? 0),
            ]);
        }

        // Jika kelasId null, tampilkan per kelas
        $rows = HistoryConfidence::query()
            ->join('mahasiswa', 'history_confidence.id_mahasiswa', '=', 'mahasiswa.id')
            ->join('kelas', 'kelas.id', '=', 'mahasiswa.id_kelas')
            ->selectRaw("
                kelas.id AS kelas_id,
                kelas.name AS kelas_name,
                SUM(CASE WHEN history_confidence.status_jawaban = 1 AND history_confidence.status_confidence = 1 THEN 1 ELSE 0 END) AS benar_yakin,
                SUM(CASE WHEN history_confidence.status_jawaban = 1 AND history_confidence.status_confidence = 0 THEN 1 ELSE 0 END) AS benar_tidak_yakin,
                SUM(CASE WHEN history_confidence.status_jawaban = 0 AND history_confidence.status_confidence = 1 THEN 1 ELSE 0 END) AS salah_yakin,
                SUM(CASE WHEN history_confidence.status_jawaban = 0 AND history_confidence.status_confidence = 0 THEN 1 ELSE 0 END) AS salah_tidak_yakin
            ")
            ->groupBy('kelas.id', 'kelas.name')
            ->orderBy('kelas.name', 'asc')
            ->get();

        $data = $rows->map(function ($row) {
            return [
                'kelas_id' => $row->kelas_id,
                'kelas_name' => $row->kelas_name,
                'benar_yakin' => (int) $row->benar_yakin,
                'benar_tidak_yakin' => (int) $row->benar_tidak_yakin,
                'salah_yakin' => (int) $row->salah_yakin,
                'salah_tidak_yakin' => (int) $row->salah_tidak_yakin,
            ];
        });

        return response()->json($data);
    }

    public function getDataGuide()
    {
        $guides = Guide::query()
            ->orderBy('order', 'asc')
            ->get(['judul', 'desc', 'img']);

        return response()->json($guides);
    }

    public function chartAktivitasUjian(Request $request)
    {
        $tahun = $request->query('tahun');
        $bulan = $request->query('bulan');
        $kelasId = $request->query('kelas_id');
    
        // Validasi tahun & bulan
        if (!$tahun || !$bulan) {
            return response()->json(['message' => 'Tahun dan bulan wajib diisi'], 422);
        }
    
        // Hitung jumlah hari di bulan tsb
        $startDate = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = (clone $startDate)->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;
    
        // Siapkan array dasar untuk hasil (dipakai juga saat kelas tidak punya mahasiswa)
        $buildEmptyMonth = function () use ($startDate, $daysInMonth) {
            $data = [];
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $tanggal = $startDate->copy()->day($i)->toDateString();
                $data[] = [
                    'tanggal' => $tanggal,
                    'total' => 0,
                ];
            }
            return $data;
        };
    
        // Ambil id_mahasiswa jika ada filter kelas
        $mahasiswaIds = [];
        $filterByKelas = false;
        if ($kelasId !== null && $kelasId !== '') {
            $mahasiswaIds = Mahasiswa::where('id_kelas', $kelasId)->pluck('id')->toArray();
            $filterByKelas = true;
            // Jika kelas dipilih tetapi tidak ada mahasiswa, langsung kembalikan nol semua
            if (count($mahasiswaIds) === 0) {
                return response()->json($buildEmptyMonth());
            }
        }
    
        // Query ujian status=1 pada bulan & tahun tsb
        $query = Ujian::query()
            ->where('status', 1)
            ->whereBetween('created_at', [$startDate->toDateString() . ' 00:00:00', $endDate->toDateString() . ' 23:59:59']);
    
        // Terapkan filter kelas (termasuk jika menghasilkan nol baris)
        if ($filterByKelas) {
            $query->whereIn('id_mahasiswa', $mahasiswaIds);
        }
    
        // Group by tanggal (YYYY-MM-DD)
        $results = $query->selectRaw('DATE(created_at) as tanggal, COUNT(*) as total')
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->pluck('total', 'tanggal')
            ->toArray();

        // Siapkan array hasil untuk semua hari di bulan tsb
        $data = [];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $tanggal = $startDate->copy()->day($i)->toDateString();
            $data[] = [
                'tanggal' => $tanggal,
                'total' => isset($results[$tanggal]) ? (int)$results[$tanggal] : 0,
            ];
        }
    
        return response()->json($data);
    }

    public function mahasiswaOnline(Request $request)
    {
        $minutes = (int) $request->query('minutes', 5);
        if ($minutes < 1 || $minutes > 120) {
            $minutes = 5;
        }

        $thresholdTs = now()->subMinutes($minutes)->timestamp;

        // Subquery ambil last_activity terbaru per user
        $sub = DB::table('sessions')
            ->select('user_id', DB::raw('MAX(last_activity) as last_activity'))
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', $thresholdTs)
            ->groupBy('user_id');

        $rows = DB::query()
            ->fromSub($sub, 's')
            ->join('users', 'users.id', '=', 's.user_id')
            ->join('mahasiswa', 'mahasiswa.id_user', '=', 'users.id')
            ->leftJoin('kelas', 'kelas.id', '=', 'mahasiswa.id_kelas')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'kelas.name as kelas',
                's.last_activity'
            )
            ->orderByDesc('s.last_activity')
            ->get()
            ->map(function ($r) {
                $dt = Carbon::createFromTimestamp($r->last_activity);
                return [
                    'id' => $r->id,
                    'name' => $r->name,
                    'email' => $r->email,
                    'kelas' => $r->kelas ?? '-',
                    'last_activity_at' => $dt->toDateTimeString(),
                    'last_activity_diff' => $dt->diffForHumans(),
                    'last_activity_unix' => $r->last_activity,
                ];
            })
            ->values();

        return response()->json([
            'minutes_window' => $minutes,
            'count' => $rows->count(),
            'data' => $rows
        ]);
    }

    public function toggleMaintenance(Request $request)
    {
        try {
            // Validasi: pastikan hanya admin yang boleh mengubah
            if (!Auth::check() || !Auth::user()->is_admin) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }

            // Ambil status dari request (true/false)
            $status = $request->boolean('status') ? '1' : '0';

            // Simpan ke tabel settings
            Setting::setValue('maintenance_mahasiswa', $status);

            // Kembalikan response sukses
            return response()->json([
                'success' => true,
                'message' => $status === '1'
                    ? 'Mode Maintenance Mahasiswa diaktifkan.'
                    : 'Mode Maintenance Mahasiswa dinonaktifkan.',
                'status' => $status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui setting.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

