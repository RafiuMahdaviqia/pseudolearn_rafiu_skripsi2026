<?php

use Database\Seeders\Support\QueueSoalKunciDefaults;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    private const DEFAULT_LEVEL_ID = '019863c4-59f9-7319-9104-08267fc3c551';

    private const SOAL_ITEMS = [
        // EASY
        ['judul' => 'Antrian Loket Karcis Bioskop', 'difficulty' => 'easy'],
        ['judul' => 'Antrian Pasien Klinik', 'difficulty' => 'easy'],
        ['judul' => 'Antrian Pengambilan Obat', 'difficulty' => 'easy'],
        ['judul' => 'Loket Bank Belum Buka', 'difficulty' => 'easy'],
        ['judul' => 'Antrian Wahana Taman Bermain', 'difficulty' => 'easy'],

        // MEDIUM
        ['judul' => 'Memanggil Pasien Pertama di Puskesmas', 'difficulty' => 'medium'],
        ['judul' => 'Melayani Seluruh Antrian Kasir Supermarket', 'difficulty' => 'medium'],
        ['judul' => 'Antrian Pendaftaran Lomba Bergantian', 'difficulty' => 'medium'],
        ['judul' => 'Membalik Urutan Antrian Peserta Ujian', 'difficulty' => 'medium'],
        ['judul' => 'Cek Palindrom Plat Nomor Kendaraan', 'difficulty' => 'medium'],

        // HARD
        ['judul' => 'Pencarian Nomor Antrian di Rumah Sakit', 'difficulty' => 'hard'],
        ['judul' => 'Mencari Stok Minimum di Gudang', 'difficulty' => 'hard'],
        ['judul' => 'Menghitung Frekuensi Kehadiran Siswa', 'difficulty' => 'hard'],
        ['judul' => 'Cari Nomor Paket lalu Balik Antrian Pengiriman', 'difficulty' => 'hard'],
        ['judul' => 'Mencari Skor Tertinggi di Antrian Turnamen', 'difficulty' => 'hard'],
    ];

    public function up(): void
    {
        // Ensure the level exists (required by soal_dela.sql)
        $levelExists = DB::table('level')->where('id', self::DEFAULT_LEVEL_ID)->exists();
        if (!$levelExists) {
            DB::table('level')->insert([
                'id' => self::DEFAULT_LEVEL_ID,
                'name' => 'Level 1',
                'jumlah_soal' => 15,
                'image' => null,
                'feedback_data_type' => null,
                'feedback_algorithm' => null,
                'limit_soal' => 5,
                'limit_ars' => 0,
                'order' => 1,
                'manual_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]);
        }

        // Ensure all referenced `soal` rows exist (so subqueries in SQL can resolve)
        foreach (self::SOAL_ITEMS as $index => $item) {
            $exists = DB::table('soal')->where('judul', $item['judul'])->exists();
            if (!$exists) {
                DB::table('soal')->insert([
                    'id' => (string) Str::uuid(),
                    'id_level' => self::DEFAULT_LEVEL_ID,
                    'judul' => $item['judul'],
                    'soal' => QueueSoalKunciDefaults::deskripsi($item['judul']),
                    'kunci_tipe_data' => json_encode(QueueSoalKunciDefaults::tipeData()),
                    'kunci_algoritma' => json_encode(QueueSoalKunciDefaults::algoritma()),
                    'order' => $index + 1,
                    'status' => 1,
                    'difficulty' => $item['difficulty'],
                    'created_at' => now(),
                    'updated_at' => now(),
                    'deleted_at' => null,
                ]);
            }
        }

        $path = database_path('seeders/data/soal_dela.sql');
        $sql = @file_get_contents($path);

        if ($sql === false) {
            throw new RuntimeException("Unable to read SQL file: {$path}");
        }

        DB::unprepared($sql);

        DB::table('bank_soal_konversi')
            ->whereNull('created_at')
            ->orWhereNull('updated_at')
            ->update([
                'created_at' => DB::raw('COALESCE(created_at, NOW())'),
                'updated_at' => DB::raw('COALESCE(updated_at, NOW())'),
            ]);

        // Optional: assign incremental order if the column exists and is empty
        if (Schema::hasColumn('bank_soal_konversi', 'order')) {
            DB::statement('SET @rownum := 0;');
            DB::statement('UPDATE bank_soal_konversi SET `order` = (@rownum := @rownum + 1) WHERE `order` IS NULL ORDER BY created_at ASC;');
        }
    }

    public function down(): void
    {
        // Remove seeded rows (best-effort)
        DB::table('bank_soal_konversi')->where('id_level', self::DEFAULT_LEVEL_ID)->delete();

        foreach (self::SOAL_ITEMS as $item) {
            DB::table('soal')->where('judul', $item['judul'])->delete();
        }

        DB::table('level')->where('id', self::DEFAULT_LEVEL_ID)->delete();
    }
};
