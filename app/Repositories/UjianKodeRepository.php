<?php

namespace App\Repositories;

use App\Models\UjianKode;
use App\Models\BankSoalKonversi;
use Illuminate\Support\Facades\Auth;

class UjianKodeRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new UjianKode();
    }

    public function submitKonversi($request)
    {
        $idUser             = Auth::id();
        $idBankSoalKonversi = $request->input('id_soal_konversi');
        $kodeLangkah        = $request->input('kode_langkah', []);
        $waktu              = $request->input('waktu', 0);

        // Ambil soal kunci dari bank_soal_konversi
        $soalKonversi = BankSoalKonversi::find($idBankSoalKonversi);
        if (!$soalKonversi) {
            return response()->json([
                'success' => false,
                'message' => 'Soal tidak ditemukan.'
            ], 404);
        }

        // Jawaban kunci — plain text dipecah per baris
        $kunciJawaban = array_values(
            array_filter(
                array_map('trim', explode("\n", $soalKonversi->jawaban))
            )
        );

        // Jawaban mahasiswa — dari drag & drop
        $jawabanMahasiswa = array_map('trim', $kodeLangkah);

        // Validasi per langkah
        $errors = [];
        foreach ($jawabanMahasiswa as $index => $jawaban) {
            if ($jawaban !== ($kunciJawaban[$index] ?? null)) {
                $errors[] = ['index' => $index];
            }
        }

        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'message' => [
                    'message' => 'Terdapat jawaban salah',
                    'errors'  => $errors,
                ]
            ], 422);
        }

        // Simpan hasil ujian
        $this->model->create([
            'id_mahasiswa'          => $idUser,
            'id_bank_soal_konversi' => $idBankSoalKonversi,
            'id_level'              => $soalKonversi->id_level,
            'jawaban'               => implode("\n", $jawabanMahasiswa),
            'output'                => $soalKonversi->output,
            'nilai'                 => 100,
            'waktu'                 => $waktu,
        ]);

        return response()->json([
            'success'     => true,
            'java_output' => $soalKonversi->output,
            'konversi'    => [
                'id' => $soalKonversi->id,
            ],
        ]);
    }
}
