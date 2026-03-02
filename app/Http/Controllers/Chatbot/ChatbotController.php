<?php

namespace App\Http\Controllers\Chatbot;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}