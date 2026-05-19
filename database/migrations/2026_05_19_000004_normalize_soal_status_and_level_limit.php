<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('soal')
            ->whereIn('status', ['active', '1'])
            ->update(['status' => 1]);

        DB::table('soal')
            ->whereIn('status', ['inactive', '0'])
            ->update(['status' => 0]);

        DB::table('level')
            ->whereNull('limit_soal')
            ->update([
                'limit_soal' => DB::raw('COALESCE(jumlah_soal, 5)'),
            ]);
    }

    public function down(): void
    {
        // Tidak di-revert: data legacy 'active' tidak bisa dipulihkan otomatis.
    }
};
