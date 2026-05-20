<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const LEVEL_ID = '019863c4-59f9-7319-9104-08267fc3c551';

    public function up(): void
    {
        DB::table('level')
            ->where('id', self::LEVEL_ID)
            ->update([
                'name' => 'Queue',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('level')
            ->where('id', self::LEVEL_ID)
            ->update([
                'name' => 'Level 1',
                'updated_at' => now(),
            ]);
    }
};