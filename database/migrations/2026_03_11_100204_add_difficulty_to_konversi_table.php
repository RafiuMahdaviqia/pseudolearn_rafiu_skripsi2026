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
        Schema::table('konversi', function (Blueprint $table) {
            $table->enum('difficulty', ['easy','medium','hard'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('konversi', 'difficulty')) {
            Schema::table('konversi', function (Blueprint $table) {
                $table->dropColumn('difficulty');
            });
        }
    }
};
