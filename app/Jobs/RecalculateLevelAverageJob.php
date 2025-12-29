<?php

namespace App\Jobs;

use App\Models\LabelSkor;
use App\Models\Mahasiswa;
use App\Models\Soal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecalculateLevelAverageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $levelId;

    public function __construct(string $levelId)
    {
        $this->levelId = $levelId;
    }

    public function handle(): void
    {
        try {
            // Bersihkan duplikat avg row dulu
            $this->removeDuplicateAverageRows();

            $activeSoalIds = Soal::where('id_level', $this->levelId)
                ->where('status', 1)
                ->pluck('id')
                ->toArray();

            if (count($activeSoalIds) === 0) {
                LabelSkor::where('id_level', $this->levelId)
                    ->whereNull('id_soal')
                    ->whereNull('label')
                    ->delete();
                return;
            }

            $activeSoalCount = count($activeSoalIds);
            $mahasiswaList = Mahasiswa::select('id')->get();

            DB::transaction(function () use ($mahasiswaList, $activeSoalIds, $activeSoalCount) {
                foreach ($mahasiswaList as $mhs) {
                    $mId = $mhs->id;

                    $skorRows = LabelSkor::where('id_level', $this->levelId)
                        ->whereIn('id_soal', $activeSoalIds)
                        ->where('id_mahasiswa', $mId)
                        ->get();

                    $uniqueSoalScored = $skorRows->pluck('id_soal')->unique()->count();

                    if ($uniqueSoalScored === $activeSoalCount && $uniqueSoalScored > 0) {
                        $total = $skorRows->sum('skor');
                        $avg = $total / $uniqueSoalScored;
                    } else {
                        $avg = 0;
                    }

                    // Ambil 1 row average (jika ada). Kalau ada duplikat tersisa (edge) tetap dibersihkan.
                    $avgRows = LabelSkor::where('id_level', $this->levelId)
                        ->where('id_mahasiswa', $mId)
                        ->whereNull('id_soal')
                        ->whereNull('label')
                        ->orderBy('updated_at', 'desc')
                        ->get();

                    if ($avgRows->count() > 1) {
                        // Sisakan yang terbaru
                        $rowsToDelete = $avgRows->slice(1)->pluck('id');
                        LabelSkor::whereIn('id', $rowsToDelete)->delete();
                    }

                    $averageRow = $avgRows->first();

                    if ($averageRow) {
                        $averageRow->skor = $avg;
                        $averageRow->updated_at = now();
                        $averageRow->save();
                    } else {
                        LabelSkor::create([
                            'id'           => (string) \Illuminate\Support\Str::uuid(),
                            'id_level'     => $this->levelId,
                            'id_soal'      => null,
                            'id_mahasiswa' => $mId,
                            'label'        => null,
                            'skor'         => $avg,
                            'created_at'   => now(),
                            'updated_at'   => now(),
                        ]);
                    }
                }
            });

        } catch (\Throwable $e) {
            Log::error('RecalculateLevelAverageJob error: '.$e->getMessage(), [
                'level_id' => $this->levelId,
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Hapus duplikat avg row (id_soal NULL, label NULL) per mahasiswa di level ini,
     * sisakan yang paling baru (updated_at terbaru).
     */
    protected function removeDuplicateAverageRows(): void
    {
        // Ambil grup yang duplikat
        $duplicateGroups = LabelSkor::select('id_mahasiswa', DB::raw('COUNT(*) as cnt'))
            ->where('id_level', $this->levelId)
            ->whereNull('id_soal')
            ->whereNull('label')
            ->groupBy('id_mahasiswa')
            ->having('cnt', '>', 1)
            ->get();

        foreach ($duplicateGroups as $group) {
            $rows = LabelSkor::where('id_level', $this->levelId)
                ->where('id_mahasiswa', $group->id_mahasiswa)
                ->whereNull('id_soal')
                ->whereNull('label')
                ->orderBy('updated_at', 'desc')
                ->get();

            // Sisakan satu (terbaru), hapus sisanya
            $toDelete = $rows->slice(1)->pluck('id');
            if ($toDelete->isNotEmpty()) {
                LabelSkor::whereIn('id', $toDelete)->delete();
            }
        }
    }
}