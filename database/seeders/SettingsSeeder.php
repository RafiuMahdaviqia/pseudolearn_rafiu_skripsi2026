<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        Setting::setValue('app.title', 'PseudoLearn');
        Setting::setValue('nyawa.max', '100');
        Setting::setValue('chatbot.enabled', '1');
        Setting::setValue('maintenance_mahasiswa', '0');
    }
}
