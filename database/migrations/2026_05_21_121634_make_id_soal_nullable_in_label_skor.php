<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop duplicate foreign keys on id_soal (may already be dropped)
        try { DB::statement('ALTER TABLE label_skor DROP FOREIGN KEY label_skor_id_soal_foreign'); } catch (\Exception $e) {}
        try { DB::statement('ALTER TABLE label_skor DROP FOREIGN KEY label_skor_ibfk_1'); } catch (\Exception $e) {}
        // Make id_soal nullable
        DB::statement('ALTER TABLE label_skor MODIFY id_soal CHAR(36) NULL');
        // Re-add single foreign key
        DB::statement('ALTER TABLE label_skor ADD CONSTRAINT label_skor_id_soal_foreign FOREIGN KEY (id_soal) REFERENCES soal(id) ON DELETE CASCADE');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE label_skor DROP FOREIGN KEY label_skor_id_soal_foreign');
        DB::statement('ALTER TABLE label_skor MODIFY id_soal CHAR(36) NOT NULL');
        DB::statement('ALTER TABLE label_skor ADD CONSTRAINT label_skor_id_soal_foreign FOREIGN KEY (id_soal) REFERENCES soal(id) ON DELETE CASCADE');
    }
};
