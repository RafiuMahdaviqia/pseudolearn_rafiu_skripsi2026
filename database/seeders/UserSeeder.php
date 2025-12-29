<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Dosen/Admin
        User::create([
            'id' => Str::uuid(),
            'name' => 'Dosen Admin',
            'email' => 'admin@mail.com',
            'password' => Hash::make('password'), // Ganti sesuai kebutuhan
            'is_admin' => 1,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);

        // Mahasiswa
        User::create([
            'id' => Str::uuid(),
            'name' => 'Mahasiswa User',
            'email' => 'mahasiswa@mail.com',
            'password' => Hash::make('password'), // Ganti sesuai kebutuhan
            'is_admin' => 0,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);
    }
}
