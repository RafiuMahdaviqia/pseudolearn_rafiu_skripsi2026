<?php

namespace App\Repositories;

use App\Models\UjianKode;
use App\Models\BankSoalKonversi;
use App\Models\Nyawa;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Auth;
use App\Models\ArsResult;
use Illuminate\Support\Facades\DB;

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

        // Ambil mahasiswa ID dari user ID
        $idMahasiswa = Mahasiswa::where('id_user', $idUser)->value('id');
        if (!$idMahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan.'
            ], 404);
        }

        // Ambil soal kunci dari bank_soal_konversi
        $soalKonversi = BankSoalKonversi::find($idBankSoalKonversi);
        if (!$soalKonversi) {
            return response()->json([
                'success' => false,
                'message' => 'Soal tidak ditemukan.'
            ], 404);
        }

        $kunciJawaban = BankSoalKonversi::parseJawabanLines($soalKonversi->jawaban);

        if ($kunciJawaban === []) {
            return response()->json([
                'success' => false,
                'message' => 'Kunci jawaban soal tidak valid.',
            ], 422);
        }

        // Jawaban mahasiswa — dari drag & drop (urutan langkah)
        $jawabanMahasiswa = array_map(static fn ($line) => trim((string) $line), $kodeLangkah);

        while (
            count($jawabanMahasiswa) > count($kunciJawaban)
            && trim((string) end($jawabanMahasiswa)) === ''
        ) {
            array_pop($jawabanMahasiswa);
        }

        $errors = [];
        foreach ($kunciJawaban as $index => $kunci) {
            $jawaban = $jawabanMahasiswa[$index] ?? '';
            if ($jawaban === '' || !BankSoalKonversi::linesMatch($kunci, $jawaban)) {
                $errors[] = ['index' => $index];
            }
        }

        if (!empty($errors)) {
            $nyawa = Nyawa::where('id_user', $idUser)->first();
            if ($nyawa && $nyawa->nyawa > 0) {
                $nyawa->nyawa -= 1;

                // Set waktu regenerasi
                if (is_null($nyawa->next_regen_at)) {
                    $nyawa->next_regen_at = now()->addMinutes(10);
                }

                $nyawa->save();
            }

            return response()->json([
                'success' => false,
                'message' => [
                    'message' => 'Terdapat jawaban salah',
                    'errors'  => $errors,
                ],
                'lives' => $nyawa->nyawa ?? 0,
            ], 422);
        }

        // Simpan hasil ujian
        $ujian = $this->model->create([
            'id_mahasiswa'          => $idMahasiswa,
            'id_bank_soal_konversi' => $idBankSoalKonversi,
            'id_level'              => $soalKonversi->id_level,
            'jawaban'               => implode("\n", $jawabanMahasiswa),
            'output'                => $soalKonversi->output,
            'nilai'                 => 100,
            'waktu'                 => $waktu,
        ]);

        $arsResult = ArsResult::where('id_mahasiswa', $idMahasiswa)
    ->where('id_level', $soalKonversi->id_level)
    ->where('id_soal', $soalKonversi->id_soal)
    ->whereNull('konversi_label')
    ->first();

if ($arsResult) {
    $langkah = DB::table('log_ujian_kode')
        ->where('id_mahasiswa', $idMahasiswa)
        ->where('id_bank_soal_konversi', $idBankSoalKonversi)
        ->count();

    [$konversiLabel, $konversiScore] = $this->determineLabelAndScore($langkah, $waktu);

    $arsResult->update([
        'konversi_label' => $konversiLabel,
        'konversi_score' => $konversiScore,
        'konversi_langkah' => $langkah,
        'konversi_durasi'  => $waktu,
    ]);
}

        return response()->json([
            'success'     => true,
            'java_output' => $soalKonversi->output,
            'konversi'    => [
                'id' => $soalKonversi->id,
            ],
        ]);
    }

    private function determineLabelAndScore($totalDrag, $totalWaktuDetik)
{
    if ($totalDrag <= 18 && $totalWaktuDetik < 53) {
        return ['Ideal', 90];
    } elseif ($totalDrag > 18 && $totalWaktuDetik >= 53) {
        return ['Struggling', 30];
    } elseif ($totalDrag <= 18 && $totalWaktuDetik >= 53) {
        return ['Normal', 70];
    } elseif ($totalDrag >= 18 && $totalWaktuDetik < 53) {
        return ['Gaming the System', 50];
    }
    return [null, null];
}
}