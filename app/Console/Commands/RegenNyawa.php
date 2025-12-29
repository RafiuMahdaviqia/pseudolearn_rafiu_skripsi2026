<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Nyawa;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RegenNyawa extends Command
{
    protected $signature = 'nyawa:regen';
    protected $description = 'Regenerasi nyawa user setiap 10 menit (selalu update next_regen_at meski penuh)';

    public function handle()
    {
        $dataNyawa = Nyawa::all();
        $nowJakarta = Carbon::now();

        foreach ($dataNyawa as $nyawa) {
            $nextRegenAt = $nyawa->next_regen_at;
            // $nextRegenAt = $nextRegenAt->subHours(7);

            if ($nyawa->next_regen_at && $nowJakarta->greaterThanOrEqualTo($nextRegenAt)) {
                // hitung selisih menit antara now dan next_regen_at
                $diffMinutes = $nyawa->next_regen_at->diffInMinutes($nowJakarta);

                // berapa kali siklus 10 menit yg terlewati
                $cycles = floor($diffMinutes / 10) + 1; // +1 untuk regen yg saat ini

                // jumlah nyawa yang akan ditambah
                $addLives = min($cycles, $nyawa->max_nyawa - $nyawa->nyawa);

                if ($addLives > 0) {
                    $nyawa->nyawa += $addLives;

                    // kalau masih belum penuh, set next regen lagi
                    if ($nyawa->nyawa < $nyawa->max_nyawa) {
                        $nyawa->next_regen_at = Carbon::now()->addMinutes(10);
                    } else {
                        $nyawa->next_regen_at = null; // stop kalau penuh
                    }

                    $nyawa->save();
                }

                // Log::info("Nyawa diregen untuk user {$nyawa->id}, total sekarang {$nyawa->nyawa}");

            }
        }

        $this->info("Regen nyawa selesai untuk semua user.");
    }
}
