<?php

namespace App\Http\Controllers\LogDataChatbot;

use App\Exports\LogDataChatbotExport;
use App\Http\Controllers\Controller;
use App\Models\ChatbotAccessLog;
use App\Models\ChatbotLog;
use App\Models\Kelas;
use App\Models\Level;
use App\Models\Mahasiswa;
use App\Models\Soal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LogDataChatbot extends Controller
{
    protected $levelModel;
    protected $soalModel;
    protected $kelasModel;
    protected $mahasiswaModel;

    public function __construct()
    {
        $this->levelModel = new Level();
        $this->soalModel = new Soal();
        $this->kelasModel = new Kelas();
        $this->mahasiswaModel = new Mahasiswa();
    }

    /**
     * Display the log data chatbot page
     */
    public function index()
    {
        $list_kelas = $this->kelasModel->get(['id', 'name', 'angkatan'])->toArray();

        $list_kelas = collect($list_kelas)->prepend(['id' => '', 'name' => 'Semua Kelas', 'angkatan' => '']);

        $list_kelas = collect($list_kelas)->map(function ($item) {
            return [
                'id' => $item['id'],
                'name' => $item['name'],
                'angkatan' => $item['angkatan'],
            ];
        })->values()->toArray();

        $list_level = $this->levelModel->orderBy('order', 'asc')->get(['id', 'name'])
            ->pluck('name', 'id')
            ->toArray();

        $list_level = collect($list_level)->map(function ($name, $id) {
            return [
                'id' => $id,
                'name' => $name,
            ];
        })->values()->toArray();

        return view('pages.logDataChatbot.index', [
            'title' => 'Log Data Chatbot',
            'list_kelas' => $list_kelas,
            'list_level' => $list_level,
        ]);
    }

    /**
     * Get table data for DataTables
     */
    public function table(Request $request)
    {
        $kelas = $request->input('kelas');
        $level = $request->input('level');
        $soal = $request->input('soal');
        $search = $request->input('search')['value'] ?? '';

        $query = $this->mahasiswaModel->setView('v_mahasiswa');

        if (!empty($kelas)) {
            $query = $query->where('id_kelas', $kelas);
        }

        if (!empty($search)) {
            $query = $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $totalRecords = (clone $query)->count();

        $filteredQuery = clone $query;

        if (!empty($level) || !empty($soal)) {
            $relevantIdsQuery = ChatbotLog::query();

            if (!empty($level)) {
                $relevantIdsQuery->where('id_level', $level);
            }

            if (!empty($soal)) {
                $relevantIdsQuery->where('id_soal', $soal);
            }

            $relevantIds = $relevantIdsQuery->distinct()->pluck('id_mahasiswa');
            $filteredQuery = $filteredQuery->whereIn('id', $relevantIds);
        }

        $filteredRecords = (clone $filteredQuery)->count();

        $start = $request->input('start', 0);
        $length = $request->input('length', 10);

        $mahasiswas = $filteredQuery->skip($start)->take($length)->get();
        $mahasiswaIds = $mahasiswas->pluck('id');
        $latestLevelByMahasiswa = [];

        $latestChatbotLogs = ChatbotLog::whereIn('id_mahasiswa', $mahasiswaIds)
            ->with('level:id,name')
            ->when(!empty($level), function ($q) use ($level) {
                $q->where('id_level', $level);
            })
            ->when(!empty($soal), function ($q) use ($soal) {
                $q->where('id_soal', $soal);
            })
            ->orderBy('created_at', 'desc')
            ->get(['id_mahasiswa', 'id_level', 'created_at']);

        foreach ($latestChatbotLogs as $log) {
            if (!isset($latestLevelByMahasiswa[$log->id_mahasiswa])) {
                $latestLevelByMahasiswa[$log->id_mahasiswa] = $log->level?->name ?? '-';
            }
        }

        $countBiasa = ChatbotAccessLog::whereIn('id_mahasiswa', $mahasiswaIds)
            ->where('type', 'biasa')
            ->selectRaw('id_mahasiswa, count(*) as total')
            ->groupBy('id_mahasiswa')
            ->pluck('total', 'id_mahasiswa');

        $countAdaptive = ChatbotAccessLog::whereIn('id_mahasiswa', $mahasiswaIds)
            ->where('type', 'adaptive')
            ->selectRaw('id_mahasiswa, count(*) as total')
            ->groupBy('id_mahasiswa')
            ->pluck('total', 'id_mahasiswa');

        $data = $mahasiswas->map(function ($mahasiswa) use ($countBiasa, $countAdaptive, $latestLevelByMahasiswa) {
            return [
                'id' => $mahasiswa->id,
                'nim' => $mahasiswa->nim,
                'name' => $mahasiswa->name,
                'kelas_name' => $mahasiswa->kelas_name ?? '-',
                'level_name' => $latestLevelByMahasiswa[$mahasiswa->id] ?? '-',
                'jumlah_chatbot' => $countBiasa[$mahasiswa->id] ?? 0,
                'jumlah_chatbot_adaptive' => $countAdaptive[$mahasiswa->id] ?? 0,
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

    /**
     * Get soal by level for filter dropdown
     */
    public function getSoalByLevel(Request $request)
    {
        $levelId = $request->input('level_id');

        if (empty($levelId)) {
            return response()->json([]);
        }

        $soals = $this->soalModel->where('id_level', $levelId)
            ->orderBy('order', 'asc')
            ->get(['id', 'judul']);

        return response()->json($soals);
    }

    /**
     * Get detail chatbot log for a student
     */
    public function detail($id)
    {
        $mahasiswa = $this->mahasiswaModel->setView('v_mahasiswa')->find($id);

        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan',
            ]);
        }

        $accessLogs = ChatbotAccessLog::where('id_mahasiswa', $id)
            ->orderBy('opened_at', 'desc')
            ->get();

        $jumlahBiasa = $accessLogs->where('type', 'biasa')->count();
        $jumlahAdaptive = $accessLogs->where('type', 'adaptive')->count();

        $context = $this->resolveMahasiswaContext($id, $accessLogs);

        $levelName = !empty($context['id_level'])
            ? (Level::where('id', $context['id_level'])->value('name') ?: 'Tidak tercatat')
            : 'Tidak tercatat';

        $soalName = !empty($context['id_soal'])
            ? (Soal::where('id', $context['id_soal'])->value('judul') ?: 'Tidak tercatat')
            : 'Tidak tercatat';

        $fallbackContext = $this->getFallbackContextForAccessIds(
            $accessLogs->pluck('id')->filter()->values()->toArray()
        );

        $singleSoalByLevelCache = [];
        $effectiveContextByAccessId = [];

        foreach ($accessLogs as $log) {
            $ctx = $fallbackContext[$log->id] ?? ['id_level' => null, 'id_soal' => null];

            if (empty($ctx['id_level']) || empty($ctx['id_soal'])) {
                $historicalCtx = $this->resolveHistoricalContext(
                    $id,
                    $log->opened_at,
                    $log->closed_at,
                    $singleSoalByLevelCache
                );

                if (empty($ctx['id_level']) && !empty($historicalCtx['id_level'])) {
                    $ctx['id_level'] = $historicalCtx['id_level'];
                }

                if (empty($ctx['id_soal']) && !empty($historicalCtx['id_soal'])) {
                    $ctx['id_soal'] = $historicalCtx['id_soal'];
                }
            }

            $effectiveLevelId = $log->id_level ?: $ctx['id_level'];
            $effectiveSoalId = $log->id_soal ?: $ctx['id_soal'];

            if (empty($effectiveLevelId) && !empty($effectiveSoalId)) {
                $effectiveLevelId = Soal::where('id', $effectiveSoalId)->value('id_level');
            }

            if (empty($effectiveSoalId) && !empty($effectiveLevelId)) {
                if (!array_key_exists($effectiveLevelId, $singleSoalByLevelCache)) {
                    $soalIds = Soal::where('id_level', $effectiveLevelId)->pluck('id');
                    $singleSoalByLevelCache[$effectiveLevelId] = $soalIds->count() === 1
                        ? $soalIds->first()
                        : null;
                }

                $effectiveSoalId = $singleSoalByLevelCache[$effectiveLevelId];
            }

            $effectiveContextByAccessId[$log->id] = [
                'id_level' => $effectiveLevelId,
                'id_soal' => $effectiveSoalId,
            ];
        }

        $levelIds = collect($effectiveContextByAccessId)->pluck('id_level')->filter()->unique()->values()->toArray();
        $soalIds = collect($effectiveContextByAccessId)->pluck('id_soal')->filter()->unique()->values()->toArray();

        $levelMap = !empty($levelIds)
            ? Level::whereIn('id', $levelIds)->pluck('name', 'id')->toArray()
            : [];

        $soalMap = !empty($soalIds)
            ? Soal::whereIn('id', $soalIds)->pluck('judul', 'id')->toArray()
            : [];

        $history = $accessLogs->map(function ($log) use ($effectiveContextByAccessId, $levelMap, $soalMap) {
            $durasiText = '-';
            if (!is_null($log->durasi_menit)) {
                if ($log->durasi_menit > 0) {
                    $durasiText = $log->durasi_menit . ' menit';
                } else {
                    $detik = $log->opened_at && $log->closed_at
                        ? abs($log->opened_at->diffInSeconds($log->closed_at))
                        : 0;
                    $durasiText = '0 menit ' . $detik . ' detik';
                }
            }

            $ctx = $effectiveContextByAccessId[$log->id] ?? ['id_level' => null, 'id_soal' => null];

            $historyLevel = !empty($ctx['id_level'])
                ? ($levelMap[$ctx['id_level']] ?? 'Tidak tercatat')
                : 'Tidak tercatat';

            $historySoal = !empty($ctx['id_soal'])
                ? ($soalMap[$ctx['id_soal']] ?? 'Tidak tercatat')
                : 'Tidak tercatat';

            return [
                'type' => $log->type,
                'level' => $historyLevel,
                'waktu_akses' => $log->opened_at ? $log->opened_at->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s') . ' WIB' : '-',
                'jenis_soal' => $historySoal,
                'durasi' => $durasiText,
            ];
        })->values()->toArray();

        $data = [
            'id' => $mahasiswa->id,
            'nim' => $mahasiswa->nim,
            'name' => $mahasiswa->name,
            'kelas_name' => $mahasiswa->kelas_name ?? '-',
            'level_name' => $levelName,
            'jumlah_chatbot' => $jumlahBiasa,
            'jumlah_chatbot_adaptive' => $jumlahAdaptive,
            'level' => $levelName,
            'jenis_soal' => $soalName,
            'history' => $history,
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    private function resolveMahasiswaContext(string $idMahasiswa, $accessLogs): array
    {
        $singleSoalByLevelCache = [];

        $candidateAccessLogs = $accessLogs->where('type', 'biasa')->values();
        if ($candidateAccessLogs->isEmpty()) {
            $candidateAccessLogs = $accessLogs->values();
        }

        $accessIds = $candidateAccessLogs->pluck('id')->filter()->values()->toArray();
        $fallbackContext = $this->getFallbackContextForAccessIds($accessIds);

        foreach ($candidateAccessLogs as $accessLog) {
            $ctx = $fallbackContext[$accessLog->id] ?? ['id_level' => null, 'id_soal' => null];

            if (empty($ctx['id_level']) || empty($ctx['id_soal'])) {
                $historicalCtx = $this->resolveHistoricalContext(
                    $idMahasiswa,
                    $accessLog->opened_at,
                    $accessLog->closed_at,
                    $singleSoalByLevelCache
                );

                if (empty($ctx['id_level']) && !empty($historicalCtx['id_level'])) {
                    $ctx['id_level'] = $historicalCtx['id_level'];
                }

                if (empty($ctx['id_soal']) && !empty($historicalCtx['id_soal'])) {
                    $ctx['id_soal'] = $historicalCtx['id_soal'];
                }
            }

            $effectiveLevelId = $accessLog->id_level ?: $ctx['id_level'];
            $effectiveSoalId = $accessLog->id_soal ?: $ctx['id_soal'];

            if (empty($effectiveLevelId) && !empty($effectiveSoalId)) {
                $effectiveLevelId = Soal::where('id', $effectiveSoalId)->value('id_level');
            }

            if (empty($effectiveSoalId) && !empty($effectiveLevelId)) {
                if (!array_key_exists($effectiveLevelId, $singleSoalByLevelCache)) {
                    $soalIds = Soal::where('id_level', $effectiveLevelId)->pluck('id');
                    $singleSoalByLevelCache[$effectiveLevelId] = $soalIds->count() === 1
                        ? $soalIds->first()
                        : null;
                }

                $effectiveSoalId = $singleSoalByLevelCache[$effectiveLevelId];
            }

            if (!empty($effectiveLevelId) || !empty($effectiveSoalId)) {
                return [
                    'id_level' => $effectiveLevelId,
                    'id_soal' => $effectiveSoalId,
                ];
            }
        }

        $latestLog = ChatbotLog::where('id_mahasiswa', $idMahasiswa)
            ->where('type', 'biasa')
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->first(['id_level', 'id_soal']);

        $resolvedLevel = $latestLog->id_level ?? null;
        $resolvedSoal = $latestLog->id_soal ?? null;

        if (empty($resolvedLevel) && !empty($resolvedSoal)) {
            $resolvedLevel = Soal::where('id', $resolvedSoal)->value('id_level');
        }

        if (empty($resolvedSoal) && !empty($resolvedLevel)) {
            if (!array_key_exists($resolvedLevel, $singleSoalByLevelCache)) {
                $soalIds = Soal::where('id_level', $resolvedLevel)->pluck('id');
                $singleSoalByLevelCache[$resolvedLevel] = $soalIds->count() === 1
                    ? $soalIds->first()
                    : null;
            }

            $resolvedSoal = $singleSoalByLevelCache[$resolvedLevel];
        }

        return [
            'id_level' => $resolvedLevel,
            'id_soal' => $resolvedSoal,
        ];
    }

    private function getFallbackContextForAccessIds(array $accessIds): array
    {
        if (empty($accessIds)) {
            return [];
        }

        $logs = ChatbotLog::whereIn('access_id', $accessIds)
            ->where('type', 'biasa')
            ->whereNotNull('access_id')
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->get(['access_id', 'id_level', 'id_soal']);

        $result = [];

        foreach ($logs->groupBy('access_id') as $accessId => $group) {
            $resolvedLevel = optional($group->first(function ($item) {
                return !empty($item->id_level);
            }))->id_level;

            $resolvedSoal = optional($group->first(function ($item) {
                return !empty($item->id_soal);
            }))->id_soal;

            $result[$accessId] = [
                'id_level' => $resolvedLevel,
                'id_soal' => $resolvedSoal,
            ];
        }

        return $result;
    }

    private function resolveHistoricalContext(string $idMahasiswa, $openedAt, $closedAt, array &$singleSoalByLevelCache = []): array
    {
        $q = ChatbotLog::where('id_mahasiswa', $idMahasiswa)
            ->where('type', 'biasa')
            ->whereNull('deleted_at');

        $openedAtCarbon = $openedAt ? Carbon::parse($openedAt) : null;
        $closedAtCarbon = $closedAt ? Carbon::parse($closedAt) : null;

        if ($openedAtCarbon && $closedAtCarbon) {
            $q->whereBetween('created_at', [$openedAtCarbon, $closedAtCarbon]);
        } elseif ($openedAtCarbon) {
            $q->where('created_at', '>=', $openedAtCarbon)
                ->where('created_at', '<=', $openedAtCarbon->copy()->addMinutes(10));
        }

        $latestLog = (clone $q)
            ->orderBy('created_at', 'desc')
            ->first(['id_level', 'id_soal']);

        $latestLogWithLevel = (clone $q)
            ->whereNotNull('id_level')
            ->orderBy('created_at', 'desc')
            ->first(['id_level']);

        $latestLogWithSoal = (clone $q)
            ->whereNotNull('id_soal')
            ->orderBy('created_at', 'desc')
            ->first(['id_soal']);

        $resolvedLevel = $latestLogWithLevel->id_level
            ?? $latestLog->id_level
            ?? null;

        $resolvedSoal = $latestLogWithSoal->id_soal
            ?? $latestLog->id_soal
            ?? null;

        if (empty($resolvedLevel) && !empty($resolvedSoal)) {
            $resolvedLevel = Soal::where('id', $resolvedSoal)->value('id_level');
        }

        if (empty($resolvedSoal) && !empty($resolvedLevel)) {
            $referenceTime = $openedAtCarbon ?: Carbon::now();

            $nearestBefore = ChatbotLog::where('id_mahasiswa', $idMahasiswa)
                ->where('type', 'biasa')
                ->where('id_level', $resolvedLevel)
                ->whereNotNull('id_soal')
                ->whereNull('deleted_at')
                ->where('created_at', '<=', $referenceTime)
                ->orderBy('created_at', 'desc')
                ->first(['id_soal', 'created_at']);

            $nearestAfter = ChatbotLog::where('id_mahasiswa', $idMahasiswa)
                ->where('type', 'biasa')
                ->where('id_level', $resolvedLevel)
                ->whereNotNull('id_soal')
                ->whereNull('deleted_at')
                ->where('created_at', '>=', $referenceTime)
                ->orderBy('created_at', 'asc')
                ->first(['id_soal', 'created_at']);

            if ($nearestBefore && $nearestAfter) {
                $diffBefore = Carbon::parse($nearestBefore->created_at)->diffInSeconds($referenceTime);
                $diffAfter = Carbon::parse($nearestAfter->created_at)->diffInSeconds($referenceTime);
                $resolvedSoal = $diffBefore <= $diffAfter
                    ? $nearestBefore->id_soal
                    : $nearestAfter->id_soal;
            } elseif ($nearestBefore) {
                $resolvedSoal = $nearestBefore->id_soal;
            } elseif ($nearestAfter) {
                $resolvedSoal = $nearestAfter->id_soal;
            }
        }

        if (empty($resolvedSoal) && !empty($resolvedLevel)) {
            if (!array_key_exists($resolvedLevel, $singleSoalByLevelCache)) {
                $soalIds = Soal::where('id_level', $resolvedLevel)->pluck('id');
                $singleSoalByLevelCache[$resolvedLevel] = $soalIds->count() === 1
                    ? $soalIds->first()
                    : null;
            }

            $resolvedSoal = $singleSoalByLevelCache[$resolvedLevel];
        }

        return [
            'id_level' => $resolvedLevel,
            'id_soal' => $resolvedSoal,
        ];
    }

    /**
     * Export data to Excel
     */
    public function export(Request $request)
    {
        $idKelas = $request->input('kelas');
        $idLevel = $request->input('level');
        $idSoal = $request->input('soal');

        $filename = 'Log_Data_Chatbot_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new LogDataChatbotExport($idKelas, $idLevel, $idSoal), $filename);
    }
}
