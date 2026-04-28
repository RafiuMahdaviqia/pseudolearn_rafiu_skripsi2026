<?php

namespace App\Http\Controllers\LogDataChatbot;

use App\Exports\LogChatbotAdaptiveExport;
use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Level;
use App\Services\LogChatbotAdaptiveService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LogChatbotAdaptive extends Controller
{
	protected LogChatbotAdaptiveService $service;
	protected Kelas $kelasModel;
	protected Level $levelModel;

	public function __construct(LogChatbotAdaptiveService $service)
	{
		$this->service = $service;
		$this->kelasModel = new Kelas();
		$this->levelModel = new Level();
	}

	public function index()
	{
		$list_kelas = $this->kelasModel->get(['id', 'name', 'angkatan'])->toArray();

		$list_kelas = collect($list_kelas)->prepend(['id' => '', 'name' => 'Semua Kelas', 'angkatan' => '']);

		$list_kelas = collect($list_kelas)->map(function ($item) {
			return [
				'id' => $item['id'],
				'name' => $item['name'],
				'angkatan' => $item['angkatan'],
			];
		})->values()->toArray();

		$list_level = $this->levelModel->orderBy('order', 'asc')->get(['id', 'name'])
			->pluck('name', 'id')
			->toArray();

		$list_level = collect($list_level)->map(function ($name, $id) {
			return [
				'id' => $id,
				'name' => $name,
			];
		})->values()->toArray();

		return view('pages.logDataChatbot.adaptive', [
			'title' => 'Log Chatbot Adaptive',
			'list_kelas' => $list_kelas,
			'list_level' => $list_level,
		]);
	}

	public function table(Request $request): JsonResponse
	{
		$data = $this->service->indexData($request);

		$start = (int) $request->input('start', 0);
		$length = (int) $request->input('length', 10);

		$rows = $data['data']->slice($start, $length)->values();

		return response()->json([
			'draw' => intval($request->input('draw')),
			'recordsTotal' => $data['recordsTotal'],
			'recordsFiltered' => $data['recordsFiltered'],
			'data' => $rows,
		]);
	}

	public function detail(string $id): JsonResponse
	{
		return response()->json($this->service->detail($id));
	}

	public function getSoalByLevel(Request $request): JsonResponse
	{
		return response()->json($this->service->getSoalByLevel($request->input('level_id'))->values());
	}

	public function export(Request $request)
	{
		$filename = 'Log_Chatbot_Adaptive_' . date('Y-m-d_H-i-s') . '.xlsx';

		return Excel::download(
			new LogChatbotAdaptiveExport(
				$request->input('kelas'),
				$request->input('level'),
				$request->input('soal')
			),
			$filename
		);
	}
}
