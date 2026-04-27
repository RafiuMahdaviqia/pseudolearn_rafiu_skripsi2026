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
	protected string $search;

	public function __construct($idKelas = null, $idLevel = null, $idSoal = null, string $search = '')
	{
		$this->service = app(LogChatbotAdaptiveService::class);
		$this->idKelas = $idKelas;
		$this->idLevel = $idLevel;
		$this->idSoal = $idSoal;
		$this->search = trim($search);
	}

	public function collection()
	{
		$rows = $this->service->exportRows($this->idKelas, $this->idLevel, $this->idSoal, $this->search);

		return $rows
			->flatMap(function (array $row) {
				$base = [
					'nim' => $row['nim'] ?? '-',
					'name' => $row['name'] ?? '-',
					'kelas_name' => $row['kelas_name'] ?? '-',
					'total_akses_adaptive' => (int) ($row['total_akses_adaptive'] ?? 0),
					'total_messages' => (int) ($row['total_messages'] ?? 0),
				];

				$history = collect($row['history'] ?? []);
				if ($history->isEmpty()) {
					return [[
						...$base,
						'session_no' => '-',
						'level_name' => '-',
						'jenis_soal' => '-',
						'waktu_pengerjaan' => '-',
						'durasi_popup' => '-',
						'jumlah_langkah' => 0,
						'labeling' => '-',
						'waktu_mulai' => '-',
						'waktu_selesai' => '-',
						'akhir_sesi' => '-',
						'history_chat' => '-',
					]];
				}

				return $history
					->values()
					->map(function (array $session, int $index) use ($base) {
						return [
							...$base,
							'session_no' => $index + 1,
							'level_name' => $session['level_name'] ?? '-',
							'jenis_soal' => $session['soal_title'] ?? '-',
							'waktu_pengerjaan' => $session['waktu_pengerjaan'] ?? ($session['waktu_akses'] ?? '-'),
							'durasi_popup' => $session['durasi'] ?? '-',
							'jumlah_langkah' => (int) ($session['jumlah_langkah'] ?? 0),
							'labeling' => $session['labeling'] ?? '-',
							'waktu_mulai' => $session['waktu_mulai_label'] ?? '-',
							'waktu_selesai' => $session['waktu_selesai_label'] ?? '-',
							'akhir_sesi' => $session['selesai_karena'] ?? '-',
							'history_chat' => $this->formatSessionMessages($session['messages'] ?? []),
						];
					})
					->all();
			})
			->values();
	}

	private function formatSessionMessages(array $messages): string
	{
		if (empty($messages)) {
			return '-';
		}

		$lines = [];
		foreach ($messages as $index => $message) {
			if (!is_array($message)) {
				continue;
			}

			$time = trim((string) ($message['waktu'] ?? '-'));
			$studentName = trim((string) ($message['mahasiswa_name'] ?? 'Mahasiswa'));
			$botName = trim((string) ($message['bot_name'] ?? 'PseudoLearn Chatbot AI'));
			$studentMessage = trim((string) ($message['mahasiswa_message'] ?? ''));
			$botResponse = trim((string) ($message['bot_response'] ?? $message['respons'] ?? ''));
			$systemNote = trim((string) ($message['pesan'] ?? ''));

			$prefix = (string) ($index + 1) . '. [' . ($time !== '' ? $time : '-') . '] ';

			if ($studentMessage !== '') {
				$lines[] = $prefix . $studentName . ': ' . preg_replace('/\s+/', ' ', $studentMessage);
			}

			if ($botResponse !== '') {
				$lines[] = '   ' . $botName . ': ' . preg_replace('/\s+/', ' ', $botResponse);
			}

			if ($studentMessage === '' && $botResponse === '' && $systemNote !== '') {
				$lines[] = $prefix . 'System: ' . preg_replace('/\s+/', ' ', $systemNote);
			}
		}

		if (empty($lines)) {
			return '-';
		}

		return implode("\n", $lines);
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
			'Total Akses Chatbot Adaptive',
			'Total Pesan Bimbingan',
			'Sesi Ke',
			'Level Soal',
			'Jenis Soal',
			'Waktu Pengerjaan',
			'Durasi Popup',
			'Jumlah Langkah',
			'Labeling',
			'Waktu Mulai',
			'Waktu Selesai',
			'Akhir Sesi',
			'History Chat',
		];
	}

	public function title(): string
	{
		return 'Log Adaptive Detail';
	}

	public function registerEvents(): array
	{
		return [
			AfterSheet::class => function (AfterSheet $event) {
				$event->sheet->insertNewRowBefore(1, 2);

				$event->sheet->setCellValue('A1', 'Log Chatbot Adaptive (Detail + History Chat)');
				$event->sheet->mergeCells('A1:P1');
				$event->sheet->getStyle('A1')->applyFromArray([
					'font' => ['bold' => true, 'size' => 14],
					'alignment' => [
						'horizontal' => Alignment::HORIZONTAL_CENTER,
						'vertical' => Alignment::VERTICAL_CENTER,
					],
				]);

				$event->sheet->getStyle('A3:P3')->applyFromArray([
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

					$event->sheet->getStyle('A4:P' . $lastRow)->applyFromArray([
						'alignment' => [
							'vertical' => Alignment::VERTICAL_TOP,
						],
						'borders' => [
							'allBorders' => [
								'borderStyle' => Border::BORDER_THIN,
								'color' => ['argb' => '000000'],
							],
						],
					]);

					$event->sheet->getStyle('P4:P' . $lastRow)->getAlignment()->setWrapText(true);
				}

				foreach (range('A', 'O') as $column) {
					$event->sheet->getColumnDimension($column)->setAutoSize(true);
				}

				$event->sheet->getColumnDimension('P')->setWidth(120);
			},
		];
	}
}
