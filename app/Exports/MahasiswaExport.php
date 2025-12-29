<?php

namespace App\Exports;

use App\Models\Mahasiswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Models\Kelas;

class MahasiswaExport implements FromCollection, WithHeadings, WithTitle, WithEvents
{
    protected $mahasiswa;
    protected $idKelas;
    protected $kelasModel;

    public function __construct($idKelas = null)
    {
        $this->mahasiswa = new Mahasiswa();
        $this->idKelas = $idKelas;
        $this->kelasModel = new Kelas();
    }

    public function collection()
    {
        $data = $this->mahasiswa->setView('v_mahasiswa')->get();

        if ($this->idKelas) {
            $data = $data->where('id_kelas', $this->idKelas);
        }

        return $data->map(function ($item) {
            return [
                'nim'         => $item->nim,
                'name'        => $item->name,
                'kelas'       => $item->kelas_name . ' (' . $item->angkatan . ')',
                'email'       => $item->email,
                'jenis_kelamin' => $item->jenis_kelamin === 'p' ? 'Perempuan' : 'Laki-laki',
            ];
        });
    }

    public function headings(): array
    {
        // Heading kolom saja, akan diletakkan di baris 3
        return ['NIM', 'Nama', 'Kelas (Angkatan)', 'Email', 'Jenis Kelamin'];
    }

    public function title(): string
    {
        if ($this->idKelas) {
            $kelas = $this->kelasModel->find($this->idKelas);
            return 'Rekapitulasi Data Mahasiswa ' . ($kelas ? $kelas->name : 'Semua Kelas');
        } else {
            return 'Rekapitulasi Data Mahasiswa';
        }
    }

    public function registerEvents(): array
    {
        $kelasName = 'Rekapitulasi Data Mahasiswa';
        if ($this->idKelas) {
            $kelas = $this->kelasModel->find($this->idKelas);
            $kelasName = 'Rekapitulasi Data Mahasiswa ' . ($kelas ? $kelas->name : 'Semua Kelas');
        } else {
            $kelasName = 'Rekapitulasi Data Mahasiswa';
        }

        return [
            AfterSheet::class => function(AfterSheet $event) use ($kelasName) {
                // Sisipkan 2 baris di atas
                $event->sheet->insertNewRowBefore(1, 2);

                // Judul di A1, merge A1:E1, font 14 bold, center
                $event->sheet->setCellValue('A1', $kelasName);
                $event->sheet->mergeCells('A1:E1');
                $event->sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Heading di baris 3, font 12 bold, center, border
                $event->sheet->getStyle('A3:E3')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Data mulai baris 4, font 12 normal, border
                $rowCount = $event->sheet->getDelegate()->getHighestRow();
                $event->sheet->getStyle('A4:E' . $rowCount)->applyFromArray([
                    'font' => [
                        'size' => 12,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Auto size kolom A-E
                foreach (range('A', 'E') as $col) {
                    $event->sheet->getDelegate()->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}