<?php

namespace App\Http\Controllers\LogDataChatbot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Level;
use App\Models\Soal;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\ChatbotAccessLog;
use App\Models\ChatbotLog;
use App\Exports\LogDataChatbotExport;
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
                'angkatan' => $item['angkatan']
            ];
        })->values()->toArray();

        $list_level = $this->levelModel->orderBy('order', 'asc')->get(['id', 'name'])
            ->pluck('name', 'id')
            ->toArray();

        $list_level = collect($list_level)->map(function ($name, $id) {
            return [
                'id' => $id,
                'name' => $name
            ];
        })->values()->toArray();

        return view('pages.logDataChatbot.index', [
            'title' => 'Log Data Chatbot',
            'list_kelas' => $list_kelas,
            'list_level' => $list_level
        ]);
    }

    /**
     * Get table data for DataTables
     * TODO: Connect to actual database when ready
     */
    public function table(Request $request)
    {
        $kelas = $request->input('kelas');
        $level = $request->input('level');
        $soal = $request->input('soal');
        $search = $request->input('search')['value'] ?? '';

        // Query mahasiswa with chatbot data
        $baseQuery = $this->mahasiswaModel->setView('v_mahasiswa');

        if (!empty($kelas)) {
            $baseQuery = $baseQuery->where('id_kelas', $kelas);
        }

        // Apply search filter
        if (!empty($search)) {
            $baseQuery = $baseQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $totalRecords = (clone $baseQuery)->count();

        $filteredQuery = clone $baseQuery;

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

        // Pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);

        $mahasiswas = $filteredQuery->skip($start)->take($length)->get();

        $mahasiswaIds = $mahasiswas->pluck('id');
        $latestLevelByMahasiswa = [];

        $latestChatbotLogs = ChatbotLog::whereIn('id_mahasiswa', $mahasiswaIds)
            ->with('level:id,name')
            ->when(!empty($level), function ($query) use ($level) {
                $query->where('id_level', $level);
            })
            ->when(!empty($soal), function ($query) use ($soal) {
                $query->where('id_soal', $soal);
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
            'data' => $data
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
     * TODO: Connect to actual database when ready
     */
    public function detail($id)
    {
        $mahasiswa = $this->mahasiswaModel->setView('v_mahasiswa')->find($id);

        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan'
            ]);
        }

        $accessLogs = ChatbotAccessLog::where('id_mahasiswa', $id)
            ->orderBy('opened_at', 'desc')
            ->get();

        $chatbotLogs = ChatbotLog::where('id_mahasiswa', $id)
            ->with('level:id,name')
            ->orderBy('created_at', 'desc')
            ->get();

        $jumlahBiasa = $accessLogs->where('type', 'biasa')->count();
        $jumlahAdaptive = $accessLogs->where('type', 'adaptive')->count();

        $latestLevel = $chatbotLogs->first()?->level?->name ?? '-';

        $history = $accessLogs->map(function ($log) use ($chatbotLogs) {
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

            $sessionEnd = $log->closed_at ?? now();
            $levelName = $chatbotLogs->first(function ($chatbotLog) use ($log, $sessionEnd) {
                return $chatbotLog->created_at
                    && $log->opened_at
                    && $chatbotLog->created_at->betweenIncluded($log->opened_at, $sessionEnd);
            })?->level?->name ?? '-';

            return [
                'type'        => $log->type,
                'level_name'  => $levelName,
                'waktu_akses' => $log->opened_at ? $log->opened_at->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s') . ' WIB' : '-',
                'durasi'      => $durasiText,
            ];
        })->values()->toArray();

        $data = [
            'id' => $mahasiswa->id,
            'nim' => $mahasiswa->nim,
            'name' => $mahasiswa->name,
            'kelas_name' => $mahasiswa->kelas_name ?? '-',
            'level_name' => $latestLevel,
            'jumlah_chatbot' => $jumlahBiasa,
            'jumlah_chatbot_adaptive' => $jumlahAdaptive,
            'history' => $history,
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Export data to Excel
     * TODO: Implement actual export when database is ready
     */
    public function export(Request $request)
    {
        $idKelas = $request->input('kelas');
        $idLevel = $request->input('level');
        $idSoal  = $request->input('soal');

        $filename = 'Log_Data_Chatbot_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new LogDataChatbotExport($idKelas, $idLevel, $idSoal), $filename);
    }
}

