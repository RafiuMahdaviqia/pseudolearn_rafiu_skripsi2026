<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Menggunakan DB::table untuk insert agar lebih cepat dan persis dengan SQL
        DB::table('users')->insert([
            [
                'id' => '55a2443f-37ef-4daa-ab66-3019315440c9',
                'name' => 'Dosen Admin',
                'email' => 'dosen@gmail.com',
                'email_verified_at' => null,
                'avatar' => null, // Dibiarkan null sesuai struktur
                'password' => bcrypt('password'), // Menggunakan bcrypt untuk hash password
                'is_admin' => 1,
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'id' => 'ebd7893f-a211-415b-a20a-21b712d30aca',
                'name' => 'Mahasiswa User',
                'email' => 'mahasiswa@gmail.com',
                'email_verified_at' => null,
                'avatar' => null, // Dibiarkan null sesuai struktur
                'password' => bcrypt('password'), // Menggunakan bcrypt untuk hash password
                'is_admin' => 0,
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ]
        ]);
    }
}