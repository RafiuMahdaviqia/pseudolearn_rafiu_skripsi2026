<?php

namespace App\Http\Controllers\LogChatbot;

use App\Exports\LogChatbotExport;
use App\Http\Controllers\Controller;
use App\Models\ChatbotAccessLog;
use App\Models\ChatbotLog;
use App\Models\Kelas;
use App\Models\LabelSkor;
use App\Models\Level;
use App\Models\LogData;
use App\Models\Mahasiswa;
use App\Models\Soal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LogChatbotController extends Controller
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

        return view('pages.logChatbot.index', [
            'title' => 'Log Chatbot',
            'list_kelas' => $list_kelas,
            'list_level' => $list_level,
        ]);
    }

    public function table(Request $request)
    {
        $kelas = $request->input('kelas');
        $searchQ = trim((string) $request->input('q', ''));
        $searchValue = trim((string) $request->input('search.value', ''));
        $searchTerm = trim((string) $request->input('searchTerm', ''));
        $search = $searchQ !== ''
            ? $searchQ
            : ($searchValue !== '' ? $searchValue : $searchTerm);

        $buildMahasiswaQuery = function () use ($kelas) {
            $query = $this->mahasiswaModel->setView('v_mahasiswa')->newQuery();

            if (!empty($kelas)) {
                $query->where('id_kelas', $kelas);
            }

            return $query;
        };

        $totalRecords = $buildMahasiswaQuery()->count();

        $filteredQuery = $buildMahasiswaQuery();
        if ($search !== '') {
            $filteredQuery->where(function ($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('kelas_name', 'like', "%{$search}%");
            });
        }

        $filteredRecords = (clone $filteredQuery)->count();

        $start = max((int) $request->input('start', 0), 0);
        $length = 20;

        $filteredQuery->orderBy('name', 'asc');
        if ($length >= 0) {
            $filteredQuery->skip($start)->take($length);
        } elseif ($start > 0) {
            $filteredQuery->skip($start);
        }

        $mahasiswas = $filteredQuery->get(['id', 'nim', 'name', 'kelas_name']);
        $mahasiswaIds = $mahasiswas->pluck('id')->filter()->values()->toArray();

        $accessLogs = !empty($mahasiswaIds)
            ? ChatbotAccessLog::query()
                ->whereIn('id_mahasiswa', $mahasiswaIds)
                ->where('type', 'biasa')
                ->get(['id_mahasiswa', 'opened_at', 'closed_at', 'durasi_menit'])
            : collect();

        $logsByMahasiswa = $accessLogs->groupBy('id_mahasiswa');

        $langkahByMahasiswa = !empty($mahasiswaIds)
            ? (new LogData())->setView('v_log_data')
                ->whereIn('id_mahasiswa', $mahasiswaIds)
                ->selectRaw('id_mahasiswa, COUNT(*) as total')
                ->groupBy('id_mahasiswa')
                ->pluck('total', 'id_mahasiswa')
                ->toArray()
            : [];

        $data = $mahasiswas->map(function ($mahasiswa) use ($logsByMahasiswa, $langkahByMahasiswa) {
            $logs = $logsByMahasiswa->get($mahasiswa->id, collect());
            $totalDurasiDetik = 0;

            foreach ($logs as $log) {
                $openedAt = !empty($log->opened_at) ? Carbon::parse($log->opened_at) : null;
                $closedAt = !empty($log->closed_at) ? Carbon::parse($log->closed_at) : null;
                $totalDurasiDetik += $this->hitungDurasiDetik($log->durasi_menit, $openedAt, $closedAt);
            }

            $hasLog = $logs->isNotEmpty();

            return [
                'id' => $mahasiswa->id,
                'nim' => $mahasiswa->nim,
                'nama_mahasiswa' => $mahasiswa->name,
                'kelas_name' => $mahasiswa->kelas_name ?? '-',
                'total_waktu' => $hasLog ? $this->formatDurasiDetik($totalDurasiDetik) : '-',
                'total_langkah' => (int) ($langkahByMahasiswa[$mahasiswa->id] ?? 0),
                'total_durasi' => $hasLog ? $this->formatDurasiDetik($totalDurasiDetik) : '-',
            ];
        })->values()->all();

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

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

    public function detail(Request $request, $id)
    {
        $levelFilter = $request->input('level');
        $soalFilter = $request->input('soal');

        $mahasiswa = $this->mahasiswaModel->setView('v_mahasiswa')->find($id);

        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan',
            ]);
        }

        $rows = ChatbotAccessLog::query()
            ->where('chatbot_access_logs.id_mahasiswa', $id)
            ->where('chatbot_access_logs.type', 'biasa')
            ->orderBy('chatbot_access_logs.opened_at', 'desc')
            ->get([
                'chatbot_access_logs.id as access_id',
                'chatbot_access_logs.id_mahasiswa',
                'chatbot_access_logs.id_level',
                'chatbot_access_logs.id_soal',
                'chatbot_access_logs.opened_at',
                'chatbot_access_logs.closed_at',
                'chatbot_access_logs.durasi_menit',
            ]);

        if ($rows->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $mahasiswa->id,
                    'nim' => $mahasiswa->nim,
                    'name' => $mahasiswa->name,
                    'kelas_name' => $mahasiswa->kelas_name ?? '-',
                    'total_akses' => 0,
                    'waktu_akses' => '-',
                    'level' => 'Tidak tercatat',
                    'jenis_soal' => 'Tidak tercatat',
                    'durasi' => '0 menit',
                    'total_pesan' => 0,
                    'history' => [],
                ],
            ]);
        }

        $effectiveContextByAccessId = $this->resolveEffectiveContextForRows($rows);
        $sessions = $this->filterRowsByContext($rows, $effectiveContextByAccessId, $levelFilter, $soalFilter);

        if (empty($sessions)) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $mahasiswa->id,
                    'nim' => $mahasiswa->nim,
                    'name' => $mahasiswa->name,
                    'kelas_name' => $mahasiswa->kelas_name ?? '-',
                    'total_akses' => 0,
                    'waktu_akses' => '-',
                    'level' => 'Tidak tercatat',
                    'jenis_soal' => 'Tidak tercatat',
                    'durasi' => '0 menit',
                    'total_pesan' => 0,
                    'history' => [],
                ],
            ]);
        }

        usort($sessions, function ($a, $b) {
            return ($b['opened_at'] ? $b['opened_at']->getTimestamp() : 0)
                <=> ($a['opened_at'] ? $a['opened_at']->getTimestamp() : 0);
        });

        $contextTriples = [];
        foreach ($sessions as $session) {
            if (!empty($session['id_level']) && !empty($session['id_soal'])) {
                $contextKey = $session['id_mahasiswa'] . '|' . $session['id_level'] . '|' . $session['id_soal'];
                $contextTriples[$contextKey] = [
                    'id_mahasiswa' => $session['id_mahasiswa'],
                    'id_level' => $session['id_level'],
                    'id_soal' => $session['id_soal'],
                ];
            }
        }

        $jumlahLangkahByContext = $this->buildJumlahLangkahByContext($contextTriples);

        $levelIds = array_values(array_unique(array_filter(array_map(function ($session) {
            return $session['id_level'];
        }, $sessions))));

        $soalIds = array_values(array_unique(array_filter(array_map(function ($session) {
            return $session['id_soal'];
        }, $sessions))));

        $levelMap = !empty($levelIds)
            ? Level::whereIn('id', $levelIds)->pluck('name', 'id')->toArray()
            : [];

        $soalMap = !empty($soalIds)
            ? Soal::whereIn('id', $soalIds)->pluck('judul', 'id')->toArray()
            : [];

        $labelMap = [];
        if (!empty($levelIds) && !empty($soalIds)) {
            $labelRows = LabelSkor::query()
                ->where('id_mahasiswa', $id)
                ->whereIn('id_level', $levelIds)
                ->whereIn('id_soal', $soalIds)
                ->orderBy('updated_at', 'desc')
                ->get(['id_level', 'id_soal', 'label']);

            foreach ($labelRows as $labelRow) {
                $labelKey = $labelRow->id_level . '|' . $labelRow->id_soal;
                if (!array_key_exists($labelKey, $labelMap)) {
                    $labelMap[$labelKey] = $labelRow->label ?: 'Tidak tercatat';
                }
            }
        }

        $accessIds = array_values(array_unique(array_map(function ($session) {
            return $session['access_id'];
        }, $sessions)));

        $totalPesanByAccess = !empty($accessIds)
            ? ChatbotLog::query()
                ->whereIn('access_id', $accessIds)
                ->where('type', 'biasa')
                ->whereNull('deleted_at')
                ->whereNotNull('respons')
                ->where('respons', '<>', '')
                ->selectRaw('access_id, COUNT(*) as total')
                ->groupBy('access_id')
                ->pluck('total', 'access_id')
                ->toArray()
            : [];

        $history = [];
        $totalDurasiDetik = 0;
        $totalPesan = 0;
        $minOpenedAt = null;
        $maxOpenedAt = null;
        $historyLevels = [];
        $historySoals = [];

        foreach ($sessions as $index => $session) {
            $contextKey = (!empty($session['id_level']) && !empty($session['id_soal']))
                ? $session['id_mahasiswa'] . '|' . $session['id_level'] . '|' . $session['id_soal']
                : null;

            $labelKey = (!empty($session['id_level']) && !empty($session['id_soal']))
                ? $session['id_level'] . '|' . $session['id_soal']
                : null;

            $levelName = !empty($session['id_level'])
                ? ($levelMap[$session['id_level']] ?? 'Tidak tercatat')
                : 'Tidak tercatat';

            $soalName = !empty($session['id_soal'])
                ? ($soalMap[$session['id_soal']] ?? 'Tidak tercatat')
                : 'Tidak tercatat';

            $sessionTotalPesan = (int) ($totalPesanByAccess[$session['access_id']] ?? 0);

            $history[] = [
                'no' => $index + 1,
                'level' => $levelName,
                'soal' => $soalName,
                'total_waktu_pengerjaan' => $this->formatDurasiDetik($session['durasi_detik']),
                'durasi_popup' => $this->formatDurasi($session['durasi_menit'], $session['opened_at'], $session['closed_at']),
                'jumlah_langkah' => $contextKey ? ($jumlahLangkahByContext[$contextKey] ?? 0) : 0,
                'labelling' => $labelKey ? ($labelMap[$labelKey] ?? 'Tidak tercatat') : 'Tidak tercatat',
                'total_pesan' => $sessionTotalPesan,
                'detail_pesan_access_id' => $session['access_id'],
            ];

            $totalDurasiDetik += $session['durasi_detik'];
            $totalPesan += $sessionTotalPesan;

            if ($session['opened_at']) {
                if (is_null($minOpenedAt) || $session['opened_at']->lt($minOpenedAt)) {
                    $minOpenedAt = $session['opened_at'];
                }

                if (is_null($maxOpenedAt) || $session['opened_at']->gt($maxOpenedAt)) {
                    $maxOpenedAt = $session['opened_at'];
                }
            }

            $historyLevels[] = $levelName;
            $historySoals[] = $soalName;
        }

        $data = [
            'id' => $mahasiswa->id,
            'nim' => $mahasiswa->nim,
            'name' => $mahasiswa->name,
            'kelas_name' => $mahasiswa->kelas_name ?? '-',
            'total_akses' => count($sessions),
            'waktu_akses' => $this->formatWaktuRange($minOpenedAt, $maxOpenedAt),
            'level' => $this->summarizeUniqueNames($historyLevels),
            'jenis_soal' => $this->summarizeUniqueNames($historySoals),
            'durasi' => $this->formatDurasiDetik($totalDurasiDetik),
            'total_pesan' => $totalPesan,
            'history' => $history,
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function detailPesan($id)
    {
        $accessLog = ChatbotAccessLog::query()
            ->where('id', $id)
            ->where('type', 'biasa')
            ->first();

        if (!$accessLog) {
            return response()->json([
                'success' => false,
                'message' => 'Data sesi chatbot tidak ditemukan',
            ]);
        }

        $sessionLogs = $this->getSessionLogsByAccess($accessLog);

        $history = $sessionLogs->map(function ($item) {
            return [
                'pesan' => $item->pesan ?: '-',
                'respons' => $item->respons ?: '-',
                'waktu' => $item->created_at
                    ? $item->created_at->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s') . ' WIB'
                    : '-',
            ];
        })->values()->toArray();

        $totalPesan = count(array_filter($history, function ($item) {
            return trim((string) ($item['respons'] ?? '')) !== '-' && trim((string) ($item['respons'] ?? '')) !== '';
        }));

        return response()->json([
            'success' => true,
            'data' => [
                'access_id' => $accessLog->id,
                'waktu_akses' => $this->formatWib($accessLog->opened_at),
                'durasi' => $this->formatDurasi($accessLog->durasi_menit, $accessLog->opened_at, $accessLog->closed_at),
                'total_pesan' => $totalPesan,
                'history' => $history,
            ],
        ]);
    }

    private function buildMahasiswaAggregates($kelas, $level, $soal, string $search = ''): array
    {
        $rows = $this->getSessionRows($kelas, $search);

        if ($rows->isEmpty()) {
            return [];
        }

        $effectiveContextByAccessId = $this->resolveEffectiveContextForRows($rows);
        $sessions = $this->filterRowsByContext($rows, $effectiveContextByAccessId, $level, $soal);

        if (empty($sessions)) {
            return [];
        }

        $grouped = [];
        $contextTriples = [];

        foreach ($sessions as $session) {
            $mahasiswaId = $session['id_mahasiswa'];

            if (!isset($grouped[$mahasiswaId])) {
                $grouped[$mahasiswaId] = [
                    'id' => $mahasiswaId,
                    'nim' => $session['nim'] ?: '-',
                    'nama_mahasiswa' => $session['name'] ?: '-',
                    'kelas_name' => $session['kelas_name'] ?: '-',
                    'min_opened_at' => $session['opened_at'],
                    'max_opened_at' => $session['opened_at'],
                    'total_durasi_detik' => 0,
                    'context_keys' => [],
                ];
            }

            if ($session['opened_at']) {
                if (is_null($grouped[$mahasiswaId]['min_opened_at']) || $session['opened_at']->lt($grouped[$mahasiswaId]['min_opened_at'])) {
                    $grouped[$mahasiswaId]['min_opened_at'] = $session['opened_at'];
                }

                if (is_null($grouped[$mahasiswaId]['max_opened_at']) || $session['opened_at']->gt($grouped[$mahasiswaId]['max_opened_at'])) {
                    $grouped[$mahasiswaId]['max_opened_at'] = $session['opened_at'];
                }
            }

            $grouped[$mahasiswaId]['total_durasi_detik'] += $session['durasi_detik'];

            if (!empty($session['id_level']) && !empty($session['id_soal'])) {
                $contextKey = $session['id_mahasiswa'] . '|' . $session['id_level'] . '|' . $session['id_soal'];
                $grouped[$mahasiswaId]['context_keys'][$contextKey] = true;
                $contextTriples[$contextKey] = [
                    'id_mahasiswa' => $session['id_mahasiswa'],
                    'id_level' => $session['id_level'],
                    'id_soal' => $session['id_soal'],
                ];
            }
        }

        $jumlahLangkahByContext = $this->buildJumlahLangkahByContext($contextTriples);

        $result = [];
        foreach ($grouped as $row) {
            $totalLangkah = 0;
            foreach (array_keys($row['context_keys']) as $contextKey) {
                $totalLangkah += (int) ($jumlahLangkahByContext[$contextKey] ?? 0);
            }

            $latestAccessTs = $row['max_opened_at'] ? $row['max_opened_at']->getTimestamp() : 0;

            $result[] = [
                'id' => $row['id'],
                'nim' => $row['nim'],
                'nama_mahasiswa' => $row['nama_mahasiswa'],
                'kelas_name' => $row['kelas_name'],
                'total_waktu' => $this->formatWaktuRange($row['min_opened_at'], $row['max_opened_at']),
                'total_langkah' => $totalLangkah,
                'total_durasi' => $this->formatDurasiDetik($row['total_durasi_detik']),
                'latest_access_ts' => $latestAccessTs,
            ];
        }

        return $result;
    }

    private function getSessionRows($kelas, string $search = '')
    {
        $query = ChatbotAccessLog::query()
            ->join('mahasiswa as m', function ($join) {
                $join->on('m.id', '=', 'chatbot_access_logs.id_mahasiswa')
                    ->whereNull('m.deleted_at');
            })
            ->leftJoin('kelas as k', function ($join) {
                $join->on('k.id', '=', 'm.id_kelas')
                    ->whereNull('k.deleted_at');
            })
            ->where('chatbot_access_logs.type', 'biasa');

        if (!empty($kelas)) {
            $query->where('m.id_kelas', $kelas);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('m.nim', 'like', "%{$search}%")
                    ->orWhere('m.name', 'like', "%{$search}%")
                    ->orWhere('k.name', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('chatbot_access_logs.opened_at', 'desc')->get([
            'chatbot_access_logs.id as access_id',
            'chatbot_access_logs.id_mahasiswa',
            'chatbot_access_logs.id_level',
            'chatbot_access_logs.id_soal',
            'chatbot_access_logs.opened_at',
            'chatbot_access_logs.closed_at',
            'chatbot_access_logs.durasi_menit',
            'm.nim',
            'm.name',
            DB::raw('COALESCE(k.name, "-") as kelas_name'),
        ]);
    }

    private function resolveEffectiveContextForRows($rows): array
    {
        $needFallbackAccessIds = $rows->filter(function ($row) {
            return empty($row->id_level) || empty($row->id_soal);
        })->pluck('access_id')->filter()->values()->toArray();

        $fallbackContext = $this->getFallbackContextForAccessIds($needFallbackAccessIds);

        $singleSoalByLevelCache = [];
        $historicalFallbackContext = [];

        foreach ($rows as $row) {
            $ctxFromAccessId = $fallbackContext[$row->access_id] ?? ['id_level' => null, 'id_soal' => null];

            $missingLevel = empty($row->id_level) && empty($ctxFromAccessId['id_level']);
            $missingSoal = empty($row->id_soal) && empty($ctxFromAccessId['id_soal']);

            if ($missingLevel || $missingSoal) {
                $historicalFallbackContext[$row->access_id] = $this->resolveHistoricalContext(
                    $row->id_mahasiswa,
                    $row->opened_at,
                    $row->closed_at,
                    $singleSoalByLevelCache
                );
            }
        }

        $effectiveContextByAccessId = [];

        foreach ($rows as $row) {
            $ctx = $fallbackContext[$row->access_id] ?? ['id_level' => null, 'id_soal' => null];
            $historicalCtx = $historicalFallbackContext[$row->access_id] ?? ['id_level' => null, 'id_soal' => null];

            $effectiveContextByAccessId[$row->access_id] = [
                'id_level' => $row->id_level ?: $ctx['id_level'] ?: $historicalCtx['id_level'],
                'id_soal' => $row->id_soal ?: $ctx['id_soal'] ?: $historicalCtx['id_soal'],
            ];
        }

        return $effectiveContextByAccessId;
    }

    private function filterRowsByContext($rows, array $effectiveContextByAccessId, $level, $soal): array
    {
        $result = [];

        foreach ($rows as $row) {
            $ctx = $effectiveContextByAccessId[$row->access_id] ?? ['id_level' => null, 'id_soal' => null];
            $effectiveLevelId = $ctx['id_level'];
            $effectiveSoalId = $ctx['id_soal'];

            if (!empty($level) && (string) $effectiveLevelId !== (string) $level) {
                continue;
            }

            if (!empty($soal) && (string) $effectiveSoalId !== (string) $soal) {
                continue;
            }

            $openedAt = !empty($row->opened_at) ? Carbon::parse($row->opened_at) : null;
            $closedAt = !empty($row->closed_at) ? Carbon::parse($row->closed_at) : null;

            $result[] = [
                'access_id' => $row->access_id,
                'id_mahasiswa' => $row->id_mahasiswa,
                'id_level' => $effectiveLevelId,
                'id_soal' => $effectiveSoalId,
                'nim' => $row->nim ?? null,
                'name' => $row->name ?? null,
                'kelas_name' => $row->kelas_name ?? '-',
                'opened_at' => $openedAt,
                'closed_at' => $closedAt,
                'durasi_menit' => $row->durasi_menit,
                'durasi_detik' => $this->hitungDurasiDetik($row->durasi_menit, $openedAt, $closedAt),
            ];
        }

        return $result;
    }

    private function buildJumlahLangkahByContext(array $contextTriples): array
    {
        if (empty($contextTriples)) {
            return [];
        }

        $uniqueMahasiswaIds = array_values(array_unique(array_map(function ($ctx) {
            return $ctx['id_mahasiswa'];
        }, $contextTriples)));

        $uniqueLevelIds = array_values(array_unique(array_map(function ($ctx) {
            return $ctx['id_level'];
        }, $contextTriples)));

        $uniqueSoalIds = array_values(array_unique(array_map(function ($ctx) {
            return $ctx['id_soal'];
        }, $contextTriples)));

        if (empty($uniqueMahasiswaIds) || empty($uniqueLevelIds) || empty($uniqueSoalIds)) {
            return [];
        }

        $summary = (new LogData())->setView('v_log_data')
            ->whereIn('id_mahasiswa', $uniqueMahasiswaIds)
            ->whereIn('id_level', $uniqueLevelIds)
            ->whereIn('id_soal', $uniqueSoalIds)
            ->selectRaw('id_mahasiswa, id_level, id_soal, COUNT(*) as total')
            ->groupBy('id_mahasiswa', 'id_level', 'id_soal')
            ->get();

        $result = [];
        foreach ($summary as $item) {
            $key = $item->id_mahasiswa . '|' . $item->id_level . '|' . $item->id_soal;
            $result[$key] = (int) $item->total;
        }

        return $result;
    }

    private function getSessionLogsByAccess(ChatbotAccessLog $accessLog)
    {
        $sessionLogs = ChatbotLog::query()
            ->where('access_id', $accessLog->id)
            ->where('id_mahasiswa', $accessLog->id_mahasiswa)
            ->where('type', 'biasa')
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'asc')
            ->get();

        if ($sessionLogs->isNotEmpty()) {
            return $sessionLogs;
        }

        $fallbackQuery = ChatbotLog::query()
            ->where('id_mahasiswa', $accessLog->id_mahasiswa)
            ->where('type', 'biasa')
            ->whereNull('deleted_at');

        if ($accessLog->opened_at && $accessLog->closed_at) {
            $fallbackQuery->whereBetween('created_at', [$accessLog->opened_at, $accessLog->closed_at]);
        } elseif ($accessLog->opened_at) {
            $openedAt = Carbon::parse($accessLog->opened_at);
            $fallbackQuery->where('created_at', '>=', $openedAt)
                ->where('created_at', '<=', $openedAt->copy()->addMinutes(10));
        }

        return $fallbackQuery->orderBy('created_at', 'asc')->get();
    }

    private function hitungDurasiDetik($durasiMenit, $openedAt, $closedAt): int
    {
        if (!is_null($durasiMenit)) {
            if ((int) $durasiMenit > 0) {
                return ((int) $durasiMenit) * 60;
            }

            if (!empty($openedAt) && !empty($closedAt)) {
                return abs($openedAt->diffInSeconds($closedAt));
            }

            return 0;
        }

        if (!empty($openedAt) && !empty($closedAt)) {
            return abs($openedAt->diffInSeconds($closedAt));
        }

        return 0;
    }

    private function formatDurasiDetik(int $detik): string
    {
        if ($detik <= 0) {
            return '0 menit';
        }

        $menit = intdiv($detik, 60);
        $sisaDetik = $detik % 60;

        if ($menit > 0 && $sisaDetik > 0) {
            return $menit . ' menit ' . $sisaDetik . ' detik';
        }

        if ($menit > 0) {
            return $menit . ' menit';
        }

        return '0 menit ' . $sisaDetik . ' detik';
    }

    private function formatWib($datetime): string
    {
        if (empty($datetime)) {
            return '-';
        }

        return Carbon::parse($datetime)->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s') . ' WIB';
    }

    private function formatWaktuRange($start, $end): string
    {
        if (empty($start) && empty($end)) {
            return '-';
        }

        if (empty($start)) {
            return $this->formatWib($end);
        }

        if (empty($end)) {
            return $this->formatWib($start);
        }

        $startText = $this->formatWib($start);
        $endText = $this->formatWib($end);

        if ($startText === $endText) {
            return $startText;
        }

        return $startText . ' s/d ' . $endText;
    }

    private function summarizeUniqueNames(array $values): string
    {
        $filtered = array_values(array_unique(array_filter($values, function ($value) {
            return !empty($value) && $value !== 'Tidak tercatat';
        })));

        if (empty($filtered)) {
            return 'Tidak tercatat';
        }

        if (count($filtered) === 1) {
            return $filtered[0];
        }

        return 'Beragam (' . count($filtered) . ')';
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
        $query = ChatbotLog::where('id_mahasiswa', $idMahasiswa)
            ->where('type', 'biasa')
            ->whereNull('deleted_at');

        $openedAtCarbon = $openedAt ? Carbon::parse($openedAt) : null;
        $closedAtCarbon = $closedAt ? Carbon::parse($closedAt) : null;

        if ($openedAtCarbon && $closedAtCarbon) {
            $query->whereBetween('created_at', [$openedAtCarbon, $closedAtCarbon]);
        } elseif ($openedAtCarbon) {
            $query->where('created_at', '>=', $openedAtCarbon)
                ->where('created_at', '<=', $openedAtCarbon->copy()->addMinutes(10));
        }

        $latestLog = (clone $query)
            ->orderBy('created_at', 'desc')
            ->first(['id_level', 'id_soal']);

        $latestLogWithLevel = (clone $query)
            ->whereNotNull('id_level')
            ->orderBy('created_at', 'desc')
            ->first(['id_level']);

        $latestLogWithSoal = (clone $query)
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

    private function formatDurasi($durasiMenit, $openedAt, $closedAt): string
    {
        if (!is_null($durasiMenit)) {
            if ((int) $durasiMenit > 0) {
                return ((int) $durasiMenit) . ' menit';
            }

            if (!empty($openedAt) && !empty($closedAt)) {
                $detik = abs(Carbon::parse($openedAt)->diffInSeconds(Carbon::parse($closedAt)));
                return '0 menit ' . $detik . ' detik';
            }

            return '0 menit';
        }

        if (!empty($openedAt) && !empty($closedAt)) {
            $detik = abs(Carbon::parse($openedAt)->diffInSeconds(Carbon::parse($closedAt)));

            if ($detik >= 60) {
                return ((int) floor($detik / 60)) . ' menit';
            }

            return '0 menit ' . $detik . ' detik';
        }

        return '-';
    }

    public function export(Request $request)
    {
        $idKelas = $request->input('kelas');
        $idLevel = $request->input('level');
        $idSoal = $request->input('soal');

        $filename = 'Log_Chatbot_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new LogChatbotExport($idKelas, $idLevel, $idSoal), $filename);
    }
}
