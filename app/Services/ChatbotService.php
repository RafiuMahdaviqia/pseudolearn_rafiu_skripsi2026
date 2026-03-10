<?php

namespace App\Services;

use App\Models\ChatbotLog;
use App\Models\Mahasiswa;
use App\Models\Soal;
use App\Models\Level;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    /**
     * Build system prompt berdasarkan konteks soal
     */
    private function buildSystemPrompt(?string $idSoal, ?string $idLevel): string
    {
        $soalInfo  = '';
        $levelInfo = '';

        if ($idLevel) {
            $level = Level::find($idLevel);
            if ($level) {
                $levelInfo = "Level saat ini: {$level->name}.";
            }
        }

        if ($idSoal) {
            $soal = Soal::find($idSoal);
            if ($soal) {
                $soalInfo = "Judul soal: {$soal->name}.";
            }
        }

        return "Kamu adalah PseudoLearn Chatbot, asisten belajar AI untuk platform pseudocode interaktif bernama PseudoLearn.
Tugasmu adalah membantu mahasiswa memahami konsep pemrograman, struktur data, dan algoritma.
{$levelInfo}
{$soalInfo}
Aturan penting:
- Jangan pernah memberikan jawaban langsung dari soal yang sedang dikerjakan mahasiswa.
- Berikan hints, penjelasan konsep, atau pertanyaan pemandu agar mahasiswa bisa menemukan jawaban sendiri.
- Gunakan bahasa Indonesia yang ramah dan mudah dipahami.
- Jawab dengan singkat dan jelas, maksimal 5-6 kalimat per respons.
- Jika pertanyaan tidak berkaitan dengan pemrograman atau materi, tolak dengan sopan.";
    }

    /**
     * Kirim pesan ke Gemini API
     */
    private function sendToGemini(string $systemPrompt, string $userMessage): string
    {
        $apiKey = config('services.gemini.api_key');
        $model  = config('services.gemini.model', 'gemini-2.5-flash');
        $url    = config('services.gemini.url') . $model . ':generateContent?key=' . $apiKey;

        $payload = [
            'system_instruction' => [
                'parts' => [
                    ['text' => $systemPrompt]
                ]
            ],
            'contents' => [
                [
                    'role'  => 'user',
                    'parts' => [
                        ['text' => $userMessage]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature'     => 0.7,
                'maxOutputTokens' => 512,
            ]
        ];

        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::timeout(30)->post($url, $payload);

        if ($response->failed()) {
            Log::error('Gemini API error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return 'Maaf, saya sedang tidak dapat merespons saat ini. Silakan coba lagi.';
        }

        $data = $response->json();

        return $data['candidates'][0]['content']['parts'][0]['text']
            ?? 'Maaf, saya tidak dapat memproses pertanyaan kamu saat ini.';
    }

    /**
     * Main method: proses chat, simpan log, return respons
     */
    public function chat(string $idMahasiswa, string $pesan, ?string $idSoal, ?string $idLevel): string
    {
        $systemPrompt = $this->buildSystemPrompt($idSoal, $idLevel);
        $respons      = $this->sendToGemini($systemPrompt, $pesan);

        ChatbotLog::create([
            'id_mahasiswa' => $idMahasiswa,
            'id_level'     => $idLevel ?: null,
            'id_soal'      => $idSoal ?: null,
            'type'         => 'biasa',
            'pesan'        => $pesan,
            'respons'      => $respons,
        ]);

        return $respons;
    }
}