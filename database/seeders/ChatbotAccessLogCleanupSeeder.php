<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChatbotAccessLogCleanupSeeder extends Seeder
{
    public function run(): void
    {
        // Keeps local dev DB tidy when reseeding.
        DB::table('chatbot_logs')->delete();
        DB::table('chatbot_access_logs')->delete();
    }
}
