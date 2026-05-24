<?php

use Database\Seeders\Support\QueueSoalKunciDefaults;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const LEVEL_ID = '019863c4-59f9-7319-9104-08267fc3c551';

    public function up(): void
    {
        $tipeData = json_encode(QueueSoalKunciDefaults::tipeData());
        $algoritma = json_encode(QueueSoalKunciDefaults::algoritma());

        foreach (QueueSoalKunciDefaults::judulLevel1() as $judul) {
            DB::table('soal')
                ->where('id_level', self::LEVEL_ID)
                ->where('judul', $judul)
                ->where(function ($query) {
                    $query->whereNull('kunci_tipe_data')
                        ->orWhereNull('kunci_algoritma')
                        ->orWhere('kunci_tipe_data', 'null')
                        ->orWhere('kunci_algoritma', 'null');
                })
                ->update([
                    'kunci_tipe_data' => $tipeData,
                    'kunci_algoritma' => $algoritma,
                    'soal' => QueueSoalKunciDefaults::deskripsi($judul),
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        // Tidak di-revert: konten kunci tidak bisa dipulihkan otomatis.
    }
};
