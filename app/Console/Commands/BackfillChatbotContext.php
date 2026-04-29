<?php

namespace App\Console\Commands;

use App\Models\ChatbotAccessLog;
use App\Models\ChatbotLog;
use App\Models\Soal;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class BackfillChatbotContext extends Command
{
    protected $signature = 'chatbot:backfill-context
                            {--apply : Persist updates to database (default dry-run)}
                            {--limit=0 : Max rows processed per phase (0 = all)}
                            {--single-soal-level : Fill id_soal if a level has exactly one soal}';

    protected $description = 'Backfill chatbot access/log context to improve detail precision';

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');
        $limit = max(0, (int) $this->option('limit'));
        $singleSoalLevel = (bool) $this->option('single-soal-level');

        $this->info('=== Chatbot Context Backfill ===');
        $this->line('Mode: ' . ($apply ? 'APPLY' : 'DRY-RUN'));
        $this->line('Limit per phase: ' . ($limit > 0 ? $limit : 'ALL'));
        $this->line('Single-soal-per-level fallback: ' . ($singleSoalLevel ? 'ON' : 'OFF'));

        $phase1 = $this->backfillChatLogAccessId($apply, $limit);
        $phase2 = $this->backfillAccessLogContext($apply, $limit, $singleSoalLevel);

        $this->newLine();
        $this->info('--- Summary ---');
        $this->line('Phase 1 (chatbot_logs.access_id):');
        $this->line('  scanned   : ' . $phase1['scanned']);
        $this->line('  linked    : ' . $phase1['linked']);
        $this->line('  ambiguous : ' . $phase1['ambiguous']);
        $this->line('  no_match  : ' . $phase1['no_match']);

        $this->line('Phase 2 (chatbot_access_logs.id_level/id_soal):');
        $this->line('  scanned         : ' . $phase2['scanned']);
        $this->line('  updated_rows    : ' . $phase2['updated_rows']);
        $this->line('  level_filled    : ' . $phase2['level_filled']);
        $this->line('  soal_filled     : ' . $phase2['soal_filled']);
        $this->line('  still_incomplete: ' . $phase2['still_incomplete']);

        if (!$apply) {
            $this->comment('Dry-run selesai. Jalankan ulang dengan --apply untuk menyimpan perubahan.');
        }

        return self::SUCCESS;
    }

    private function backfillChatLogAccessId(bool $apply, int $limit): array
    {
        $query = ChatbotLog::whereNull('access_id')->orderBy('created_at', 'asc');
        if ($limit > 0) {
            $query->limit($limit);
        }

        $chatLogs = $query->get(['id', 'id_mahasiswa', 'type', 'created_at', 'access_id']);

        $stats = [
            'scanned' => 0,
            'linked' => 0,
            'ambiguous' => 0,
            'no_match' => 0,
        ];

        foreach ($chatLogs as $chatLog) {
            $stats['scanned']++;
            $candidates = $this->findAccessCandidatesForChat($chatLog);
            $count = $candidates->count();

            if ($count === 1) {
                $stats['linked']++;
                if ($apply) {
                    $chatLog->access_id = $candidates->first()->id;
                    $chatLog->save();
                }
                continue;
            }

            if ($count === 0) {
                $stats['no_match']++;
            } else {
                $stats['ambiguous']++;
            }
        }

        return $stats;
    }

    private function backfillAccessLogContext(bool $apply, int $limit, bool $singleSoalLevel): array
    {
        $query = ChatbotAccessLog::where(function ($q) {
            $q->whereNull('id_level')->orWhereNull('id_soal');
        })->orderBy('opened_at', 'asc');

        if ($limit > 0) {
            $query->limit($limit);
        }

        $accessLogs = $query->get(['id', 'id_mahasiswa', 'type', 'opened_at', 'closed_at', 'id_level', 'id_soal']);

        $stats = [
            'scanned' => 0,
            'updated_rows' => 0,
            'level_filled' => 0,
            'soal_filled' => 0,
            'still_incomplete' => 0,
        ];

        $singleSoalByLevelCache = [];

        foreach ($accessLogs as $accessLog) {
            $stats['scanned']++;

            $originalLevel = $accessLog->id_level;
            $originalSoal = $accessLog->id_soal;

            $resolved = $this->resolveContextForAccess($accessLog, $singleSoalLevel, $singleSoalByLevelCache);

            if (empty($accessLog->id_level) && !empty($resolved['id_level'])) {
                $accessLog->id_level = $resolved['id_level'];
            }

            if (empty($accessLog->id_soal) && !empty($resolved['id_soal'])) {
                $accessLog->id_soal = $resolved['id_soal'];
            }

            $levelChanged = empty($originalLevel) && !empty($accessLog->id_level);
            $soalChanged = empty($originalSoal) && !empty($accessLog->id_soal);

            if ($levelChanged || $soalChanged) {
                $stats['updated_rows']++;
                if ($levelChanged) {
                    $stats['level_filled']++;
                }
                if ($soalChanged) {
                    $stats['soal_filled']++;
                }

                if ($apply) {
                    $accessLog->save();
                }
            }

            if (empty($accessLog->id_level) || empty($accessLog->id_soal)) {
                $stats['still_incomplete']++;
            }
        }

        return $stats;
    }

    private function resolveContextForAccess(ChatbotAccessLog $accessLog, bool $singleSoalLevel, array &$singleSoalByLevelCache): array
    {
        $resolvedLevel = null;
        $resolvedSoal = null;

        // 1) From directly linked chat logs by access_id
        $linkedLogs = ChatbotLog::where('access_id', $accessLog->id)
            ->where('id_mahasiswa', $accessLog->id_mahasiswa)
            ->where('type', $accessLog->type)
            ->get(['id_level', 'id_soal']);

        $resolvedLevel = $this->extractUniqueNonNull($linkedLogs->pluck('id_level'));
        $resolvedSoal = $this->extractUniqueNonNull($linkedLogs->pluck('id_soal'));

        // 2) Historical fallback from session window if still missing
        if (empty($resolvedLevel) || empty($resolvedSoal)) {
            $sessionLogs = ChatbotLog::where('id_mahasiswa', $accessLog->id_mahasiswa)
                ->where('type', $accessLog->type);

            if ($accessLog->opened_at && $accessLog->closed_at) {
                $sessionLogs = $sessionLogs->whereBetween('created_at', [$accessLog->opened_at, $accessLog->closed_at]);
            } elseif ($accessLog->opened_at) {
                $openedAt = $accessLog->opened_at->copy();
                $sessionLogs = $sessionLogs
                    ->where('created_at', '>=', $openedAt)
                    ->where('created_at', '<=', $openedAt->copy()->addMinutes(10));
            }

            $sessionLogs = $sessionLogs->get(['id_level', 'id_soal']);

            if (empty($resolvedLevel)) {
                $resolvedLevel = $this->extractUniqueNonNull($sessionLogs->pluck('id_level'));
            }
            if (empty($resolvedSoal)) {
                $resolvedSoal = $this->extractUniqueNonNull($sessionLogs->pluck('id_soal'));
            }
        }

        // 3) Derive level from soal when possible
        if (empty($resolvedLevel) && !empty($resolvedSoal)) {
            $resolvedLevel = Soal::where('id', $resolvedSoal)->value('id_level');
        }

        // 4) Optional deterministic fallback: level with a single soal
        if ($singleSoalLevel && empty($resolvedSoal) && !empty($resolvedLevel)) {
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

    private function findAccessCandidatesForChat(ChatbotLog $chatLog): Collection
    {
        $logTime = $chatLog->created_at;

        $candidates = ChatbotAccessLog::where('id_mahasiswa', $chatLog->id_mahasiswa)
            ->where('type', $chatLog->type)
            ->where('opened_at', '<=', $logTime)
            ->where(function ($q) use ($logTime) {
                $q->where('closed_at', '>=', $logTime)
                    ->orWhereNull('closed_at');
            })
            ->get(['id', 'opened_at', 'closed_at']);

        return $candidates->filter(function (ChatbotAccessLog $accessLog) use ($logTime) {
            if (!is_null($accessLog->closed_at)) {
                return true;
            }

            // Access lama yang belum closed dianggap aktif max 10 menit untuk matching historis
            return $logTime->lessThanOrEqualTo($accessLog->opened_at->copy()->addMinutes(10));
        })->values();
    }

    private function extractUniqueNonNull(Collection $values): ?string
    {
        $unique = $values->filter()->unique()->values();

        return $unique->count() === 1 ? (string) $unique->first() : null;
    }
}
