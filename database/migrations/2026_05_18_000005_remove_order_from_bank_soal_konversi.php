<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_bank_soal_konversi;');

        if (Schema::hasColumn('bank_soal_konversi', 'order')) {
            Schema::table('bank_soal_konversi', function (Blueprint $table) {
                $table->dropColumn('order');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('bank_soal_konversi', 'order')) {
            Schema::table('bank_soal_konversi', function (Blueprint $table) {
                $table->unsignedInteger('order')->nullable();
            });
        }
    }
};