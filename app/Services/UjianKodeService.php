<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Repositories\UjianKodeRepository;

class UjianKodeService
{
    protected $ujianKodeRepository;

    public function __construct()
    {
        $this->ujianKodeRepository = new UjianKodeRepository();
    }

    public function submitKonversi($request)
    {
        return $this->ujianKodeRepository->submitKonversi($request);
    }

    // Admin
    public function tableUjianKode($request)
    {
        try {
            $query = DB::table('v_ujian_kode')
                ->selectRaw('id_mahasiswa, nim, name, MAX(id_kelas) as id_kelas, MAX(kelas_name) as kelas_name')
                ->whereNull('deleted_at')
                ->groupBy('id_mahasiswa', 'nim', 'name');

            $total = (clone $query)->get()->count();
            $data  = (clone $query)
                ->offset($request->input('start', 0))
                ->limit($request->input('length', 10))
                ->get();

            return response()->json([
                'draw'            => intval($request->input('draw')),
                'recordsTotal'    => $total,
                'recordsFiltered' => $total,
                'data'            => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'draw'            => 1,
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
                'error'           => $e->getMessage()
            ]);
        }
    }
    public function tableDetail($request)
    {
        $idMahasiswa = $request->input('id_mahasiswa');
        $idLevel     = $request->input('id_level', '');
        $idSoal      = $request->input('id_soal', '');
        $start       = $request->input('start', 0);
        $length      = $request->input('length', 10);

        $query = DB::table('v_ujian_kode')
            ->where('id_mahasiswa', $idMahasiswa);

        if (!empty($idLevel)) {
            $query->where('id_level', $idLevel);
        }

        if (!empty($idSoal)) {
            $query->where('id_soal', $idSoal);
        }

        $total = (clone $query)->count();
        $data  = (clone $query)
            ->orderBy('created_at', 'desc')
            ->offset($start)
            ->limit($length)
            ->get();

        // ── Tambah kolom drag_drop & total_submit per baris ──
        $data = $data->map(function ($row) use ($idMahasiswa) {
            // Total drag & drop untuk soal ini
            $row->drag_drop = DB::table('log_ujian_kode')
                ->where('id_mahasiswa', $idMahasiswa)
                ->where('id_bank_soal_konversi', $row->id_bank_soal_konversi)
                ->whereNull('deleted_at')
                ->count();

            // Total submit untuk soal ini
            $row->total_submit = DB::table('v_ujian_kode')
                ->where('id_mahasiswa', $idMahasiswa)
                ->where('id_bank_soal_konversi', $row->id_bank_soal_konversi)
                ->whereNull('deleted_at')
                ->count();

            return $row;
        });

        return response()->json([
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => $total,
            'recordsFiltered' => $total,
            'data'            => $data,
        ]);
    }
}
