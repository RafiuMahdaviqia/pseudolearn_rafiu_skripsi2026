<?php

namespace App\Exports;

use App\Models\ChatbotAccessLog;
use App\Models\ChatbotLog;
use App\Models\Mahasiswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LogDataChatbotExport implements FromCollection, WithHeadings
{
    protected $idKelas;
    protected $idLevel;
    protected $idSoal;

    public function __construct($idKelas = null, $idLevel = null, $idSoal = null)
    {
        $this->idKelas = $idKelas;
        $this->idLevel = $idLevel;
        $this->idSoal = $idSoal;
    }

    public function collection()
    {
        $query = (new Mahasiswa())->setView('v_mahasiswa');

        if (!empty($this->idKelas)) {
            $query = $query->where('id_kelas', $this->idKelas);
        }

        $mahasiswas = $query->get(['id', 'nim', 'name', 'kelas_name']);
        $mahasiswaIds = $mahasiswas->pluck('id')->filter()->values();

        if ($mahasiswaIds->isEmpty()) {
            return collect();
        }

        if (!empty($this->idLevel) || !empty($this->idSoal)) {
            $relevantIds = ChatbotLog::whereIn('id_mahasiswa', $mahasiswaIds)
                ->when(!empty($this->idLevel), function ($q) {
                    $q->where('id_level', $this->idLevel);
                })
                ->when(!empty($this->idSoal), function ($q) {
                    $q->where('id_soal', $this->idSoal);
                })
                ->pluck('id_mahasiswa')
                ->unique()
                ->values();
        } else {
            $relevantIds = $mahasiswaIds;
        }

        if ($relevantIds->isEmpty()) {
            return collect();
        }

        $countBiasa = ChatbotAccessLog::whereIn('id_mahasiswa', $relevantIds)
            ->where('type', 'biasa')
            ->selectRaw('id_mahasiswa, count(*) as total')
            ->groupBy('id_mahasiswa')
            ->pluck('total', 'id_mahasiswa');

        $countAdaptive = ChatbotAccessLog::whereIn('id_mahasiswa', $relevantIds)
            ->where('type', 'adaptive')
            ->selectRaw('id_mahasiswa, count(*) as total')
            ->groupBy('id_mahasiswa')
            ->pluck('total', 'id_mahasiswa');

        return $mahasiswas->filter(function ($row) use ($relevantIds) {
            return $relevantIds->contains($row->id);
        })->map(function ($row) use ($countBiasa, $countAdaptive) {
            return [
                'nim' => $row->nim,
                'nama' => $row->name,
                'kelas' => $row->kelas_name ?: '-',
                'jumlah_chatbot' => (int) ($countBiasa[$row->id] ?? 0),
                'jumlah_chatbot_adaptive' => (int) ($countAdaptive[$row->id] ?? 0),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'NIM',
            'Nama',
            'Kelas',
            'Jumlah Buka Chatbot',
            'Jumlah Buka Chatbot Adaptive',
        ];
    }
}
