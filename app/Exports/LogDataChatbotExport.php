<?php

namespace App\Exports;

use App\Models\ChatbotAccessLog;
use App\Models\ChatbotLog;
use App\Models\Kelas;
use App\Models\Level;
use App\Models\Mahasiswa;
use App\Models\Soal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class LogDataChatbotExport implements FromCollection, WithHeadings, WithTitle, WithEvents, WithColumnFormatting
{
    protected Mahasiswa $mahasiswaModel;
    protected Kelas $kelasModel;
    protected Level $levelModel;
    protected Soal $soalModel;

    protected $idKelas;
    protected $idLevel;
    protected $idSoal;

    public function __construct($idKelas = null, $idLevel = null, $idSoal = null)
    {
        $this->mahasiswaModel = new Mahasiswa();
        $this->kelasModel     = new Kelas();
        $this->levelModel     = new Level();
        $this->soalModel      = new Soal();

        $this->idKelas = $idKelas;
        $this->idLevel = $idLevel;
        $this->idSoal  = $idSoal;
    }

    public function collection()
    {
        $baseQuery = $this->mahasiswaModel->setView('v_mahasiswa');

        if (!empty($this->idKelas)) {
            $baseQuery = $baseQuery->where('id_kelas', $this->idKelas);
        }

        $mahasiswas   = $baseQuery->orderBy('name', 'asc')->get();
        $mahasiswaIds = $mahasiswas->pluck('id');

        if ($mahasiswaIds->isEmpty()) {
            return collect();
        }

        $latestChatbotLogs = ChatbotLog::whereIn('id_mahasiswa', $mahasiswaIds)
            ->with('level:id,name')
            ->when(!empty($this->idLevel), function ($query) {
                $query->where('id_level', $this->idLevel);
            })
            ->when(!empty($this->idSoal), function ($query) {
                $query->where('id_soal', $this->idSoal);
            })
            ->orderBy('created_at', 'desc')
            ->get(['id_mahasiswa', 'id_level', 'created_at']);

        $latestLevelBy = [];
        foreach ($latestChatbotLogs as $log) {
            if (!isset($latestLevelBy[$log->id_mahasiswa])) {
                $latestLevelBy[$log->id_mahasiswa] = $log->level?->name ?? '-';
            }
        }

        $countBiasa = ChatbotAccessLog::whereIn('id_mahasiswa', $mahasiswaIds)
            ->where('type', 'biasa')
            ->selectRaw('id_mahasiswa, count(*) as total')
            ->groupBy('id_mahasiswa')
            ->pluck('total', 'id_mahasiswa');

        $countAdaptive = ChatbotAccessLog::whereIn('id_mahasiswa', $mahasiswaIds)
            ->where('type', 'adaptive')
            ->selectRaw('id_mahasiswa, count(*) as total')
            ->groupBy('id_mahasiswa')
            ->pluck('total', 'id_mahasiswa');

        $countBiasa = ChatbotAccessLog::whereIn('id_mahasiswa', $mahasiswaIds)
            ->where('type', 'biasa')
            ->selectRaw('id_mahasiswa, count(*) as total')
            ->groupBy('id_mahasiswa')
            ->pluck('total', 'id_mahasiswa');

        $countAdaptive = ChatbotAccessLog::whereIn('id_mahasiswa', $mahasiswaIds)
            ->where('type', 'adaptive')
            ->selectRaw('id_mahasiswa, count(*) as total')
            ->groupBy('id_mahasiswa')
            ->pluck('total', 'id_mahasiswa');

        $accessLogs = ChatbotAccessLog::whereIn('id_mahasiswa', $mahasiswaIds)
            ->with('mahasiswa:id,nim,name,id_kelas')
            ->orderBy('opened_at', 'asc')
            ->get();

        $accessLogsByMahasiswa = $accessLogs->groupBy('id_mahasiswa');

        $rows = collect();

        foreach ($mahasiswas as $mahasiswa) {
            $studentAccessLogs = $accessLogsByMahasiswa->get($mahasiswa->id, collect());

            if ($studentAccessLogs->isEmpty()) {
                $rows->push([
                    'nim'                     => (string) $mahasiswa->nim,
                    'name'                    => $mahasiswa->name,
                    'kelas'                   => $mahasiswa->kelas_name ?? '-',
                    'level_terakhir'          => $latestLevelBy[$mahasiswa->id] ?? '-',
                    'jumlah_chatbot'          => $countBiasa[$mahasiswa->id] ?? 0,
                    'jumlah_chatbot_adaptive' => $countAdaptive[$mahasiswa->id] ?? 0,
                    'type'                    => '-',
                    'level_name'              => '-',
                    'soal_name'               => '-',
                    'waktu_akses'             => '-',
                    'durasi'                  => '-',
                ]);

                continue;
            }

            foreach ($studentAccessLogs as $log) {
                $sessionEnd = $log->closed_at ?? now();

                $chatbotLogs = ChatbotLog::query()
                    ->where('id_mahasiswa', $mahasiswa->id)
                    ->when(!empty($this->idLevel), function ($query) {
                        $query->where('id_level', $this->idLevel);
                    })
                    ->when(!empty($this->idSoal), function ($query) {
                        $query->where('id_soal', $this->idSoal);
                    })
                    ->whereBetween('created_at', [$log->opened_at, $sessionEnd])
                    ->with(['level:id,name', 'soal:id,judul'])
                    ->orderBy('created_at', 'asc')
                    ->get();

                $firstLog = $chatbotLogs->first();
                $levelName = $firstLog?->level?->name ?? '-';
                $soalName = $firstLog?->soal?->judul ?? '-';

                $durasiText = '-';
                if (!is_null($log->durasi_menit)) {
                    if ($log->durasi_menit > 0) {
                        $durasiText = $log->durasi_menit . ' menit';
                    } else {
                        $detik = $log->opened_at && $log->closed_at
                            ? abs($log->opened_at->diffInSeconds($log->closed_at))
                            : 0;
                        $durasiText = '0 menit ' . $detik . ' detik';
                    }
                }

                $rows->push([
                    'nim'                     => (string) $mahasiswa->nim,
                    'name'                    => $mahasiswa->name,
                    'kelas'                   => $mahasiswa->kelas_name ?? '-',
                    'level_terakhir'          => $latestLevelBy[$mahasiswa->id] ?? '-',
                    'jumlah_chatbot'          => $countBiasa[$mahasiswa->id] ?? 0,
                    'jumlah_chatbot_adaptive' => $countAdaptive[$mahasiswa->id] ?? 0,
                    'type'                    => ucfirst($log->type),
                    'level_name'              => $levelName,
                    'soal_name'               => $soalName,
                    'waktu_akses'             => $log->opened_at ? $log->opened_at->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s') . ' WIB' : '-',
                    'durasi'                  => $durasiText,
                ]);
            }
        }

        return $rows;
    }

    public function columnFormats(): array
    {
        return [
            // Kolom A (NIM) dipaksa sebagai teks supaya semua digit tampil
            'A' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function headings(): array
    {
        return [
            'NIM',
            'Nama',
            'Kelas',
            'Level Terakhir',
            'Jumlah Buka Chatbot',
            'Jumlah Buka Chatbot Adaptive',
            'Tipe Chatbot',
            'Level',
            'Soal',
            'Waktu Akses',
            'Durasi',
        ];
    }

    public function title(): string
    {
        $title = 'Log Data Chatbot';

        if (!empty($this->idKelas)) {
            $kelas = $this->kelasModel->find($this->idKelas);
            if ($kelas) {
                $title .= ' - ' . $kelas->name;
            }
        }

        if (!empty($this->idLevel)) {
            $level = $this->levelModel->find($this->idLevel);
            if ($level) {
                $title .= ' - ' . $level->name;
            }
        }

        if (!empty($this->idSoal)) {
            $soal = $this->soalModel->find($this->idSoal);
            if ($soal) {
                $title .= ' - ' . $soal->judul;
            }
        }

        return $title;
    }

    public function registerEvents(): array
    {
        $sheetTitle = $this->title();

        return [
            AfterSheet::class => function (AfterSheet $event) use ($sheetTitle) {
                $event->sheet->insertNewRowBefore(1, 2);

                $event->sheet->setCellValue('A1', $sheetTitle);
                $event->sheet->mergeCells('A1:K1');
                $event->sheet->getStyle('A1')->applyFromArray([
                    'font'      => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $event->sheet->getStyle('A3:K3')->applyFromArray([
                    'font'      => [
                        'bold' => true,
                        'size' => 12,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'borders'   => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color'       => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $event->sheet->setAutoFilter('A3:K3');
                $event->sheet->freezePane('A4');
                $event->sheet->getStyle('A1:K' . max($event->sheet->getHighestRow(), 3))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $lastRow = $event->sheet->getHighestRow();
                if ($lastRow >= 4) {
                    // Pastikan semua sel kolom NIM diperlakukan sebagai teks
                    $sheet = $event->sheet->getDelegate();
                    for ($row = 4; $row <= $lastRow; $row++) {
                        $cellCoordinate = 'A' . $row;
                        $value          = $sheet->getCell($cellCoordinate)->getValue();
                        $sheet->setCellValueExplicit($cellCoordinate, (string) $value, DataType::TYPE_STRING);
                    }

                    $event->sheet->getStyle('A4:K' . $lastRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color'       => ['argb' => '000000'],
                            ],
                        ],
                    ]);
                }

                foreach (range('A', 'K') as $column) {
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }

                $event->sheet->getStyle('A4:K' . $lastRow)->getAlignment()->setWrapText(true);
            },
        ];
    }
}
