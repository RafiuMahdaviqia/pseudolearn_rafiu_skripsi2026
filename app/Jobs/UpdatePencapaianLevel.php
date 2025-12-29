<?php

namespace App\Jobs;

use App\Models\Mahasiswa;
use App\Models\Pencapaian;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdatePencapaianLevel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $levelId;
    protected string $imagePath;
    protected ?string $kelasId;

    /**
     * @param string $levelId   ID level yang diupdate
     * @param string $imagePath Path image terbaru level (relative path)
     * @param string|null $kelasId Batasi update hanya untuk mahasiswa pada kelas ini (opsional)
     */
    public function __construct(string $levelId, string $imagePath, ?string $kelasId = null)
    {
        $this->levelId = $levelId;
        $this->imagePath = $imagePath;
        $this->kelasId = $kelasId;
    }

    public function handle(): void
    {
        try {
            // Query dasar: pencapaian untuk level terkait
            $query = Pencapaian::where('id_level', $this->levelId)->whereIn('category', ['soal', 'konversi']);

            // Jika kelasId diberikan, filter berdasarkan mahasiswa pada kelas tsb
            if (!empty($this->kelasId)) {
                $mahasiswaIds = Mahasiswa::where('id_kelas', $this->kelasId)->pluck('id');
                if ($mahasiswaIds->isEmpty()) {
                    return;
                }
                $query->whereIn('id_mahasiswa', $mahasiswaIds);
            }

            // Update massal kolom img
            $query->update(['img' => $this->imagePath]);
        } catch (\Throwable $e) {
            Log::error('UpdatePencapaianLevel job failed', [
                'level_id' => $this->levelId,
                'kelas_id' => $this->kelasId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}