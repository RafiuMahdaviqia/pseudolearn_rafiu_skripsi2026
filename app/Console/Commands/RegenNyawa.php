<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Nyawa;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RegenNyawa extends Command
{
    protected $signature = 'nyawa:regen';
    protected $description = 'Regenerasi nyawa user 10 per menit (max 100)';

    public function handle()
    {
        $dataNyawa = Nyawa::all();
        $nowJakarta = Carbon::now();

        foreach ($dataNyawa as $nyawa) {
            $nextRegenAt = $nyawa->next_regen_at;

            if ($nyawa->next_regen_at && $nowJakarta->greaterThanOrEqualTo($nextRegenAt)) {
                // hitung selisih menit antara now dan next_regen_at
                $diffMinutes = $nyawa->next_regen_at->diffInMinutes($nowJakarta);

                // berapa kali siklus 1 menit yg terlewati
                $cycles = floor($diffMinutes / 1) + 1; // +1 untuk regen yg saat ini

                // jumlah nyawa yang akan ditambah (10 per siklus)
                $addLives = min($cycles * 10, $nyawa->max_nyawa - $nyawa->nyawa);

                if ($addLives > 0) {
                    $nyawa->nyawa += $addLives;

                    // kalau masih belum penuh, set next regen lagi
                    if ($nyawa->nyawa < $nyawa->max_nyawa) {
                        $nyawa->next_regen_at = Carbon::now()->addMinute();
                    } else {
                        $nyawa->next_regen_at = null; // stop kalau penuh
                    }

                    $nyawa->save();
                }
            }
        }

        $this->info("Regen nyawa selesai untuk semua user.");
    }
}
