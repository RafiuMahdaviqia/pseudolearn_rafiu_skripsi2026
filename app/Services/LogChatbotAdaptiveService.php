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
        $search = trim((string) ($request->input('search_custom') ?? $request->input('search.value') ?? ''));

        $recordsTotal = $this->buildMahasiswaQuery('', '')->count();
        $students = $this->buildMahasiswaQuery($kelas, $search)
            ->orderBy('name', 'asc')
            ->get(['id', 'nim', 'name', 'id_kelas', 'kelas_name', 'angkatan']);

        $students = $this->filterStudentsByKelas($students, $kelas);
        $students = $this->filterStudentsBySearch($students, $search);
        $recordsFiltered = $students->count();

        $rows = $this->buildRowsFromStudents($students, $kelas, $level, $soal);

        return [
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
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

    public function exportRows(
        ?string $idKelas = null,
        ?string $idLevel = null,
        ?string $idSoal = null,
        ?string $search = null
    ): Collection
    {
        $searchValue = trim((string) ($search ?? ''));
        $logs = $this->buildAdaptiveLogQuery($idKelas, $idLevel, $idSoal, $searchValue)
            ->orderBy('waktu_mulai', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->buildRowsFromLogs($logs)->values();
    }

    private function buildMahasiswaQuery(?string $kelas = null, string $search = '')
    {
        $query = $this->mahasiswaModel->setView('v_mahasiswa');

        if (!empty($kelas)) {
            $kelasValue = trim((string) $kelas);
            $kelasParts = $this->parseKelasLabel($kelasValue);

            $query->where(function ($kelasQuery) use ($kelasValue, $kelasParts) {
                $kelasQuery->where('id_kelas', $kelasValue)
                    ->orWhere('kelas_name', $kelasValue);

                if (!empty($kelasParts)) {
                    $kelasQuery->orWhere(function ($subQuery) use ($kelasParts) {
                        $subQuery->where('kelas_name', $kelasParts['name'])
                            ->where('angkatan', $kelasParts['angkatan']);
                    });
                }
            });
        }

        if ($search !== '') {
            $query->where(function ($searchQuery) use ($search) {
                $searchQuery->where('name', 'like', '%' . $search . '%')
                    ->orWhere('nim', 'like', '%' . $search . '%');
            });
        }

        return $query;
    }

    private function buildAdaptiveLogQuery(?string $kelas = null, ?string $level = null, ?string $soal = null, string $search = '')
    {
        $query = $this->chatbotAdaptiveLogModel->newQuery();

        if (!empty($kelas)) {
            $kelasValue = trim((string) $kelas);
            $kelasParts = $this->parseKelasLabel($kelasValue);

            $query->where(function ($kelasQuery) use ($kelasValue, $kelasParts) {
                $kelasQuery->where('id_kelas', $kelasValue)
                    ->orWhere('kelas', $kelasValue);

                if (!empty($kelasParts)) {
                    $kelasQuery->orWhere('kelas', $kelasParts['name']);
                }
            });
        }

        if (!empty($level)) {
            $query->where('id_level', $level);
        }

        if (!empty($soal)) {
            $query->where('id_soal', $soal);
        }

        if ($search !== '') {
            $query->where(function ($searchQuery) use ($search) {
                $searchQuery->where('nama', 'like', '%' . $search . '%')
                    ->orWhere('nim', 'like', '%' . $search . '%');
            });
        }

        return $query;
    }

    private function buildRowsFromLogs(Collection $logs): Collection
    {
        return $logs
            ->groupBy('id_mahasiswa')
            ->map(function (Collection $studentLogs) {
                return $this->buildStudentSummaryFromAdaptiveLogs($studentLogs);
            })
            ->filter(function (?array $summary) {
                return !is_null($summary);
            })
            ->values()
            ->sortBy(function (array $summary) {
                return mb_strtolower((string) ($summary['name'] ?? ''));
            })
            ->values();
    }

    private function buildRowsFromStudents(
        Collection $students,
        ?string $kelas = null,
        ?string $level = null,
        ?string $soal = null
    ): Collection {
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
        $waktuTotal = $history->isNotEmpty()
            ? $this->formatSecondsDuration($totalWaktuDetik)
            : '-';
        $durasiTotal = $history->isNotEmpty()
            ? $this->formatSecondsDuration($totalDurasiDetik)
            : '-';

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
            'waktu' => $waktuTotal,
            'labeling' => $latestSession['labeling'] ?? '-',
            'durasi' => $durasiTotal,
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
        $resolvedLabeling = $this->resolveAdaptiveLabeling($log);

        $strictWaktuDetik = $this->resolveStrictWaktuDetikFromDetail($detail);
        $completionProgress = is_null($strictWaktuDetik)
            ? $this->resolveCompletionProgress($log, $detail)
            : null;

        $waktuDetik = $strictWaktuDetik ?? ($completionProgress['waktu_detik'] ?? $this->resolveWaktuDetik($log, $detail));
        $durasiDetik = $this->resolveDurasiDetik($log, $detail, $waktuDetik);
        $jumlahLangkah = (int) ($detail['jumlah_langkah'] ?? ($completionProgress['jumlah_langkah'] ?? ($log->jumlah_langkah ?? 0)));

        return [
            'id' => $log->id,
            'id_level' => $log->id_level,
            'id_soal' => $log->id_soal,
            'level_name' => $log->level_soal ?? '-',
            'soal_title' => $log->jenis_soal ?? '-',
            'waktu_akses' => $this->formatSecondsDuration($waktuDetik),
            'waktu_akses_detik' => $waktuDetik,
            'durasi' => $this->formatSecondsDuration($durasiDetik),
            'durasi_detik' => $durasiDetik,
            'jumlah_langkah' => $jumlahLangkah,
            'waktu' => $this->formatSecondsDuration($waktuDetik),
            'waktu_detik' => $waktuDetik,
            'labeling' => $resolvedLabeling,
            'total_messages' => $totalMessages,
            'messages' => $messages,
        ];
    }

    private function resolveAdaptiveLabeling(ChatbotAdaptiveLog $log): string
    {
        $fromColumn = $this->normalizeLabeling($log->labeling);
        if ($fromColumn !== '-') {
            return $fromColumn;
        }

        return $this->normalizeLabeling($log->pesan_bimbingan);
    }

    private function resolveCompletionProgress(ChatbotAdaptiveLog $log, array $detail): ?array
    {
        if (empty($log->id_mahasiswa) || empty($log->id_soal)) {
            return null;
        }

        $startAt = $this->resolveAttemptStartAtFromAdaptiveLog($log, $detail);
        if (!$startAt) {
            return null;
        }

        $triggeredAt = $this->resolveTriggeredAt($log, $detail) ?? $startAt;
        $endAt = null;

        $correctSubmission = $this->ujianModel->newQuery()
            ->where('id_mahasiswa', $log->id_mahasiswa)
            ->where('id_soal', $log->id_soal)
            ->where('status', 1)
            ->where('created_at', '>=', $triggeredAt)
            ->orderBy('created_at', 'asc')
            ->first();

        if ($correctSubmission && $correctSubmission->created_at) {
            $endAt = $correctSubmission->created_at->copy();
        }

        if (is_null($endAt) && !empty($detail['submit_benar_at'])) {
            try {
                $parsedSubmitAt = Carbon::parse((string) $detail['submit_benar_at']);
                $endAt = $parsedSubmitAt;
            } catch (\Throwable $e) {
            }
        }

        if (is_null($endAt) && $log->waktu_selesai) {
            $endAt = $log->waktu_selesai->copy();
        }

        if (is_null($endAt)) {
            $lastSubmit = $this->ujianModel->newQuery()
                ->where('id_mahasiswa', $log->id_mahasiswa)
                ->where('id_soal', $log->id_soal)
                ->where('created_at', '>=', $startAt)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($lastSubmit && $lastSubmit->created_at) {
                $endAt = $lastSubmit->created_at->copy();
            }
        }

        if (is_null($endAt)) {
            $lastInteractionAt = $this->logDataModel->newQuery()
                ->where('id_mahasiswa', $log->id_mahasiswa)
                ->where('id_soal', $log->id_soal)
                ->where('created_at', '>=', $startAt)
                ->max('created_at');

            if (!empty($lastInteractionAt)) {
                try {
                    $endAt = Carbon::parse((string) $lastInteractionAt);
                } catch (\Throwable $e) {
                }
            }
        }

        if (!$endAt) {
            return null;
        }

        if ($startAt->gt($endAt)) {
            return null;
        }

        $jumlahLangkah = $this->logDataModel->newQuery()
            ->where('id_mahasiswa', $log->id_mahasiswa)
            ->where('id_soal', $log->id_soal)
            ->whereBetween('created_at', [$startAt, $endAt])
            ->count();

        return [
            'waktu_detik' => $startAt->diffInSeconds($endAt),
            'jumlah_langkah' => max(0, (int) $jumlahLangkah),
        ];
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

        $triggeredAt = $this->resolveTriggeredAt($log, $detail);
        if (!$triggeredAt) {
            return null;
        }

        $elapsed = $this->resolveElapsedSecondsFromDetail($detail);
        if (is_null($elapsed)) {
            return null;
        }

        return $triggeredAt->copy()->subSeconds($elapsed);
    }

    private function parseKelasLabel(string $kelas): ?array
    {
        if ($kelas === '') {
            return null;
        }

        if (preg_match('/^(.*?)\s*\((\d+)\)\s*$/', $kelas, $matches) !== 1) {
            return null;
        }

        $name = trim((string) ($matches[1] ?? ''));
        $angkatan = trim((string) ($matches[2] ?? ''));

        if ($name === '' || $angkatan === '') {
            return null;
        }

        return [
            'name' => $name,
            'angkatan' => $angkatan,
        ];
    }

    private function filterStudentsByKelas(Collection $students, ?string $kelas): Collection
    {
        $kelasValue = trim((string) ($kelas ?? ''));
        if ($kelasValue === '') {
            return $students->values();
        }

        $kelasParts = $this->parseKelasLabel($kelasValue);

        return $students
            ->filter(function ($student) use ($kelasValue, $kelasParts) {
                $idKelas = trim((string) ($student->id_kelas ?? ''));
                $kelasName = trim((string) ($student->kelas_name ?? ''));
                $angkatan = trim((string) ($student->angkatan ?? ''));

                if ($idKelas !== '' && $idKelas === $kelasValue) {
                    return true;
                }

                if ($kelasName !== '' && $kelasName === $kelasValue) {
                    return true;
                }

                if (!empty($kelasParts)) {
                    return $kelasName === $kelasParts['name'] && $angkatan === $kelasParts['angkatan'];
                }

                return false;
            })
            ->values();
    }

    private function filterStudentsBySearch(Collection $students, ?string $search): Collection
    {
        $searchValue = trim((string) ($search ?? ''));
        if ($searchValue === '') {
            return $students->values();
        }

        $needle = mb_strtolower($searchValue);

        return $students
            ->filter(function ($student) use ($needle) {
                $name = mb_strtolower((string) ($student->name ?? ''));
                $nim = mb_strtolower((string) ($student->nim ?? ''));

                return ($name !== '' && str_contains($name, $needle))
                    || ($nim !== '' && str_contains($nim, $needle));
            })
            ->values();
    }

    private function resolveTriggeredAt(ChatbotAdaptiveLog $log, array $detail): ?Carbon
    {
        if (!empty($detail['triggered_at'])) {
            try {
                return Carbon::parse((string) $detail['triggered_at']);
            } catch (\Throwable $e) {
            }
        }

        if ($log->created_at) {
            return $log->created_at->copy();
        }

        return null;
    }

    private function resolveElapsedSecondsFromDetail(array $detail): ?int
    {
        foreach (['waktu_detik_submit', 'waktu_detik_saat_close', 'waktu_detik', 'waktu_akses_detik', 'total_waktu_detik', 'waktu'] as $key) {
            if (array_key_exists($key, $detail)) {
                $seconds = $this->parseDurationValueToSeconds($detail[$key]);
                if (!is_null($seconds)) {
                    return $seconds;
                }
            }
        }

        return null;
    }

    private function resolveStrictWaktuDetikFromDetail(array $detail): ?int
    {
        foreach (['waktu_detik_submit', 'waktu_detik_saat_close', 'waktu_detik', 'waktu_akses_detik', 'total_waktu_detik', 'waktu'] as $key) {
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

    private function normalizeLabeling(?string $labeling): string
    {
        $label = trim((string) $labeling);

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

    private function resolveWaktuDetik(ChatbotAdaptiveLog $log, array $detail): ?int
    {
        if ($log->waktu_mulai && $log->waktu_selesai && !$log->waktu_mulai->gt($log->waktu_selesai)) {
            return (int) $log->waktu_mulai->diffInSeconds($log->waktu_selesai);
        }

        foreach (['waktu_detik_submit', 'waktu_detik_saat_close', 'waktu_detik', 'waktu_akses_detik', 'total_waktu_detik', 'waktu'] as $key) {
            if (array_key_exists($key, $detail)) {
                $seconds = $this->parseDurationValueToSeconds($detail[$key]);
                if (!is_null($seconds)) {
                    return $seconds;
                }
            }
        }

        return null;
    }

    private function resolveDurasiDetik(ChatbotAdaptiveLog $log, array $detail, ?int $fallbackWaktuDetik = null): ?int
    {
        if (array_key_exists('durasi_detik', $detail)) {
            $seconds = $this->parseDurationValueToSeconds($detail['durasi_detik']);
            if (!is_null($seconds)) {
                return $seconds;
            }
        }

        if (!empty($detail['popup_opened_at']) && !empty($detail['popup_closed_at'])) {
            try {
                $openedAt = Carbon::parse((string) $detail['popup_opened_at']);
                $closedAt = Carbon::parse((string) $detail['popup_closed_at']);
                if (!$openedAt->gt($closedAt)) {
                    return $openedAt->diffInSeconds($closedAt);
                }
            } catch (\Throwable $e) {
            }
        }

        if (!is_null($log->durasi_menit)) {
            return max(0, (int) $log->durasi_menit) * 60;
        }

        foreach (['durasi_detik', 'durasi'] as $key) {
            if (array_key_exists($key, $detail)) {
                $seconds = $this->parseDurationValueToSeconds($detail[$key]);
                if (!is_null($seconds)) {
                    return $seconds;
                }
            }
        }

        return null;
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
}
