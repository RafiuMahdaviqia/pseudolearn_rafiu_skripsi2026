<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Nyawa;
use Illuminate\Console\Command;

class ResetNyawaDaily extends Command
{
    protected $signature = 'nyawa:reset-daily';
    protected $description = 'Reset nyawa semua user ke max_nyawa setiap hari';

    public function handle()
    {
        $count = Nyawa::query()->update([
            'nyawa' => \DB::raw('max_nyawa'),
            'next_regen_at' => null,
        ]);

        $this->info("Reset nyawa selesai untuk {$count} user.");
    }
}
