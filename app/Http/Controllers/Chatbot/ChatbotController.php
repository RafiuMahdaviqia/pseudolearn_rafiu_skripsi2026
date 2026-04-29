<?php

namespace App\Http\Controllers\Chatbot;

use App\Http\Controllers\Controller;
use App\Models\ChatbotAdaptiveLog;
use App\Models\ChatbotAccessLog;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\Soal;
use App\Models\Level;
use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ChatbotController extends Controller
{
    protected ChatbotService $chatbotService;

    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'message'   => 'required|string|max:1000',
            'access_id' => 'nullable|string',
            'id_soal'   => 'nullable|string',
            'id_level'  => 'nullable|string',
        ]);
 
        // Ambil mahasiswa dari user yang sedang login
        $user      = Auth::user();
        $mahasiswa = Mahasiswa::where('id_user', $user->id)->first();

        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan.',
            ], 404);
        }

        $respons = $this->chatbotService->chat(
            idMahasiswa: $mahasiswa->id,
            pesan:       $request->input('message'),
            accessId:    $request->input('access_id'),
            idSoal:      $request->input('id_soal'),
            idLevel:     $request->input('id_level'),
        );

        $this->appendAdaptiveConversationMessage(
            mahasiswa: $mahasiswa,
            accessId: $request->input('access_id'),
            studentMessage: (string) $request->input('message'),
            botResponse: (string) $respons,
        );

        return response()->json([
            'success' => true,
            'respons' => $respons,
        ]);
    }

    private function appendAdaptiveConversationMessage(
        Mahasiswa $mahasiswa,
        ?string $accessId,
        string $studentMessage,
        string $botResponse
    ): void {
        if (empty($accessId)) {
            return;
        }

        $accessLog = ChatbotAccessLog::query()
            ->where('id', $accessId)
            ->where('id_mahasiswa', $mahasiswa->id)
            ->where('type', 'adaptive')
            ->first();

        if (!$accessLog || !is_null($accessLog->closed_at)) {
            return;
        }

        $adaptiveLog = ChatbotAdaptiveLog::query()
            ->where('id_mahasiswa', $mahasiswa->id)
            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(detail, '$.access_id')) = ?", [$accessId])
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$adaptiveLog) {
            $fallbackQuery = ChatbotAdaptiveLog::query()
                ->where('id_mahasiswa', $mahasiswa->id)
                ->whereNull('durasi_menit')
                ->orderBy('created_at', 'desc');

            if ($accessLog->opened_at) {
                $fallbackQuery->where('created_at', '>=', $accessLog->opened_at->copy()->subMinutes(10));
            }

            $adaptiveLog = $fallbackQuery->first();
        }

        if (!$adaptiveLog) {
            return;
        }

        $detail = $adaptiveLog->detail;
        if (!is_array($detail)) {
            $detail = [];
        }

        $messages = [];
        if (isset($detail['messages']) && is_array($detail['messages'])) {
            $messages = $detail['messages'];
        }

        $now = Carbon::now();
        $messages[] = [
            'waktu' => $now->copy()->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s') . ' WIB',
            'created_at' => $now->toDateTimeString(),
            'mahasiswa_name' => $mahasiswa->name ?? 'Mahasiswa',
            'bot_name' => 'PseudoLearn Chatbot AI',
            'mahasiswa_message' => $studentMessage,
            'bot_response' => $botResponse,
            'access_id' => $accessId,
            'source' => 'adaptive_conversation',
        ];

        $detail['access_id'] = $accessId;
        $detail['messages'] = $messages;
        $detail['total_messages'] = count($messages);

        $adaptiveLog->update([
            'detail' => $detail,
        ]);
    }

    /**
     * Log ketika mahasiswa membuka chatbot
     */
    public function open(Request $request): JsonResponse
    {
        $request->validate([
            'type'     => 'nullable|in:biasa,adaptive',
            'id_soal'  => 'nullable|string',
            'id_level' => 'nullable|string',
        ]);

        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('id_user', $user->id)->first();

        if (!$mahasiswa) {
            return response()->json(['success' => false, 'message' => 'Data mahasiswa tidak ditemukan.'], 404);
        }

        $log = ChatbotAccessLog::create([
            'id_mahasiswa' => $mahasiswa->id,
            'id_level'     => $request->input('id_level'),
            'id_soal'      => $request->input('id_soal'),
            'type'         => $request->input('type', 'biasa'),
            'opened_at'    => Carbon::now(),
        ]);

        return response()->json([
            'success'   => true,
            'access_id' => $log->id,
        ]);
    }

    /**
     * Log ketika mahasiswa menutup chatbot
     */
    public function close(Request $request): JsonResponse
    {
        $request->validate([
            'access_id' => 'required|string',
        ]);

        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('id_user', $user->id)->first();

        if (!$mahasiswa) {
            return response()->json(['success' => false, 'message' => 'Data mahasiswa tidak ditemukan.'], 404);
        }

        $log = ChatbotAccessLog::where('id', $request->input('access_id'))
            ->where('id_mahasiswa', $mahasiswa->id)
            ->whereNull('closed_at')
            ->first();

        if (!$log) {
            return response()->json(['success' => false, 'message' => 'Log akses tidak ditemukan.'], 404);
        }

        $closedAt = Carbon::now();
        $durasiDetik = abs($log->opened_at->diffInSeconds($closedAt));
        $durasiMenit = (int) floor($durasiDetik / 60);

        $log->update([
            'closed_at'    => $closedAt,
            'durasi_menit' => $durasiMenit,
        ]);

        if ($log->type === 'adaptive') {
            $this->finalizeAdaptiveRealtimeLog(
                mahasiswaId: $mahasiswa->id,
                accessId: (string) $log->id,
                openedAt: $log->opened_at,
                closedAt: $closedAt,
                durasiMenit: $durasiMenit,
                durasiDetik: $durasiDetik,
            );
        }

        return response()->json(['success' => true]);
    }

    /**
     * Cek performa real-time mahasiswa untuk deteksi Struggling / Gaming the System
     */
    public function checkPerformance(Request $request): JsonResponse
    {
        $request->validate([
            'id_soal'       => 'required|string',
            'id_level'      => 'required|string',
            'elapsed_time'  => 'required|integer|min:0',
        ]);

        $user      = Auth::user();
        $mahasiswa = Mahasiswa::where('id_user', $user->id)->first();

        if (!$mahasiswa) {
            return response()->json(['success' => false, 'message' => 'Data mahasiswa tidak ditemukan.'], 404);
        }

        $result = $this->chatbotService->checkPerformance(
            idMahasiswa: $mahasiswa->id,
            idSoal:      $request->input('id_soal'),
            idLevel:     $request->input('id_level'),
            elapsedTime: (int) $request->input('elapsed_time'),
        );

        return response()->json([
            'success' => true,
            ...$result,
        ]);
    }

    /**
     * Kirim bimbingan materi adaptif otomatis untuk mahasiswa low performance
     */
    public function adaptiveGuide(Request $request): JsonResponse
    {
        $request->validate([
            'id_soal'  => 'required|string',
            'id_level' => 'required|string',
            'label'    => 'required|string|in:Struggling,Gaming the System',
            'elapsed_time' => 'nullable|integer|min:0',
            'total_drag' => 'nullable|integer|min:0',
            'access_id' => 'nullable|string',
        ]);

        $user      = Auth::user();
        $mahasiswa = Mahasiswa::where('id_user', $user->id)->first();

        if (!$mahasiswa) {
            return response()->json(['success' => false, 'message' => 'Data mahasiswa tidak ditemukan.'], 404);
        }

        $respons = $this->chatbotService->adaptiveChat(
            idMahasiswa: $mahasiswa->id,
            idSoal:      $request->input('id_soal'),
            idLevel:     $request->input('id_level'),
            label:       $request->input('label'),
        );

        $this->storeAdaptiveRealtimeLog(
            mahasiswa: $mahasiswa,
            idSoal: (string) $request->input('id_soal'),
            idLevel: (string) $request->input('id_level'),
            label: (string) $request->input('label'),
            respons: $respons,
            elapsedTime: $request->filled('elapsed_time') ? (int) $request->input('elapsed_time') : null,
            totalDrag: $request->filled('total_drag') ? (int) $request->input('total_drag') : null,
            accessId: $request->input('access_id'),
        );

        return response()->json([
            'success' => true,
            'respons' => $respons,
        ]);
    }

    private function storeAdaptiveRealtimeLog(
        Mahasiswa $mahasiswa,
        string $idSoal,
        string $idLevel,
        string $label,
        string $respons,
        ?int $elapsedTime,
        ?int $totalDrag,
        ?string $accessId
    ): void {
        $now = Carbon::now();
        $elapsedDetik = is_null($elapsedTime) ? null : max(0, $elapsedTime);
        $jumlahLangkah = is_null($totalDrag) ? 0 : max(0, $totalDrag);
        $waktuMulai = !is_null($elapsedDetik) ? $now->copy()->subSeconds($elapsedDetik) : null;
        $waktuSelesai = !is_null($elapsedDetik) ? $now->copy() : null;

        $kelas = null;
        if (!empty($mahasiswa->id_kelas)) {
            $kelas = Kelas::find($mahasiswa->id_kelas);
        }

        $level = Level::find($idLevel);
        $soal = Soal::find($idSoal);

        $normalizedLabel = $this->normalizeAdaptiveLabel($label);
        $pesanBimbingan = '[ADAPTIVE - ' . $normalizedLabel . '] Bimbingan materi otomatis';

        $payloadDetail = [
            'source' => 'realtime-adaptive-trigger',
            'access_id' => $accessId,
            'triggered_at' => $now->toDateTimeString(),
            'waktu_detik' => $elapsedDetik,
            'waktu_akses_detik' => $elapsedDetik,
            'jumlah_langkah' => $jumlahLangkah,
            'messages' => [
                [
                    'waktu' => $now->copy()->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s') . ' WIB',
                    'created_at' => $now->toDateTimeString(),
                    'pesan' => $pesanBimbingan,
                    'respons' => $respons,
                ],
            ],
            'total_messages' => 1,
        ];

        $adaptiveAccessCount = ChatbotAccessLog::where('id_mahasiswa', $mahasiswa->id)
            ->where('type', 'adaptive')
            ->count();

        ChatbotAdaptiveLog::create([
            'id_mahasiswa' => $mahasiswa->id,
            'nim' => $mahasiswa->nim,
            'nama' => $mahasiswa->name,
            'id_kelas' => $mahasiswa->id_kelas,
            'kelas' => $kelas?->name,
            'id_level' => $idLevel,
            'level_soal' => $level?->name,
            'id_soal' => $idSoal,
            'jenis_soal' => $soal?->judul,
            'jumlah_langkah' => $jumlahLangkah,
            'waktu_mulai' => $waktuMulai,
            'waktu_selesai' => $waktuSelesai,
            'labeling' => $normalizedLabel,
            'durasi_menit' => null,
            'total_akses_chatbot_adaptive' => max(0, $adaptiveAccessCount),
            'pesan_bimbingan' => $pesanBimbingan,
            'detail' => $payloadDetail,
        ]);
    }

    private function finalizeAdaptiveRealtimeLog(
        string $mahasiswaId,
        string $accessId,
        ?Carbon $openedAt,
        Carbon $closedAt,
        int $durasiMenit,
        int $durasiDetik
    ): void {
        $adaptiveLogQuery = ChatbotAdaptiveLog::query()
            ->where('id_mahasiswa', $mahasiswaId)
            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(detail, '$.access_id')) = ?", [$accessId])
            ->orderBy('created_at', 'desc');

        $adaptiveLog = $adaptiveLogQuery->first();

        if (!$adaptiveLog) {
            $fallbackQuery = ChatbotAdaptiveLog::query()
                ->where('id_mahasiswa', $mahasiswaId)
                ->whereNull('durasi_menit')
                ->orderBy('created_at', 'desc');

            if ($openedAt) {
                $fallbackQuery->where('created_at', '>=', $openedAt->copy()->subMinutes(10));
            }

            $adaptiveLog = $fallbackQuery->first();
        }

        if (!$adaptiveLog) {
            return;
        }

        $detail = $adaptiveLog->detail;
        if (!is_array($detail)) {
            $detail = [];
        }

        $detail['access_id'] = $accessId;
        $detail['popup_opened_at'] = $openedAt ? $openedAt->toDateTimeString() : null;
        $detail['popup_closed_at'] = $closedAt->toDateTimeString();
        $detail['durasi_detik'] = max(0, $durasiDetik);

        $adaptiveLog->update([
            'durasi_menit' => max(0, $durasiMenit),
            'detail' => $detail,
        ]);
    }

    private function normalizeAdaptiveLabel(string $label): string
    {
        $normalized = trim($label);

        if ($normalized === '') {
            return '-';
        }

        if (preg_match('/\[ADAPTIVE\s*-\s*(.*?)\]/i', $normalized, $matches) === 1) {
            $candidate = trim((string) ($matches[1] ?? ''));
            if ($candidate !== '') {
                return $candidate;
            }
        }

        return $normalized;
    }
}