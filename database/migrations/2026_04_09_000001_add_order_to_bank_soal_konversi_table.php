<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bank_soal_konversi', function (Blueprint $table) {
            if (!Schema::hasColumn('bank_soal_konversi', 'order')) {
                $table->unsignedInteger('order')->default(0)->after('id_soal')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('bank_soal_konversi', function (Blueprint $table) {
            if (Schema::hasColumn('bank_soal_konversi', 'order')) {
                $table->dropColumn('order');
            }
        });
    }
};

