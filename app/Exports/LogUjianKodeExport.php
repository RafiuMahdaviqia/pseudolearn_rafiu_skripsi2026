<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use App\Models\UjianKode;
use App\Models\LogUjianKode;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LogUjianKodeExport implements FromCollection, WithHeadings, WithMapping
{
    protected $idMahasiswa;
    protected $idLevel;
    protected $idSoal;
    protected $ujianKodeModel;
    protected $logUjianKodeModel;

    public function __construct($idMahasiswa, $idLevel, $idSoal)
    {
        $this->idMahasiswa = $idMahasiswa;
        $this->idLevel = $idLevel;
        $this->idSoal = $idSoal;
        $this->ujianKodeModel = new UjianKode();
        $this->logUjianKodeModel = new LogUjianKode();
    }

    public function collection()
    {
        $query = DB::table('v_ujian_kode')
            ->where('id_mahasiswa', $this->idMahasiswa)
            ->whereNull('deleted_at');

        if (!empty($this->idLevel)) {
            $query->where('id_level', $this->idLevel);
        }
        if (!empty($this->idSoal)) {
            $query->where('id_bank_soal_konversi', $this->idSoal);
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        return $data->map(function ($row) {
            $row->drag_drop = DB::table('log_ujian_kode')
                ->where('id_mahasiswa', $this->idMahasiswa)
                ->where('id_bank_soal_konversi', $row->id_bank_soal_konversi)
                ->whereNull('deleted_at')
                ->count();

            $row->total_submit = DB::table('ujian_kode')
                ->where('id_mahasiswa', $this->idMahasiswa)
                ->where('id_bank_soal_konversi', $row->id_bank_soal_konversi)
                ->whereNull('deleted_at')
                ->count();

            return $row;
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Soal',
            'Tanggal Ujian',
            'Drag & Drop',
            'Total Submit',
            'Waktu (detik)',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $row->judul_soal,   
            $row->created_at,
            $row->drag_drop,
            $row->total_submit,
            $row->waktu,
        ];
    }
}
