<?php

namespace App\Services;

use App\Models\ChatbotAdaptiveLog;
use App\Models\Kelas;
use App\Models\Level;
use App\Models\LogData;
use App\Models\Mahasiswa;
use App\Models\Soal;
use App\Models\Ujian;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class LogChatbotAdaptiveService
{
    protected ChatbotAdaptiveLog $chatbotAdaptiveLogModel;
    protected Mahasiswa $mahasiswaModel;
    protected Kelas $kelasModel;
    protected Level $levelModel;
    protected Soal $soalModel;
    protected Ujian $ujianModel;
    protected LogData $logDataModel;

    public function __construct()
    {
        $this->chatbotAdaptiveLogModel = new ChatbotAdaptiveLog();
        $this->mahasiswaModel = new Mahasiswa();
        $this->kelasModel = new Kelas();
        $this->levelModel = new Level();
        $this->soalModel = new Soal();
        $this->ujianModel = new Ujian();
        $this->logDataModel = new LogData();
    }

    public function indexData(Request $request): array
    {
        $kelas = $request->input('kelas');
        $level = $request->input('level');
        $soal = $request->input('soal');
        $search = $this->resolveSearchTerm($request);

        $recordsTotal = $this->buildMahasiswaQuery('', '')->count();
        $recordsFiltered = $this->buildMahasiswaQuery($kelas, $search)->count();

        $students = $this->buildMahasiswaQuery($kelas, $search)
            ->orderBy('name', 'asc')
            ->get(['id', 'nim', 'name', 'id_kelas', 'kelas_name']);

        return [
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $this->buildRowsFromStudents($students, $kelas, $level, $soal),
        ];
    }

    public function detail(string $studentId): array
    {
        $mahasiswa = $this->mahasiswaModel->setView('v_mahasiswa')->find($studentId);
        if (!$mahasiswa) {
            return [
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan',
            ];
        }

        $history = $this->buildHistoryFromAdaptiveLogs($studentId);
        if ($history->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Data log chatbot adaptive tidak ditemukan',
            ];
        }

        $latestSession = $history->first();

        return [
            'success' => true,
            'data' => [
                'id' => $mahasiswa->id,
                'nim' => $mahasiswa->nim,
                'name' => $mahasiswa->name,
                'kelas_name' => $mahasiswa->kelas_name ?? '-',
                'level_name' => $latestSession['level_name'] ?? '-',
                'jenis_soal' => $latestSession['soal_title'] ?? '-',
                'labeling' => $latestSession['labeling'] ?? '-',
                'total_akses_adaptive' => $history->count(),
                'total_messages' => $history->sum('total_messages'),
                'history' => $history->values()->all(),
            ],
        ];
    }

    public function getSoalByLevel(?string $levelId): Collection
    {
        if (empty($levelId)) {
            return collect();
        }

        return $this->soalModel->where('id_level', $levelId)
            ->orderBy('order', 'asc')
            ->get(['id', 'judul']);
    }

    public function exportRows(?string $idKelas = null, ?string $idLevel = null, ?string $idSoal = null, string $search = ''): Collection
    {
        $students = $this->buildMahasiswaQuery($idKelas, trim($search))
            ->orderBy('name', 'asc')
            ->get(['id', 'nim', 'name', 'id_kelas', 'kelas_name']);

        return $this->buildRowsFromStudents($students, $idKelas, $idLevel, $idSoal)->values();
    }

    private function resolveSearchTerm(Request $request): string
    {
        $dataTablesSearch = trim((string) ($request->input('search.value') ?? ''));
        if ($dataTablesSearch !== '') {
            return $dataTablesSearch;
        }

        $customSearch = trim((string) ($request->input('search_mahasiswa') ?? ''));
        if ($customSearch !== '') {
            return $customSearch;
        }

        $genericSearch = $request->input('search');
        if (is_array($genericSearch)) {
            return trim((string) ($genericSearch['value'] ?? ''));
        }

        return trim((string) ($genericSearch ?? ''));
    }

    private function buildMahasiswaQuery(?string $kelas = null, string $search = '')
    {
        $query = $this->mahasiswaModel->setView('v_mahasiswa')->newQuery();

        if (!empty($kelas)) {
            $query = $query->where('id_kelas', $kelas);
        }

        if ($search !== '') {
            $keyword = '%' . $search . '%';
            $query = $query->whereRaw('(name LIKE ? OR nim LIKE ?)', [$keyword, $keyword]);
        }

        return $query;
    }

    private function buildAdaptiveLogQuery(?string $kelas = null, ?string $level = null, ?string $soal = null, string $search = '')
    {
        $query = $this->chatbotAdaptiveLogModel->newQuery();

        if (!empty($kelas)) {
            $query->where('id_kelas', $kelas);
        }

        if (!empty($level)) {
            $query->where('id_level', $level);
        }

        if (!empty($soal)) {
            $query->where('id_soal', $soal);
        }

        if ($search !== '') {
            $keyword = '%' . $search . '%';
            $query->whereRaw('(nama LIKE ? OR nim LIKE ?)', [$keyword, $keyword]);
        }

        return $query;
    }

    private function buildRowsFromStudents(Collection $students, ?string $kelas = null, ?string $level = null, ?string $soal = null): Collection
    {
        if ($students->isEmpty()) {
            return collect();
        }

        $studentIds = $students->pluck('id')->filter()->values();
        $logsByStudent = collect();

        if ($studentIds->isNotEmpty()) {
            $logsByStudent = $this->buildAdaptiveLogQuery($kelas, $level, $soal, '')
                ->whereIn('id_mahasiswa', $studentIds)
                ->orderBy('waktu_mulai', 'desc')
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy('id_mahasiswa');
        }

        return $students
            ->map(function ($student) use ($logsByStudent) {
                $studentLogs = $logsByStudent->get((string) $student->id, collect());

                if ($studentLogs->isEmpty()) {
                    return $this->buildEmptyStudentSummary($student);
                }

                $summary = $this->buildStudentSummaryFromAdaptiveLogs($studentLogs);
                if (is_null($summary)) {
                    return $this->buildEmptyStudentSummary($student);
                }

                $summary['id'] = $student->id;
                $summary['nim'] = $student->nim ?? '-';
                $summary['name'] = $student->name ?? '-';
                $summary['kelas_name'] = $student->kelas_name ?? '-';

                return $summary;
            })
            ->values()
            ->sortBy(function (array $summary) {
                return mb_strtolower((string) ($summary['name'] ?? ''));
            })
            ->values();
    }

    private function buildEmptyStudentSummary($student): array
    {
        return [
            'id' => $student->id,
            'nim' => $student->nim ?? '-',
            'name' => $student->name ?? '-',
            'kelas_name' => $student->kelas_name ?? '-',
            'level_name' => '-',
            'jenis_soal' => '-',
            'jumlah_langkah' => 0,
            'waktu' => '-',
            'labeling' => '-',
            'durasi' => '-',
            'total_akses_adaptive' => 0,
            'total_messages' => 0,
            'history' => [],
        ];
    }

    private function buildStudentSummaryFromAdaptiveLogs(Collection $studentLogs): ?array
    {
        if ($studentLogs->isEmpty()) {
            return null;
        }

        $history = $studentLogs
            ->sortByDesc(function (ChatbotAdaptiveLog $log) {
                return $log->waktu_mulai ?? $log->created_at;
            })
            ->values()
            ->map(function (ChatbotAdaptiveLog $log) {
                return $this->mapAdaptiveLogToHistoryItem($log);
            })
            ->values();

        $latestSession = $history->first();
        $totalWaktuDetik = $history->sum(function (array $session) {
            return (int) ($session['waktu_detik'] ?? 0);
        });
        $totalLangkah = $history->sum(function (array $session) {
            return (int) ($session['jumlah_langkah'] ?? 0);
        });
        $totalDurasiDetik = $history->sum(function (array $session) {
            return (int) ($session['durasi_detik'] ?? 0);
        });

        /** @var ChatbotAdaptiveLog $firstLog */
        $firstLog = $studentLogs->first();

        $name = $firstLog->nama;
        $nim = $firstLog->nim;
        $kelasName = $firstLog->kelas;

        if (!$name || !$nim || !$kelasName) {
            $mahasiswa = $this->mahasiswaModel->setView('v_mahasiswa')->find($firstLog->id_mahasiswa);
            $name = $name ?: ($mahasiswa->name ?? '-');
            $nim = $nim ?: ($mahasiswa->nim ?? '-');
            $kelasName = $kelasName ?: ($mahasiswa->kelas_name ?? '-');
        }

        return [
            'id' => $firstLog->id_mahasiswa,
            'nim' => $nim ?? '-',
            'name' => $name ?? '-',
            'kelas_name' => $kelasName ?? '-',
            'level_name' => $latestSession['level_name'] ?? '-',
            'jenis_soal' => $latestSession['soal_title'] ?? '-',
            'jumlah_langkah' => max(0, (int) $totalLangkah),
            'waktu' => $history->isNotEmpty() ? $this->formatSecondsDuration($totalWaktuDetik) : '-',
            'labeling' => $latestSession['labeling'] ?? '-',
            'durasi' => $history->isNotEmpty() ? $this->formatSecondsDuration($totalDurasiDetik) : '-',
            'total_akses_adaptive' => $history->count(),
            'total_messages' => $history->sum('total_messages'),
            'history' => $history->values()->all(),
        ];
    }

    private function buildHistoryFromAdaptiveLogs(string $studentId): Collection
    {
        return $this->chatbotAdaptiveLogModel->newQuery()
            ->where('id_mahasiswa', $studentId)
            ->orderBy('waktu_mulai', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function (ChatbotAdaptiveLog $log) {
                return $this->mapAdaptiveLogToHistoryItem($log);
            })
            ->values();
    }

    private function mapAdaptiveLogToHistoryItem(ChatbotAdaptiveLog $log): array
    {
        $detail = $this->normalizeDetailPayload($log->detail);
        $messages = $this->extractMessagesFromDetail($detail, (string) ($log->pesan_bimbingan ?? ''));
        $totalMessages = $this->extractTotalMessages($detail, count($messages));
        $labeling = $this->resolveAdaptiveLabeling($log);

        $startAt = $this->resolveAttemptStartAtFromAdaptiveLog($log, $detail);
        $endAt = $this->resolveCompletionEndAt($log, $detail, $startAt);
        $waktuDetik = $this->resolveDurationBetween($startAt, $endAt, $detail, $log);
        $durasiDetik = $this->resolvePopupDuration($log, $detail, $startAt, $endAt);
        $jumlahLangkah = $this->resolveJumlahLangkah($log, $detail, $startAt, $endAt);

        return [
            'id' => $log->id,
            'id_level' => $log->id_level,
            'id_soal' => $log->id_soal,
            'level_name' => $log->level_soal ?? '-',
            'soal_title' => $log->jenis_soal ?? '-',
            'waktu_mulai_label' => $this->formatDateTimeLabel($startAt),
            'waktu_selesai_label' => $this->formatDateTimeLabel($endAt),
            'selesai_karena' => $this->resolveEndReason($log, $detail, $endAt),
            'waktu_akses' => $this->formatSecondsDuration($waktuDetik),
            'waktu_pengerjaan' => $this->formatSecondsDuration($waktuDetik),
            'waktu_akses_detik' => $waktuDetik,
            'durasi' => $this->formatSecondsDuration($durasiDetik),
            'durasi_detik' => $durasiDetik,
            'jumlah_langkah' => $jumlahLangkah,
            'waktu' => $this->formatSecondsDuration($waktuDetik),
            'waktu_detik' => $waktuDetik,
            'labeling' => $labeling,
            'total_messages' => $totalMessages,
            'messages' => $messages,
        ];
    }

    private function resolveAdaptiveLabeling(ChatbotAdaptiveLog $log): string
    {
        $label = trim((string) ($log->labeling ?? ''));
        if ($label === '') {
            $label = trim((string) ($log->pesan_bimbingan ?? ''));
        }

        if ($label === '') {
            return '-';
        }

        if (preg_match('/\[ADAPTIVE\s*-\s*(.*?)\]/i', $label, $matches) === 1) {
            $parsed = trim((string) ($matches[1] ?? ''));
            if ($parsed !== '') {
                return $parsed;
            }
        }

        return $label;
    }

    private function resolveAttemptStartAtFromAdaptiveLog(ChatbotAdaptiveLog $log, array $detail): ?Carbon
    {
        if ($log->waktu_mulai) {
            return $log->waktu_mulai->copy();
        }

        if (!empty($detail['attempt_start_at'])) {
            try {
                return Carbon::parse((string) $detail['attempt_start_at']);
            } catch (\Throwable $e) {
            }
        }

        if (!empty($detail['triggered_at'])) {
            try {
                $triggeredAt = Carbon::parse((string) $detail['triggered_at']);
                $elapsed = $this->resolveElapsedSecondsFromDetail($detail);
                if (!is_null($elapsed)) {
                    return $triggeredAt->copy()->subSeconds($elapsed);
                }
                return $triggeredAt;
            } catch (\Throwable $e) {
            }
        }

        return $log->created_at ? $log->created_at->copy() : null;
    }

    private function resolveCompletionEndAt(ChatbotAdaptiveLog $log, array $detail, ?Carbon $startAt): ?Carbon
    {
        if (!empty($detail['submit_benar_at'])) {
            try {
                $submitAt = Carbon::parse((string) $detail['submit_benar_at']);
                if (!$startAt || $submitAt->gte($startAt)) {
                    return $submitAt;
                }
            } catch (\Throwable $e) {
            }
        }

        if (!empty($detail['popup_closed_at'])) {
            try {
                $closedAt = Carbon::parse((string) $detail['popup_closed_at']);
                if (!$startAt || $closedAt->gte($startAt)) {
                    return $closedAt;
                }
            } catch (\Throwable $e) {
            }
        }

        if ($log->waktu_selesai) {
            return $log->waktu_selesai->copy();
        }

        return $log->created_at ? $log->created_at->copy() : null;
    }

    private function resolveEndReason(ChatbotAdaptiveLog $log, array $detail, ?Carbon $endAt): string
    {
        if ($endAt && !empty($detail['submit_benar_at'])) {
            return 'submit benar';
        }

        if ($endAt && !empty($detail['popup_closed_at'])) {
            return 'close soal';
        }

        if ($log->waktu_selesai) {
            return 'close soal';
        }

        return '-';
    }

    private function resolveDurationBetween(?Carbon $startAt, ?Carbon $endAt, array $detail, ChatbotAdaptiveLog $log): int
    {
        if (!is_null($detail['waktu_detik_submit'] ?? null)) {
            $seconds = $this->parseDurationValueToSeconds($detail['waktu_detik_submit']);
            if (!is_null($seconds)) {
                return $seconds;
            }
        }

        if (!is_null($detail['waktu_detik_saat_close'] ?? null)) {
            $seconds = $this->parseDurationValueToSeconds($detail['waktu_detik_saat_close']);
            if (!is_null($seconds)) {
                return $seconds;
            }
        }

        if ($startAt && $endAt && !$startAt->gt($endAt)) {
            return max(0, $startAt->diffInSeconds($endAt));
        }

        if ($log->waktu_mulai && $log->waktu_selesai && !$log->waktu_mulai->gt($log->waktu_selesai)) {
            return max(0, $log->waktu_mulai->diffInSeconds($log->waktu_selesai));
        }

        foreach (['waktu_detik', 'waktu_akses_detik', 'total_waktu_detik', 'waktu'] as $key) {
            if (array_key_exists($key, $detail)) {
                $seconds = $this->parseDurationValueToSeconds($detail[$key]);
                if (!is_null($seconds)) {
                    return $seconds;
                }
            }
        }

        return 0;
    }

    private function resolvePopupDuration(ChatbotAdaptiveLog $log, array $detail, ?Carbon $startAt, ?Carbon $endAt): int
    {
        if (!empty($detail['durasi_detik'])) {
            $seconds = $this->parseDurationValueToSeconds($detail['durasi_detik']);
            if (!is_null($seconds)) {
                return $seconds;
            }
        }

        if (!empty($detail['popup_opened_at']) && !empty($detail['popup_closed_at'])) {
            try {
                return max(0, Carbon::parse((string) $detail['popup_opened_at'])->diffInSeconds(Carbon::parse((string) $detail['popup_closed_at'])));
            } catch (\Throwable $e) {
            }
        }

        if ($log->durasi_menit !== null) {
            return max(0, (int) $log->durasi_menit) * 60;
        }

        if ($startAt && $endAt) {
            return max(0, $startAt->diffInSeconds($endAt));
        }

        return 0;
    }

    private function resolveJumlahLangkah(ChatbotAdaptiveLog $log, array $detail, ?Carbon $startAt, ?Carbon $endAt): int
    {
        if (!empty($detail['jumlah_langkah'])) {
            return max(0, (int) $detail['jumlah_langkah']);
        }

        if ($log->jumlah_langkah !== null) {
            return max(0, (int) $log->jumlah_langkah);
        }

        if ($startAt && $endAt && !empty($log->id_mahasiswa) && !empty($log->id_soal)) {
            return $this->logDataModel->newQuery()
                ->where('id_mahasiswa', $log->id_mahasiswa)
                ->where('id_soal', $log->id_soal)
                ->whereBetween('created_at', [$startAt, $endAt])
                ->count();
        }

        return 0;
    }

    private function resolveElapsedSecondsFromDetail(array $detail): ?int
    {
        foreach (['waktu_detik', 'waktu_akses_detik', 'total_waktu_detik'] as $key) {
            if (array_key_exists($key, $detail)) {
                $seconds = $this->parseDurationValueToSeconds($detail[$key]);
                if (!is_null($seconds)) {
                    return $seconds;
                }
            }
        }

        return null;
    }

    private function normalizeDetailPayload($detail): array
    {
        if (is_array($detail)) {
            return $detail;
        }

        if (is_string($detail) && $detail !== '') {
            $decoded = json_decode($detail, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }

    private function extractTotalMessages(array $detail, int $fallback): int
    {
        if (isset($detail['total_messages'])) {
            return max(0, (int) $detail['total_messages']);
        }

        if (isset($detail['total_pesan'])) {
            return max(0, (int) $detail['total_pesan']);
        }

        return $fallback;
    }

    private function extractMessagesFromDetail(array $detail, string $pesanBimbingan): array
    {
        $messages = [];

        if (isset($detail['messages']) && is_array($detail['messages'])) {
            foreach ($detail['messages'] as $message) {
                if (!is_array($message)) {
                    continue;
                }

                $messages[] = [
                    'waktu' => (string) ($message['waktu'] ?? $message['created_at'] ?? '-'),
                    'pesan' => (string) ($message['pesan'] ?? $message['message'] ?? $pesanBimbingan),
                    'respons' => (string) ($message['respons'] ?? $message['response'] ?? ''),
                    'mahasiswa_name' => (string) ($message['mahasiswa_name'] ?? 'Mahasiswa'),
                    'bot_name' => (string) ($message['bot_name'] ?? 'PseudoLearn Chatbot AI'),
                    'mahasiswa_message' => (string) ($message['mahasiswa_message'] ?? ''),
                    'bot_response' => (string) ($message['bot_response'] ?? $message['respons'] ?? $message['response'] ?? ''),
                    'source' => (string) ($message['source'] ?? ''),
                ];
            }
        }

        if (empty($messages) && $pesanBimbingan !== '') {
            $messages[] = [
                'waktu' => '-',
                'pesan' => $pesanBimbingan,
                'respons' => '',
                'mahasiswa_name' => 'Mahasiswa',
                'bot_name' => 'PseudoLearn Chatbot AI',
                'mahasiswa_message' => '',
                'bot_response' => '',
                'source' => 'legacy_fallback',
            ];
        }

        return $messages;
    }

    private function parseDurationValueToSeconds($value): ?int
    {
        if (is_null($value)) {
            return null;
        }

        if (is_int($value) || is_float($value) || (is_string($value) && is_numeric($value))) {
            return max(0, (int) $value);
        }

        if (!is_string($value)) {
            return null;
        }

        $text = trim($value);
        if ($text === '') {
            return null;
        }

        if (preg_match('/^(\d{1,2}):(\d{1,2})(?::(\d{1,2}))?$/', $text, $timeParts) === 1) {
            if (isset($timeParts[3]) && $timeParts[3] !== '') {
                return max(0, ((int) $timeParts[1] * 3600) + ((int) $timeParts[2] * 60) + (int) $timeParts[3]);
            }

            return max(0, ((int) $timeParts[1] * 60) + (int) $timeParts[2]);
        }

        if (preg_match('/^(\d+)\s*m(?:enit)?\s*(\d+)?\s*(?:d|detik|s|sec(?:ond)?s?)?$/i', $text, $compactMatches) === 1) {
            $minutes = (int) ($compactMatches[1] ?? 0);
            $seconds = isset($compactMatches[2]) && $compactMatches[2] !== '' ? (int) $compactMatches[2] : 0;
            return max(0, ($minutes * 60) + $seconds);
        }

        if (preg_match('/^(\d+)\s*(?:d|detik|s|sec(?:ond)?s?)$/i', $text, $secondsOnlyMatch) === 1) {
            return max(0, (int) ($secondsOnlyMatch[1] ?? 0));
        }

        if (preg_match('/(\d+)\s*menit/i', $text, $minuteMatch) !== 1 && preg_match('/(\d+)\s*detik/i', $text, $secondMatch) !== 1) {
            return null;
        }

        $minutes = 0;
        $seconds = 0;

        if (preg_match('/(\d+)\s*menit/i', $text, $minuteMatch) === 1) {
            $minutes = (int) ($minuteMatch[1] ?? 0);
        }

        if (preg_match('/(\d+)\s*detik/i', $text, $secondMatch) === 1) {
            $seconds = (int) ($secondMatch[1] ?? 0);
        }

        return max(0, ($minutes * 60) + $seconds);
    }

    private function formatSecondsDuration(?int $totalDetik): string
    {
        if (is_null($totalDetik)) {
            return '-';
        }

        $detik = max(0, $totalDetik);
        $menit = (int) floor($detik / 60);
        $sisaDetik = $detik % 60;

        return $menit . ' menit ' . $sisaDetik . ' detik';
    }

    private function formatDateTimeLabel($dateTime): string
    {
        if (!$dateTime) {
            return '-';
        }

        try {
            return Carbon::parse($dateTime)->setTimezone('Asia/Jakarta')->format('d/m/Y H:i:s') . ' WIB';
        } catch (\Throwable $e) {
            return '-';
        }
    }
}
