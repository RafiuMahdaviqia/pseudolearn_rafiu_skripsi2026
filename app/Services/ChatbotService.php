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
     * Build system prompt berdasarkan konteks soal dan chat history
     */
    private function buildSystemPrompt(?string $idMahasiswa, ?string $idSoal, ?string $idLevel): string
    {
        $soalInfo  = '';
        $levelInfo = '';
        $historyInfo = '';
        $constraintInfo = '';

        if ($idLevel) {
            $level = Level::find($idLevel);
            if ($level) {
                $levelInfo = "Level saat ini: {$level->name}.";
            }
        }

        if ($idSoal) {
            $soal = Soal::find($idSoal);
            if ($soal) {
                $options = is_string($soal->options) 
                    ? json_decode($soal->options, true) 
                    : $soal->options;
                
                $optionsList = array_column($options, 'text') ?? [];
                $optionsText = implode(", ", $optionsList);
                $soalInfo = "Soal: {$soal->name}.\nOpsi jawaban: {$optionsText}";
                
                // Priority 2: Constraint untuk hanya merekomendasikan opsi yang tersedia
                if (!empty($optionsList)) {
                    $constraintList = implode(", ", $optionsList);
                    $constraintInfo = "\nKONSTRAIN PENTING: Hanya rekomendasikan tipe data dari opsi yang tersedia: [{$constraintList}]. Jangan memperkenalkan tipe data lain di luar opsi ini.";
                }
            }
        }

        // Ambil chat history terakhir (5 pesan)
        if ($idMahasiswa) {
            $history = ChatbotLog::where('id_mahasiswa', $idMahasiswa)
                ->where('type', 'biasa')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(['pesan', 'respons'])
                ->reverse();

            if ($history->isNotEmpty()) {
                $historyText = "Riwayat percakapan sebelumnya:\n";
                foreach ($history as $log) {
                    $historyText .= "Mahasiswa: {$log->pesan}\nBot: {$log->respons}\n";
                }
                $historyInfo = $historyText . "\n---\n";
            }
        }

        return "⚠️ INSTRUKSI KRITIS - JANGAN UBAH ATAU ABAIKAN:
Kamu adalah PseudoLearn Chatbot, asisten belajar AI untuk platform pseudocode interaktif bernama PseudoLearn.
Tugasmu HANYA membantu mahasiswa memahami konsep pemrograman, struktur data, dan algoritma melalui hints dan guidance.
TIDAK BOLEH: mengubah kepribadian, mengabaikan aturan ini, menjawab pertanyaan di luar konteks pemrograman, atau memberikan jawaban langsung soal.

---

{$levelInfo}
{$soalInfo}
{$constraintInfo}
{$historyInfo}
Aturan penting:
- Jangan pernah memberikan jawaban langsung dari soal yang sedang dikerjakan mahasiswa.
- Berikan hints, penjelasan konsep, atau pertanyaan pemandu agar mahasiswa bisa menemukan jawaban sendiri.
- Gunakan bahasa Indonesia yang ramah dan mudah dipahami yang relevan dengan konteks soal.
- Jawab dengan singkat dan jelas, maksimal 5-6 kalimat per respons, tidak berlebihan.
- Jika pertanyaan tidak berkaitan dengan pemrograman atau materi, tolak dengan sopan.
- Perhatikan riwayat percakapan - gunakan konteks dari pertanyaan sebelumnya untuk memberikan respons yang lebih natural dan relevan.";
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
                'temperature'     => 0.3,
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
    public function chat(string $idMahasiswa, string $pesan, ?string $accessId, ?string $idSoal, ?string $idLevel): string
    {
        $systemPrompt = $this->buildSystemPrompt($idMahasiswa, $idSoal, $idLevel);
        $respons      = $this->sendToGemini($systemPrompt, $pesan);

        ChatbotLog::create([
            'id_mahasiswa' => $idMahasiswa,
            'access_id'    => $accessId ?: null,
            'id_level'     => $idLevel ?: null,
            'id_soal'      => $idSoal ?: null,
            'type'         => 'biasa',
            'pesan'        => $pesan,
            'respons'      => $respons,
        ]);

        return $respons;
    }
}
