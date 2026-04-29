<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chatbot_access_logs', function (Blueprint $table) {
            $table->uuid('id_level')->nullable()->after('id_mahasiswa');
            $table->uuid('id_soal')->nullable()->after('id_level');

            $table->foreign('id_level')->references('id')->on('level')->nullOnDelete();
            $table->foreign('id_soal')->references('id')->on('soal')->nullOnDelete();
        });

        Schema::table('chatbot_logs', function (Blueprint $table) {
            $table->uuid('access_id')->nullable()->after('id_mahasiswa');
            $table->foreign('access_id')->references('id')->on('chatbot_access_logs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('chatbot_logs', function (Blueprint $table) {
            $table->dropForeign(['access_id']);
            $table->dropColumn('access_id');
        });

        Schema::table('chatbot_access_logs', function (Blueprint $table) {
            $table->dropForeign(['id_level']);
            $table->dropForeign(['id_soal']);
            $table->dropColumn(['id_level', 'id_soal']);
        });
    }
};
