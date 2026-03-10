<?php

namespace App\Http\Controllers\Chatbot;

use App\Http\Controllers\Controller;
use App\Models\ChatbotAccessLog;
use App\Models\Mahasiswa;
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
            'message'  => 'required|string|max:1000',
            'id_soal'  => 'nullable|string',
            'id_level' => 'nullable|string',
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
            idSoal:      $request->input('id_soal'),
            idLevel:     $request->input('id_level'),
        );

        return response()->json([
            'success' => true,
            'respons' => $respons,
        ]);
    }

    /**
     * Log ketika mahasiswa membuka chatbot
     */
    public function open(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'nullable|in:biasa,adaptive',
        ]);

        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('id_user', $user->id)->first();

        if (!$mahasiswa) {
            return response()->json(['success' => false, 'message' => 'Data mahasiswa tidak ditemukan.'], 404);
        }

        $log = ChatbotAccessLog::create([
            'id_mahasiswa' => $mahasiswa->id,
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

        return response()->json(['success' => true]);
    }
}