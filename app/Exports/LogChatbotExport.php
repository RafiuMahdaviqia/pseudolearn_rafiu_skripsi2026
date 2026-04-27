<?php

namespace App\Exports;

use App\Models\ChatbotAccessLog;
use App\Models\ChatbotLog;
use App\Models\LogData;
use App\Models\Soal;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LogChatbotExport implements FromCollection, WithHeadings
{
    protected $idKelas;
    protected $idLevel;
    protected $idSoal;

    public function __construct($idKelas = null, $idLevel = null, $idSoal = null)
    {
        $this->idKelas = $idKelas;
        $this->idLevel = $idLevel;
        $this->idSoal = $idSoal;
    }

    public function collection()
    {
        $rows = $this->getSessionRows();

        if ($rows->isEmpty()) {
            return collect();
        }

        $effectiveContextByAccessId = $this->resolveEffectiveContextForRows($rows);
        $sessions = $this->filterRowsByContext($rows, $effectiveContextByAccessId, $this->idLevel, $this->idSoal);

        if (empty($sessions)) {
            return collect();
        }

        $aggregatedRows = $this->aggregateByMahasiswa($sessions);
        $detailLogsByMahasiswa = $this->buildDetailLogTextByMahasiswa($sessions);

        usort($aggregatedRows, function ($a, $b) {
            return ($b['latest_access_ts'] ?? 0) <=> ($a['latest_access_ts'] ?? 0);
        });

        return collect($aggregatedRows)->values()->map(function ($row, $index) use ($detailLogsByMahasiswa) {
            return [
                'no' => $index + 1,
                'nim' => $row['nim'],
                'nama_mahasiswa' => $row['nama_mahasiswa'],
                'kelas' => $row['kelas_name'],
                'total_waktu' => $row['total_waktu'],
                'total_langkah' => $row['total_langkah'],
                'total_durasi' => $row['total_durasi'],
                'detail' => $detailLogsByMahasiswa[$row['id']] ?? 'Tidak ada pesan',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'NIM',
            'Nama Mahasiswa',
            'Kelas',
            'Total Waktu',
            'Total Langkah',
            'Total Durasi',
            'Detail Log Pesan',
        ];
    }

    private function aggregateByMahasiswa(array $sessions): array
    {
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

    private function getSessionRows()
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

        if (!empty($this->idKelas)) {
            $query->where('m.id_kelas', $this->idKelas);
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

    private function buildDetailLogTextByMahasiswa(array $sessions): array
    {
        if (empty($sessions)) {
            return [];
        }

        $sessionByMahasiswa = [];
        $accessIds = [];

        foreach ($sessions as $session) {
            $mahasiswaId = $session['id_mahasiswa'];
            $sessionByMahasiswa[$mahasiswaId][] = $session;

            if (!empty($session['access_id'])) {
                $accessIds[] = $session['access_id'];
            }
        }

        $accessIds = array_values(array_unique(array_filter($accessIds)));

        $logsByAccess = !empty($accessIds)
            ? ChatbotLog::query()
                ->whereIn('access_id', $accessIds)
                ->where('type', 'biasa')
                ->whereNull('deleted_at')
                ->orderBy('created_at', 'asc')
                ->get(['id', 'access_id', 'id_mahasiswa', 'pesan', 'respons', 'created_at'])
                ->groupBy('access_id')
            : collect();

        $mahasiswaIds = array_values(array_unique(array_keys($sessionByMahasiswa)));
        $directLogsQuery = ChatbotLog::query()
            ->whereIn('id_mahasiswa', $mahasiswaIds)
            ->where('type', 'biasa')
            ->whereNull('deleted_at');

        if (!empty($this->idLevel)) {
            $directLogsQuery->where('id_level', $this->idLevel);
        }

        if (!empty($this->idSoal)) {
            $directLogsQuery->where('id_soal', $this->idSoal);
        }

        $directLogsByMahasiswa = $directLogsQuery
            ->orderBy('created_at', 'asc')
            ->get(['id', 'id_mahasiswa', 'pesan', 'respons', 'created_at'])
            ->groupBy('id_mahasiswa');

        $result = [];

        foreach ($sessionByMahasiswa as $mahasiswaId => $mahasiswaSessions) {
            usort($mahasiswaSessions, function ($a, $b) {
                $aTs = $a['opened_at'] ? $a['opened_at']->getTimestamp() : 0;
                $bTs = $b['opened_at'] ? $b['opened_at']->getTimestamp() : 0;

                return $aTs <=> $bTs;
            });

            $lines = [];
            $sessionNumber = 1;

            foreach ($mahasiswaSessions as $session) {
                $sessionLogs = !empty($session['access_id'])
                    ? ($logsByAccess->get($session['access_id']) ?? collect())
                    : collect();

                if ($sessionLogs->isEmpty()) {
                    $sessionLogs = $this->getFallbackSessionLogs($session);
                }

                if ($sessionLogs->isEmpty()) {
                    continue;
                }

                $accessLabel = $session['access_id'] ?? '-';
                $openedAtLabel = $this->formatWib($session['opened_at']);
                $lines[] = 'Sesi ' . $sessionNumber . ' | Access ID: ' . $accessLabel . ' | Waktu Akses: ' . $openedAtLabel;

                foreach ($sessionLogs as $log) {
                    $waktuLog = !empty($log->created_at)
                        ? $this->formatWib($log->created_at)
                        : $openedAtLabel;

                    $pesan = trim((string) ($log->pesan ?? ''));
                    $respons = trim((string) ($log->respons ?? ''));

                    if ($pesan !== '') {
                        $lines[] = '[' . $waktuLog . '] Mahasiswa: ' . $this->normalizeExportText($pesan);
                    }

                    if ($respons !== '') {
                        $lines[] = '[' . $waktuLog . '] Chatbot: ' . $this->normalizeExportText($respons);
                    }
                }

                $lines[] = '';
                $sessionNumber++;
            }

            if (empty($lines)) {
                $directLogs = $directLogsByMahasiswa->get($mahasiswaId, collect());

                if ($directLogs->isNotEmpty()) {
                    $lines[] = 'Riwayat dari Tabel Log Data Chatbot';

                    foreach ($directLogs as $log) {
                        $waktuLog = !empty($log->created_at)
                            ? $this->formatWib($log->created_at)
                            : '-';

                        $pesan = trim((string) ($log->pesan ?? ''));
                        $respons = trim((string) ($log->respons ?? ''));

                        if ($pesan !== '') {
                            $lines[] = '[' . $waktuLog . '] Mahasiswa: ' . $this->normalizeExportText($pesan);
                        }

                        if ($respons !== '') {
                            $lines[] = '[' . $waktuLog . '] Chatbot: ' . $this->normalizeExportText($respons);
                        }
                    }
                }
            }

            if (empty($lines)) {
                $result[$mahasiswaId] = 'Tidak ada pesan';
                continue;
            }

            $text = trim(implode("\n", $lines));
            $text = $this->sanitizeForExcelCell($text);
            $result[$mahasiswaId] = $this->truncateForExcelCell($text);
        }

        return $result;
    }

    private function getFallbackSessionLogs(array $session)
    {
        $query = ChatbotLog::query()
            ->where('id_mahasiswa', $session['id_mahasiswa'])
            ->where('type', 'biasa')
            ->whereNull('deleted_at');

        $openedAt = $session['opened_at'] ?? null;
        $closedAt = $session['closed_at'] ?? null;

        if ($openedAt && $closedAt) {
            $query->whereBetween('created_at', [$openedAt, $closedAt]);
        } elseif ($openedAt) {
            $query->where('created_at', '>=', $openedAt)
                ->where('created_at', '<=', $openedAt->copy()->addMinutes(10));
        }

        return $query->orderBy('created_at', 'asc')
            ->get(['id', 'access_id', 'id_mahasiswa', 'pesan', 'respons', 'created_at']);
    }

    private function normalizeExportText(string $text): string
    {
        return $this->sanitizeForExcelCell($text);
    }

    private function sanitizeForExcelCell(string $text): string
    {
        $normalized = str_replace(["\r\n", "\r"], "\n", $text);
        $normalized = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $normalized) ?? '';
        $normalized = trim($normalized);

        if ($normalized === '') {
            return '';
        }

        $leading = ltrim($normalized);
        $firstChar = $this->strSubstr($leading, 0, 1);

        if (in_array($firstChar, ['=', '+', '-', '@'], true)) {
            return "'" . $normalized;
        }

        return $normalized;
    }

    private function truncateForExcelCell(string $text, int $maxLength = 32000): string
    {
        $suffix = ' ...[dipotong]';

        if ($this->strLength($text) <= $maxLength) {
            return $text;
        }

        $allowed = max(0, $maxLength - $this->strLength($suffix));

        return rtrim($this->strSubstr($text, 0, $allowed)) . $suffix;
    }

    private function strLength(string $text): int
    {
        return function_exists('mb_strlen')
            ? mb_strlen($text)
            : strlen($text);
    }

    private function strSubstr(string $text, int $start, int $length): string
    {
        if (function_exists('mb_substr')) {
            return (string) mb_substr($text, $start, $length);
        }

        return (string) substr($text, $start, $length);
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
}