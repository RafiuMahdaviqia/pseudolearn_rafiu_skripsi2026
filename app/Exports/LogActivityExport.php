<?php

namespace App\Exports;

use App\Models\Mahasiswa;
use App\Models\Level;
use App\Models\Soal;
use App\Models\Kelas;
use App\Models\Ujian;
use App\Models\LogData;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class LogActivityExport implements FromCollection, WithHeadings, WithTitle, WithEvents
{
    protected $mahasiswa;
    protected $idKelas;
    protected $idLevel;
    protected $idSoal;
    protected $kelasModel;
    protected $levelModel;
    protected $soalModel;
    protected $ujianModel;
    protected $logDataModel;

    public function __construct($idKelas = null, $idLevel = null, $idSoal = null)
    {
        $this->mahasiswa = new Mahasiswa();
        $this->idKelas = $idKelas;
        $this->idLevel = $idLevel;
        $this->idSoal = $idSoal;
        $this->kelasModel = new Kelas();
        $this->levelModel = new Level();
        $this->soalModel = new Soal();
        $this->ujianModel = new Ujian();
        $this->logDataModel = new LogData();
    }

    public function collection()
    {
        $data = $this->mahasiswa->setView('v_mahasiswa')->get();

        if ($this->idKelas) {
            $data = $data->where('id_kelas', $this->idKelas);
        }

        return $data->map(function ($item) {
            // Calculate totalDrag
            $logDataQuery = $this->logDataModel->setView('v_log_data')
                ->where('id_mahasiswa', $item->id);
            if ($this->idLevel) {
                $logDataQuery = $logDataQuery->where('id_level', $this->idLevel);
            }
            if ($this->idSoal) {
                $logDataQuery = $logDataQuery->where('id_soal', $this->idSoal);
            }
            $totalDrag = $logDataQuery->count();

            // Calculate totalSubmit and totalWaktu
            $ujianQuery = $this->ujianModel->setView('v_ujian')
                ->where('id_mahasiswa', $item->id);
            if ($this->idLevel) {
                $ujianQuery = $ujianQuery->where('id_level', $this->idLevel);
            }
            if ($this->idSoal) {
                $ujianQuery = $ujianQuery->where('id_soal', $this->idSoal);
            }
            $totalSubmit = $ujianQuery->count();
            $totalWaktuDetik = $ujianQuery->sum('waktu');

            // Convert seconds to HH:MM:SS
            $hours = floor($totalWaktuDetik / 3600);
            $minutes = floor(($totalWaktuDetik % 3600) / 60);
            $seconds = $totalWaktuDetik % 60;
            $totalWaktu = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

            return [
                'nim'         => $item->nim,
                'name'        => $item->name,
                'kelas'       => $item->kelas_name . ' (' . $item->angkatan . ')',
                'totalDrag'   => $totalDrag,
                'totalSubmit' => $totalSubmit,
                'totalWaktu'  => $totalWaktu,
                'totalWaktuDetik' => $totalWaktuDetik . ' detik',
            ];
        });
    }

    public function headings(): array
    {
        return ['NIM', 'Nama', 'Kelas (Angkatan)', 'Drag and Drop', 'Total Submit', 'Total Waktu', 'Total Waktu (Detik)'];
    }

    public function title(): string
    {
        $title = 'Log Aktivitas';
        
        if ($this->idKelas) {
            $kelas = $this->kelasModel->find($this->idKelas);
            $title .= ' - ' . ($kelas ? $kelas->name : '');
        }
        
        if ($this->idLevel) {
            $level = $this->levelModel->find($this->idLevel);
            $title .= ' - ' . ($level ? $level->name : '');
        }
        
        if ($this->idSoal) {
            $soal = $this->soalModel->find($this->idSoal);
            $title .= ' - ' . ($soal ? $soal->judul : '');
        }
        
        return $title;
    }

    public function registerEvents(): array
    {
        $sheetTitle = $this->title();

        return [
            AfterSheet::class => function(AfterSheet $event) use ($sheetTitle) {
                // Insert 2 rows at the top
                $event->sheet->insertNewRowBefore(1, 2);

                // Title in A1, merge A1:G1, font 14 bold, center
                $event->sheet->setCellValue('A1', $sheetTitle);
                $event->sheet->mergeCells('A1:G1');
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

                // Heading in row 3, font 12 bold, center, border
                $event->sheet->getStyle('A3:G3')->applyFromArray([
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
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                // Style all data cells with border
                $lastRow = $event->sheet->getHighestRow();
                $event->sheet->getStyle('A4:G' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                // Auto-size columns
                foreach (range('A', 'G') as $column) {
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}
