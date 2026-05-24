<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
      
    }

    public function down(): void
    {
        // Tidak di-revert: data legacy 'active' tidak bisa dipulihkan otomatis.
    }
};
