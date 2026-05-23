<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First, convert any existing `id_mahasiswa` values that currently store `users.id`
        // into the corresponding `mahasiswa.id` where possible.
        try {
            \Illuminate\Support\Facades\DB::statement(<<<'SQL'
                UPDATE ujian_kode uk
                JOIN mahasiswa m ON uk.id_mahasiswa = m.id_user
                SET uk.id_mahasiswa = m.id
                WHERE uk.id_mahasiswa IS NOT NULL;
            SQL
            );
        } catch (\Exception $e) {
            // ignore; we'll still attempt to add FK and let DB report issues
        }

        // Determine existing foreign constraint name for id_mahasiswa (if present) and drop it
        try {
            $constraint = \Illuminate\Support\Facades\DB::selectOne(<<<'SQL'
                SELECT kcu.CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE kcu
                WHERE kcu.TABLE_SCHEMA = DATABASE()
                  AND kcu.TABLE_NAME = 'ujian_kode'
                  AND kcu.COLUMN_NAME = 'id_mahasiswa'
                  AND kcu.REFERENCED_TABLE_NAME IS NOT NULL
                LIMIT 1;
            SQL
            );

            if (!empty($constraint->CONSTRAINT_NAME)) {
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `ujian_kode` DROP FOREIGN KEY `{$constraint->CONSTRAINT_NAME}`");
            }
        } catch (\Exception $e) {
            // ignore
        }

        Schema::table('ujian_kode', function (Blueprint $table) {
            // Recreate FK to mahasiswa.id
            $table->foreign('id_mahasiswa')
                ->references('id')
                ->on('mahasiswa')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('ujian_kode', function (Blueprint $table) {
            try {
                $table->dropForeign(['id_mahasiswa']);
            } catch (\Exception $e) {
            }

            $table->foreign('id_mahasiswa')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }
};
