<?php

namespace App\Exports;

use App\Models\Mahasiswa;
use App\Models\Level;
use App\Models\Soal;
use App\Models\Kelas;
use App\Models\Ujian;
use App\Models\LogData;
use App\Models\LabelSkor;
use App\Models\NilaiTest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ScoringExport implements FromCollection, WithHeadings, WithTitle, WithEvents
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
    protected $labelSkorModel;
    protected $nilaiTestModel;

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
        $this->labelSkorModel = new LabelSkor();
        $this->nilaiTestModel = new NilaiTest();
    }

    public function collection()
    {
        $mahasiswaData = $this->mahasiswa->setView('v_mahasiswa')->orderBy('name', 'asc')->get();

        if ($this->idKelas) {
            $mahasiswaData = $mahasiswaData->where('id_kelas', $this->idKelas);
        }

        // Get levels
        $levels = $this->levelModel->orderBy('order', 'asc')->get();
        if ($this->idLevel) {
            $levels = $levels->where('id', $this->idLevel);
        }

        $result = collect();

        foreach ($mahasiswaData as $item) {
            foreach ($levels as $level) {
                // Get soal for this level
                $soalList = $this->soalModel->where('id_level', $level->id)->orderBy('order', 'asc')->get();
                if ($this->idSoal) {
                    $soalList = $soalList->where('id', $this->idSoal);
                }

                // Get pre-test and post-test values for this level
                $nilaiTest = $this->nilaiTestModel
                    ->where('id_mahasiswa', $item->id)
                    ->where('id_level', $level->id)
                    ->first();
                $preTest = $nilaiTest->pre_test ?? '';
                $postTest = $nilaiTest->post_test ?? '';

                foreach ($soalList as $soal) {
                    // Calculate totalDrag for this specific level and soal
                    $totalDrag = $this->logDataModel->setView('v_log_data')
                        ->where('id_mahasiswa', $item->id)
                        ->where('id_level', $level->id)
                        ->where('id_soal', $soal->id)
                        ->count();

                    // Calculate totalWaktu for this specific level and soal
                    $totalWaktuDetik = $this->ujianModel->setView('v_ujian')
                        ->where('id_mahasiswa', $item->id)
                        ->where('id_level', $level->id)
                        ->where('id_soal', $soal->id)
                        ->sum('waktu');

                    // Convert seconds to HH:MM:SS
                    $hours = floor($totalWaktuDetik / 3600);
                    $minutes = floor(($totalWaktuDetik % 3600) / 60);
                    $seconds = $totalWaktuDetik % 60;
                    $totalWaktu = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

                    // Get skor for this specific combination
                    $labelSkor = $this->labelSkorModel->setView('v_label_skor')
                        ->where('id_mahasiswa', $item->id)
                        ->where('id_level', $level->id)
                        ->where('id_soal', $soal->id)
                        ->where('id_kelas', $item->id_kelas)
                        ->first();
                    $skor = $labelSkor->skor ?? '';

                    $result->push([
                        'nim'             => $item->nim,
                        'name'            => $item->name,
                        'kelas'           => $item->kelas_name . ' (' . $item->angkatan . ')',
                        'level'           => $level->name,
                        'soal'            => $soal->judul,
                        'pre_test'        => $preTest,
                        'post_test'       => $postTest,
                        'totalDrag'       => $totalDrag,
                        'totalWaktu'      => $totalWaktu,
                        'totalWaktuDetik' => $totalWaktuDetik . ' detik',
                        'skor'            => $skor,
                    ]);
                }
            }
        }

        return $result;
    }

    public function headings(): array
    {
        return ['NIM', 'Nama', 'Kelas (Angkatan)', 'Level', 'Soal', 'Pre-Test', 'Post-Test', 'Drag and Drop', 'Total Waktu', 'Total Waktu (Detik)', 'Skor'];
    }

    public function title(): string
    {
        $title = 'Clustering Scoring';
        
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

                // Title in A1, merge A1:K1, font 14 bold, center
                $event->sheet->setCellValue('A1', $sheetTitle);
                $event->sheet->mergeCells('A1:K1');
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
                $event->sheet->getStyle('A3:K3')->applyFromArray([
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
                $event->sheet->getStyle('A4:K' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                // Auto-size columns
                foreach (range('A', 'K') as $column) {
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}
