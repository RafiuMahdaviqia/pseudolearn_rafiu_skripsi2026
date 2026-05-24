<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            [
                'id' => '01995dec-678e-70cf-854a-b25e2c2d0d28',
                'name' => 'Tipe Data',
                'image' => 'assets/media/level_image/level_696cfb93a5cd8.png',
                'feedback_data_type' => 'Cek kembali kesalahan pada tipe data int, String, Double, Float, long',
                'feedback_algorithm' => 'Cek kembali kesalahan pada urutan algoritma',
                'order' => 1,
                'manual_active' => 0,
            ],
            [
                'id' => '01995e0c-9825-73b3-b94f-2ae0542eabef',
                'name' => 'Kondisi',
                'image' => 'assets/media/level_image/level_696cfbb088f61.png',
                'feedback_data_type' => 'Cek kembali kesalahan pada tipe data int, String, Double, Float, long',
                'feedback_algorithm' => 'Cek kembali kesalahan pada urutan algoritma',
                'order' => 2,
                'manual_active' => 0,
            ],
            [
                'id' => '01995e0d-85fe-7041-8c7e-cdfbc81bf1ed',
                'name' => 'Perulangan',
                'image' => 'assets/media/level_image/level_696cfbc71833b.png',
                'feedback_data_type' => 'Cek kembali kesalahan pada tipe data int, String, Double, Float, long',
                'feedback_algorithm' => 'Cek kembali kesalahan pada urutan algoritma',
                'order' => 3,
                'manual_active' => 0,
            ],
            [
                'id' => '01995e12-4580-7361-b0d1-379bdea0b2b6',
                'name' => 'Fungsi',
                'image' => 'assets/media/level_image/level_696cfbdbcdcfe.png',
                'feedback_data_type' => 'Cek kembali kesalahan pada tipe data int, String, Double, Float, long',
                'feedback_algorithm' => 'Cek kembali kesalahan pada urutan algoritma',
                'order' => 4,
                'manual_active' => 0,
            ],
            [
                'id' => '01995e13-29af-7010-8995-1a40e4504851',
                'name' => 'Array 1',
                'image' => 'assets/media/level_image/level_696cfbebd15c6.png',
                'feedback_data_type' => 'Cek kembali kesalahan pada tipe data int, String, Double, Float, long',
                'feedback_algorithm' => 'Cek kembali kesalahan pada urutan algoritma',
                'order' => 5,
                'manual_active' => 0,
            ],
            [
                'id' => '01996adc-c3ca-712b-abc0-933c691ccfc8',
                'name' => 'Array 2',
                'image' => 'assets/media/level_image/level_696cfc0852e19.png',
                'feedback_data_type' => 'Cek kembali kesalahan pada tipe data int, String, Double, Float, long',
                'feedback_algorithm' => 'Cek kembali kesalahan pada urutan algoritma',
                'order' => 6,
                'manual_active' => 0,
            ],
            [
                'id' => '01985f44-f662-72f9-a85b-a7b256942492',
                'name' => 'Stack',
                'image' => 'assets/media/level_image/level_688b13cbbc675.png',
                'feedback_data_type' => 'Cek kembali kesalahan pada tipe data int, String, Double, Float, long',
                'feedback_algorithm' => 'Cek kembali kesalahan pada urutan algoritma',
                'order' => 7,
                'manual_active' => 0,
            ],
            [
                'id' => '019863c4-59f9-7319-9104-08267fc3c551', // Tetap mempertahankan UUID penentu Queue
                'name' => 'Queue',
                'image' => 'assets/media/level_image/level_68a69ecb76297.png',
                'feedback_data_type' => 'Cek kembali kesalahan pada tipe data int, String, Double, Float, long',
                'feedback_algorithm' => 'Cek kembali kesalahan pada urutan algoritma',
                'order' => 8,
                'manual_active' => 0,
            ],
            [
                'id' => '01995e17-eb90-721f-92c5-3ce5162cedfd',
                'name' => 'Sorting',
                'image' => 'assets/media/level_image/level_696cfc29a2fea.png',
                'feedback_data_type' => 'Cek kembali kesalahan pada tipe data int, String, Double, Float, long',
                'feedback_algorithm' => 'Cek kembali kesalahan pada urutan algoritma',
                'order' => 9,
                'manual_active' => 0,
            ],
            [
                'id' => '01995e18-9100-7037-9f92-b6491990b048',
                'name' => 'Searching',
                'image' => 'assets/media/level_image/level_696cfd336aeb4.png',
                'feedback_data_type' => 'Cek kembali kesalahan pada tipe data int, String, Double, Float, long',
                'feedback_algorithm' => 'Cek kembali kesalahan pada urutan algoritma',
                'order' => 10,
                'manual_active' => 0,
            ],
            [
                'id' => '019de356-abfa-717d-958c-e9311c2712f3',
                'name' => 'Linked List',
                'image' => 'assets/media/level_image/level_69f4910fda4ac.png',
                'feedback_data_type' => 'Cek kembali kesalahan pada tipe data int, String, Double, Float, long',
                'feedback_algorithm' => 'Cek kembali kesalahan pada urutan algoritma',
                'order' => 11,
                'manual_active' => 0,
            ],
        ];

        foreach ($levels as $level) {
            Level::query()->updateOrCreate(
                ['id' => $level['id']], // Kunci pencarian berbasis UUID induk
                [
                    'name' => $level['name'],
                    'image' => $level['image'],
                    'feedback_data_type' => $level['feedback_data_type'],
                    'feedback_algorithm' => $level['feedback_algorithm'],
                    'order' => $level['order'],
                    'manual_active' => $level['manual_active'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}