<?php

namespace App\Exports;

use App\Services\LogChatbotAdaptiveService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class LogChatbotAdaptiveExport implements FromCollection, WithHeadings, WithTitle, WithEvents, WithColumnFormatting
{
	protected LogChatbotAdaptiveService $service;

	protected ?string $idKelas;
	protected ?string $idLevel;
	protected ?string $idSoal;

	public function __construct($idKelas = null, $idLevel = null, $idSoal = null)
	{
		$this->service = app(LogChatbotAdaptiveService::class);
		$this->idKelas = $idKelas;
		$this->idLevel = $idLevel;
		$this->idSoal = $idSoal;
	}

	public function collection()
	{
		return $this->service->exportRows($this->idKelas, $this->idLevel, $this->idSoal)
			->map(function (array $row) {
				return [
					'nim' => $row['nim'] ?? '-',
					'name' => $row['name'] ?? '-',
					'kelas_name' => $row['kelas_name'] ?? '-',
					'level_name' => $row['level_name'] ?? '-',
					'jenis_soal' => $row['jenis_soal'] ?? '-',
					'jumlah_langkah' => $row['jumlah_langkah'] ?? 0,
					'waktu' => $row['waktu'] ?? '-',
					'labeling' => $row['labeling'] ?? '-',
					'durasi' => $row['durasi'] ?? '-',
					'total_akses_adaptive' => $row['total_akses_adaptive'] ?? 0,
					'total_messages' => $row['total_messages'] ?? 0,
				];
			});
	}

	public function columnFormats(): array
	{
		return [
			'A' => NumberFormat::FORMAT_TEXT,
		];
	}

	public function headings(): array
	{
		return [
			'NIM',
			'Nama',
			'Kelas',
			'Level Soal',
			'Jenis Soal',
			'Jumlah Langkah',
			'Waktu',
			'Labeling',
			'Durasi',
			'Total Akses Chatbot Adaptive',
			'Total Pesan Bimbingan',
		];
	}

	public function title(): string
	{
		return 'Log Chatbot Adaptive';
	}

	public function registerEvents(): array
	{
		return [
			AfterSheet::class => function (AfterSheet $event) {
				$event->sheet->insertNewRowBefore(1, 2);

				$event->sheet->setCellValue('A1', 'Log Chatbot Adaptive');
				$event->sheet->mergeCells('A1:K1');
				$event->sheet->getStyle('A1')->applyFromArray([
					'font' => ['bold' => true, 'size' => 14],
					'alignment' => [
						'horizontal' => Alignment::HORIZONTAL_CENTER,
						'vertical' => Alignment::VERTICAL_CENTER,
					],
				]);

				$event->sheet->getStyle('A3:K3')->applyFromArray([
					'font' => ['bold' => true, 'size' => 12],
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
						$value = $sheet->getCell('A' . $row)->getValue();
						$sheet->setCellValueExplicit('A' . $row, (string) $value, DataType::TYPE_STRING);
					}

					$event->sheet->getStyle('A4:K' . $lastRow)->applyFromArray([
						'borders' => [
							'allBorders' => [
								'borderStyle' => Border::BORDER_THIN,
								'color' => ['argb' => '000000'],
							],
						],
					]);
				}

				foreach (range('A', 'K') as $column) {
					$event->sheet->getColumnDimension($column)->setAutoSize(true);
				}
			},
		];
	}
}
