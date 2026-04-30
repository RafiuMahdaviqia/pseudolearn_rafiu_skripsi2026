<?php

namespace App\Http\Controllers\Quiz;

use App\Enums\ClusterLabel;
use App\Enums\SoalDifficulty;
use App\Http\Controllers\Controller;
use App\Models\Konversi;
use App\Models\LabelSkor;
use App\Models\Level;
use App\Models\Mahasiswa;
use App\Models\Nyawa;
use App\Models\Soal;
use App\Models\Ujian;
use App\Models\UjianKonversi;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Reference-only implementation.
 *
 * Goal:
 * - Keep the same payload shape used by the existing question-list view.
 * - Demonstrate cleaner structure, bulk queries, and clearer separation of concerns.
 *
 * Notes:
 * - This file is not wired to routes by default.
 * - Use it as a blueprint when adding new features to the real controller.
 */
class QuestionListRefactorReferenceController extends Controller
{
    public function questionList(Request $request): View
    {
        $validated = $request->validate([
            'level' => ['required'],
        ]);

        $level = Level::query()->find($validated['level']);
        abort_if($level === null, 404, 'Level not found.');

        $user = Auth::user();
        abort_if($user === null, 401);

        $mahasiswaId = Mahasiswa::query()
            ->where('id_user', $user->id)
            ->value('id');

        abort_if($mahasiswaId === null, 404, 'Mahasiswa not found.');

        $targetDifficulty = $this->resolveTargetDifficulty($level->id, (string) $mahasiswaId);
        $soalLimit = $this->resolveSoalLimit($level);

        $dataSoal = $this->fetchServedSoal($level->id, $targetDifficulty, $soalLimit);
        if ($dataSoal === []) {
            // Fallback bila tidak ada soal pada difficulty hasil adaptasi.
            $dataSoal = $this->fetchServedSoal($level->id, null, $soalLimit);
        }

        $servedSoalIds = array_column($dataSoal, 'id');
        $dataKonversi = $this->fetchActiveKonversiBySoalIds($servedSoalIds);

        $ujianBySoal = $this->fetchUjianMap((string) $mahasiswaId, $level->id, $servedSoalIds);
        [$konversiBySoal, $konversiById] = $this->buildKonversiMaps($dataKonversi, (string) $mahasiswaId, $level->id);
        $badgeBySoal = $this->fetchBadgeMap((string) $mahasiswaId, $level->id, $servedSoalIds);

        $result = $this->buildQuestionRows($dataSoal, $konversiBySoal, $ujianBySoal, $badgeBySoal);
        $result = $this->normalizeProgressStatus($result);

        $algopoin = $this->fetchAlgoPoin((string) $mahasiswaId, $level->id);
        $nilaiKonversiList = $this->fetchNilaiKonversiList((string) $mahasiswaId, $level->id, $konversiById);

        $dataLevel = $level;
        $jumlahSoalKonversi = count($dataKonversi);

        $nyawa = Nyawa::query()->where('id_user', $user->id)->first();
        if ($nyawa) {
            $nyawa->checkAndRegenerate();
        }

        return view('pages.quiz.question-list', [
            'title' => 'List Soal',
            'dataSoal' => $result,
            'algopoin' => $algopoin,
            'levelId' => $level->id,
            'nilaiKonversiList' => $nilaiKonversiList,
            'dataLevel' => $dataLevel,
            'jumlahSoalKonversi' => $jumlahSoalKonversi,
            'lives' => $nyawa?->nyawa ?? 0,
            'max_lives' => $nyawa?->max_nyawa ?? 0,
            'next_regen_at' => $nyawa?->next_regen_at,
        ]);
    }

    private function resolveSoalLimit(Level $level): ?int
    {
        $limit = intval($level->jumlah_soal ?? 0);

        return $limit > 0 ? $limit : null;
    }

    private function resolveTargetDifficulty(string $levelId, string $mahasiswaId): SoalDifficulty
    {
        $latestUjian = Ujian::query()
            ->where('id_mahasiswa', $mahasiswaId)
            ->where('id_level', $levelId)
            ->whereNotNull('id_soal')
            ->orderBy('created_at', 'desc')
            ->first(['id_soal']);

        if ($latestUjian === null || empty($latestUjian->id_soal)) {
            return SoalDifficulty::EASY;
        }

        $latestSoalDifficulty = Soal::query()
            ->whereKey($latestUjian->id_soal)
            ->value('difficulty');

        $currentDifficulty = SoalDifficulty::tryFrom((string) $latestSoalDifficulty) ?? SoalDifficulty::EASY;

        $latestLabel = LabelSkor::query()
            ->where('id_mahasiswa', $mahasiswaId)
            ->where('id_level', $levelId)
            ->where('id_soal', $latestUjian->id_soal)
            ->whereNotNull('label')
            ->orderBy('created_at', 'desc')
            ->value('label');

        $clusterLabel = ClusterLabel::tryFrom((string) $latestLabel);

        if ($clusterLabel === null) {
            return $currentDifficulty;
        }

        $nextDifficultyIndex = match ($clusterLabel) {
            ClusterLabel::IDEAL, ClusterLabel::NORMAL => $currentDifficulty->index() + 1,
            ClusterLabel::STRUGGLING, ClusterLabel::GAMING_THE_SYSTEM => $currentDifficulty->index(),
        };

        return SoalDifficulty::fromIndex($nextDifficultyIndex) ?? $currentDifficulty;
    }

