<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Level;
use App\Models\Mahasiswa;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Jobs\GeneratePencapaianMahasiswa;
use Maatwebsite\Excel\Concerns\ToCollection;

class MahasiswaImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $usersData = [];
        $mahasiswaData = [];
        $now = now();

        foreach ($rows->slice(1) as $row) { // mulai dari row ke-2
            // Jika semua kolom null, hentikan loop
            if ($row[0] === null && $row[1] === null && $row[2] === null && $row[3] === null && $row[4] === null) {
                break;
            }

            // Cek apakah NIM sudah ada di database
            if (Mahasiswa::where('nim', $row[0])->exists()) {
                continue; // Lewati jika NIM sudah ada
            }

            $cekKelas = Kelas::whereRaw('LOWER(name) = ?', [strtolower($row[2])])
                                ->where('angkatan', $row[3])
                                ->first();

            if (!$cekKelas) {
                $insertData = [
                    'name' => $row[2],
                    'angkatan' => $row[3],
                ];
                $kelasBaru = Kelas::create($insertData);
                $kelasId = $kelasBaru->id;
            } else {
                $kelasId = $cekKelas->id;
            }

            // Ambil jenis kelamin dari kolom ke-4, jika ada
            $jenisKelamin = isset($row[4]) ? strtolower($row[4]) : null;

            if ($jenisKelamin === 'laki-laki' || $jenisKelamin === 'l' || $jenisKelamin === 'laki-laki' || $jenisKelamin === 'L') {
                $jenisKelamin = 'l';
            } elseif ($jenisKelamin === 'perempuan' || $jenisKelamin === 'p' || $jenisKelamin === 'perempuan' || $jenisKelamin === 'P') {
                $jenisKelamin = 'p';
            } else {
                $jenisKelamin = null;
            }

            $userId = (string) Str::uuid();

            $usersData[] = [
                'id' => $userId,
                'name' => $row[1],
                'email' => "{$row[0]}@gmail.com",
                'email_verified_at' => null,
                'password' => Hash::make($row[0]),
                'is_admin' => 0,
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ];

            $mahasiswaData[] = [
                'id' => (string) Str::uuid(),
                'id_user' => $userId,
                'id_kelas' => $kelasId,
                'nim' => $row[0],
                'name' => $row[1],
                'jenis_kelamin' => $jenisKelamin,
                'open_panduan' => 0, // default belum dibuka
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ];
        }

        DB::beginTransaction();
        try {
            User::insert($usersData);
            Mahasiswa::insert($mahasiswaData);
            DB::commit();

            foreach ($mahasiswaData as $mahasiswa) {
                // Dispatch job per Level
                $levelList = Level::all();
                foreach ($levelList as $level) {
                    GeneratePencapaianMahasiswa::dispatch($mahasiswa, $level->id);
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
