<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    /**
     * NOTE: `soal_dela.sql` assumes this exact UUID exists in `level`.
     */
    private const DEFAULT_LEVEL_ID = '019863c4-59f9-7319-9104-08267fc3c551';

    public function run(): void
    {
        Level::query()->updateOrCreate(
            ['id' => self::DEFAULT_LEVEL_ID],
            [
                'name' => 'Level 1',
                'jumlah_soal' => 15,
                'limit_soal' => 5,
                'limit_ars' => 0,
                'order' => 1,
                'manual_active' => 1,
            ]
        );
    }
}