    private function fetchServedSoal(string $levelId, ?SoalDifficulty $difficulty, ?int $limit): array
    {
        $query = Soal::query()
            ->where('id_level', $levelId)
            ->where('status', 1)
            ->orderBy('order', 'asc');

        if ($difficulty !== null) {
            $query->where('difficulty', $difficulty->value);
        }

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get()->toArray();
    }

    private function fetchActiveKonversiBySoalIds(array $soalIds): array
    {
        if ($soalIds === []) {
            return [];
        }

        return Konversi::query()
            ->with('soal:id,judul')
            ->whereIn('id_soal', $soalIds)
            ->get()
            ->toArray();
    }

    private function fetchUjianMap(string $mahasiswaId, string $levelId, array $soalIds): array
    {
        if ($soalIds === []) {
            return [];
        }

        $doneSoalIds = Ujian::query()
            ->where('id_mahasiswa', $mahasiswaId)
            ->where('id_level', $levelId)
            ->where('status', 1)
            ->whereIn('id_soal', $soalIds)
            ->pluck('id_soal')
            ->all();

        if ($doneSoalIds === []) {
            return [];
        }

        return array_fill_keys($doneSoalIds, true);
    }

    /**
     * @return array{0: array<int|string, array<string, mixed>>, 1: array<int|string, array<string, mixed>>}
     */
    private function buildKonversiMaps(array $dataKonversi, string $mahasiswaId, string $levelId): array
    {
        $konversiIds = array_values(array_unique(array_column($dataKonversi, 'id')));
        $ujianKonversiById = [];

        if ($konversiIds !== []) {
            // Ambil percobaan terakhir per konversi dengan urutan ascending, sehingga key terakhir jadi data terbaru.
            $ujianKonversiById = UjianKonversi::query()
                ->where('id_mahasiswa', $mahasiswaId)
                ->where('id_level', $levelId)
                ->whereIn('id_soal_konversi', $konversiIds)
                ->orderBy('created_at', 'asc')
                ->get()
                ->keyBy('id_soal_konversi')
                ->map(fn ($item) => $item->toArray())
                ->toArray();
        }

        $konversiBySoal = [];
        $konversiById = [];
        foreach ($dataKonversi as $konversi) {
            $konversi['ujianKonversi'] = $ujianKonversiById[$konversi['id']] ?? null;
            $konversiBySoal[$konversi['id_soal']] = $konversi;
            $konversiById[$konversi['id']] = $konversi;
        }

        return [$konversiBySoal, $konversiById];
    }

    private function fetchBadgeMap(string $mahasiswaId, string $levelId, array $soalIds): array
    {
        if ($soalIds === []) {
            return [];
        }

        return LabelSkor::query()
            ->where('id_mahasiswa', $mahasiswaId)
            ->where('id_level', $levelId)
            ->whereIn('id_soal', $soalIds)
            ->pluck('label', 'id_soal')
            ->toArray();
    }

    private function buildQuestionRows(array $dataSoal, array $konversiBySoal, array $ujianBySoal, array $badgeBySoal): array
    {
        $rows = [];

        foreach ($dataSoal as $soal) {
            $soalId = $soal['id'];

            $soalEntry = $soal;
            $soalEntry['type'] = 'soal';
            $soalEntry['konversi'] = $konversiBySoal[$soalId] ?? null;
            $soalEntry['ujianKonversi'] = null;
            $soalEntry['status'] = isset($ujianBySoal[$soalId]) ? 'done' : 'locked';
            $soalEntry['badge'] = $badgeBySoal[$soalId] ?? null;
            $rows[] = $soalEntry;

            if (isset($konversiBySoal[$soalId])) {
                $konversi = $konversiBySoal[$soalId];
                $konversiEntry = $konversi;
                $konversiEntry['type'] = 'konversi';
                $konversiEntry['soal'] = $soal;
                $konversiEntry['badge'] = null;
                $konversiEntry['status'] = $konversi['ujianKonversi'] ? 'done' : 'locked';
                $rows[] = $konversiEntry;
            }
        }

        return $rows;
    }

    private function normalizeProgressStatus(array $rows): array
    {
        $firstActiveSet = false;

        foreach ($rows as $index => $row) {
            if ($row['status'] === 'done') {
                continue;
            }

            if (! $firstActiveSet) {
                $rows[$index]['status'] = 'active';
                $firstActiveSet = true;
                continue;
            }

            $rows[$index]['status'] = 'locked';
        }

        return $rows;
    }

    private function fetchAlgoPoin(string $mahasiswaId, string $levelId): int|float
    {
        return LabelSkor::query()
            ->where('id_mahasiswa', $mahasiswaId)
            ->whereNull('id_soal')
            ->whereNull('label')
            ->where('id_level', $levelId)
            ->sum('skor');
    }

    private function fetchNilaiKonversiList(string $mahasiswaId, string $levelId, array $konversiById): array
    {
        $konversiIds = array_keys($konversiById);

        if ($konversiIds === []) {
            return [];
        }

        $nilaiRows = UjianKonversi::query()
            ->where('id_mahasiswa', $mahasiswaId)
            ->where('id_level', $levelId)
            ->whereIn('id_soal_konversi', $konversiIds)
            ->whereNotNull('nilai')
            ->orderBy('created_at', 'asc')
            ->get(['id_soal_konversi', 'nilai']);

        $nilaiKonversiList = [];
        foreach ($nilaiRows as $row) {
            $konversi = $konversiById[$row->id_soal_konversi] ?? null;
            if ($konversi === null) {
                continue;
            }

            $judul = $konversi['soal']['judul'] ?? ($konversi['judul'] ?? ('Konversi ' . $row->id_soal_konversi));
            $nilaiKonversiList[$judul] = $row->nilai;
        }

        return $nilaiKonversiList;
    }
}
