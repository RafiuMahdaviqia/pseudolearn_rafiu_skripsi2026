<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ArsExport implements
    FromCollection,
    WithHeadings,
    WithTitle,
    WithEvents,
    WithColumnFormatting,
    ShouldAutoSize
{
    protected $kelas;

    public function __construct($kelas = null)
    {
        $this->kelas = $kelas;
    }

    public function collection()
    {
        return DB::table('mahasiswa as m')
            ->leftJoin('kelas as k', 'k.id', '=', 'm.id_kelas')
            ->leftJoin('ars_result as ar', 'ar.id_mahasiswa', '=', 'm.id')
            ->leftJoin('soal as s', 's.id', '=', 'ar.id_soal')
            ->leftJoin('level as l', 'l.id', '=', 'ar.id_level')

            ->when($this->kelas, function ($q) {
                $q->where('m.id_kelas', $this->kelas);
            })

            ->select(
                'm.nim',
                'm.name',
                'k.name as kelas',

                DB::raw("
                    COALESCE(
                        SUM(
                            CASE
                                WHEN ar.pseudo_label IN ('Struggling','Gaming the System')
                                THEN 1
                                ELSE 0
                            END
                        ),0
                    ) as total_ars
                "),

                DB::raw("
                    COUNT(DISTINCT ar.id_soal) as jumlah_soal
                "),

                DB::raw("
                    COALESCE(
                        SUM(
                            COALESCE(ar.pseudo_durasi,0)
                            +
                            COALESCE(ar.konversi_durasi,0)
                        ),0
                    ) as total_waktu
                "),

                'l.name as level',
                's.judul as soal',
                'ar.ars_batch',
                'ar.difficulty',
                'ar.pseudo_label',
                'ar.pseudo_durasi',
                'ar.konversi_label',
                'ar.konversi_durasi',

                DB::raw("
                    COALESCE(ar.pseudo_durasi,0)
                    +
                    COALESCE(ar.konversi_durasi,0)
                    as total_durasi
                "),

                'ar.created_at'
            )

            ->groupBy(
                'm.nim',
                'm.name',
                'k.name',
                'l.name',
                's.judul',
                'ar.ars_batch',
                'ar.difficulty',
                'ar.pseudo_label',
                'ar.pseudo_durasi',
                'ar.konversi_label',
                'ar.konversi_durasi',
                'ar.created_at'
            )

            ->orderBy('m.name')
            ->orderBy('ar.created_at', 'desc')

            ->get()

            ->map(function ($row) {

                return [
                    'nim' => $row->nim,
                    'nama' => $row->name,
                    'kelas' => $row->kelas,

                    'total_ars' => $row->total_ars,
                    'jumlah_soal' => $row->jumlah_soal,
                    'total_waktu' => gmdate('H:i:s', $row->total_waktu ?? 0),

                    'level' => $row->level ?? '-',
                    'soal' => $row->soal ?? '-',
                    'batch' => $row->ars_batch ?? '-',
                    'difficulty' => $row->difficulty ?? '-',

                    'pseudo_label' => $row->pseudo_label ?? '-',
                    'pseudo_durasi' => gmdate('H:i:s', $row->pseudo_durasi ?? 0),

                    'konversi_label' => $row->konversi_label ?? '-',
                    'konversi_durasi' => gmdate('H:i:s', $row->konversi_durasi ?? 0),

                    'total_durasi' => gmdate('H:i:s', $row->total_durasi ?? 0),

                    'tanggal' => $row->created_at
                        ? date('d M Y', strtotime($row->created_at))
                        : '-',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'NIM',
            'Nama',
            'Kelas',
            'Total ARS',
            'Jumlah Soal Tambahan',
            'Total Waktu',
            'Level',
            'Soal Tambahan',
            'Batch',
            'Difficulty',
            'Label Pseudo',
            'Durasi Pseudo',
            'Label Konversi',
            'Durasi Konversi',
            'Total Durasi',
            'Tanggal',
        ];
    }

    public function title(): string
    {
        return 'ARS Report';
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $event->sheet->insertNewRowBefore(1, 2);

                $event->sheet->setCellValue('A1', 'ARS REPORT');
                $event->sheet->mergeCells('A1:P1');

                $event->sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $event->sheet->getStyle('A3:P3')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $lastRow = $event->sheet->getHighestRow();

                if ($lastRow >= 4) {

                    $sheet = $event->sheet->getDelegate();

                    for ($row = 4; $row <= $lastRow; $row++) {

                        $value = $sheet->getCell('A'.$row)->getValue();

                        $sheet->setCellValueExplicit(
                            'A'.$row,
                            (string) $value,
                            DataType::TYPE_STRING
                        );
                    }

                    $event->sheet->getStyle('A4:P'.$lastRow)
                        ->applyFromArray([
                            'alignment' => [
                                'vertical' => Alignment::VERTICAL_CENTER,
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => ['argb' => '000000'],
                                ],
                            ],
                        ]);
                }
            },
        ];
    }
}