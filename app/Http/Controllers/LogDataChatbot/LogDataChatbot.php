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
        $query = $this->mahasiswaModel->setView('v_mahasiswa');

        if (!empty($kelas)) {
            $query = $query->where('id_kelas', $kelas);
        }

        // Apply search filter
        if (!empty($search)) {
            $query = $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $totalRecords = $query->count();
        $filteredRecords = $totalRecords;

        // Pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);

        $mahasiswas = $query->skip($start)->take($length)->get();

        $mahasiswaIds = $mahasiswas->pluck('id');

        // When level/soal filter active, count from chatbot_logs (has id_level/id_soal)
        // When no filter, count from chatbot_access_logs (open events)
        if (!empty($level) || !empty($soal)) {
            // Cari mahasiswa yang punya chatbot_logs di soal/level tersebut
            $relevantIds = ChatbotLog::whereIn('id_mahasiswa', $mahasiswaIds);
            if (!empty($level)) $relevantIds->where('id_level', $level);
            if (!empty($soal))  $relevantIds->where('id_soal', $soal);
            $relevantIds = $relevantIds->pluck('id_mahasiswa')->unique();

            // Hitung dari chatbot_access_logs seperti biasa
            $countBiasa = ChatbotAccessLog::whereIn('id_mahasiswa', $relevantIds)
                ->where('type', 'biasa')
                ->selectRaw('id_mahasiswa, count(*) as total')
                ->groupBy('id_mahasiswa')
                ->pluck('total', 'id_mahasiswa');

            $countAdaptive = ChatbotAccessLog::whereIn('id_mahasiswa', $relevantIds)
                ->where('type', 'adaptive')
                ->selectRaw('id_mahasiswa, count(*) as total')
                ->groupBy('id_mahasiswa')
                ->pluck('total', 'id_mahasiswa');
        } else {
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
        }

        $data = $mahasiswas->map(function ($mahasiswa) use ($countBiasa, $countAdaptive) {
            return [
                'id' => $mahasiswa->id,
                'nim' => $mahasiswa->nim,
                'name' => $mahasiswa->name,
                'kelas_name' => $mahasiswa->kelas_name ?? '-',
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

        $jumlahBiasa = $accessLogs->where('type', 'biasa')->count();
        $jumlahAdaptive = $accessLogs->where('type', 'adaptive')->count();

        $history = $accessLogs->map(function ($log) {
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

            return [
                'type'        => $log->type,
                'waktu_akses' => $log->opened_at ? $log->opened_at->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s') . ' WIB' : '-',
                'durasi'      => $durasiText,
            ];
        })->values()->toArray();

        $data = [
            'id' => $mahasiswa->id,
            'nim' => $mahasiswa->nim,
            'name' => $mahasiswa->name,
            'kelas_name' => $mahasiswa->kelas_name ?? '-',
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
        // TODO: Implement Excel export
        // return Excel::download(new LogDataChatbotExport($request->all()), 'log_data_chatbot.xlsx');
        
        return response()->json([
            'success' => false,
            'message' => 'Export belum tersedia. Silakan tunggu database siap.'
        ]);
    }
}

