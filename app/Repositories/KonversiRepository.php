<?php

namespace App\Repositories;

use App\Models\Soal;
use App\Models\Kelas;
use App\Models\Nyawa;
use App\Models\Konversi;
use App\Models\LabelSkor;
use App\Models\Mahasiswa;
use App\Core\BaseResponse;
use App\Models\Pencapaian;
use Illuminate\Support\Str;
use App\Models\DebugKonversi;
use App\Models\UjianKonversi;
use App\Services\DecoyAnswerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Jobs\DeletePencapaianKonversi;
use Symfony\Component\Process\Process;
use App\Jobs\GeneratePencapaianKonversi;
use Prettus\Repository\Eloquent\BaseRepository;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Class KelasRepository.
 *
 * @package namespace App\Repositories;
 */
class KonversiRepository extends BaseRepository
{

    protected $model;
    protected $soalModel;
    protected $mahasiswaModel;
    protected $ujianKonversiModel;
    protected $debugKonversiModel;
    protected $labelSkorModel;

    public function __construct()
    {
        $this->model = new Konversi();
        $this->soalModel = new Soal();
        $this->mahasiswaModel = new Mahasiswa();
        $this->ujianKonversiModel = new UjianKonversi();
        $this->debugKonversiModel = new DebugKonversi();
        $this->labelSkorModel = new LabelSkor();
    }

    /**
     * Specify the model class name.
     *
     * @return string
     */
    public function model()
    {
        return $this->model;
    }

    /**
     * Boot up the repository, pushing criteria.
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */

    public function table($request)
    {
        $opr = $this->model->setView('v_konversi');

        $level = $request->input('id_level');
        if (!is_null($level) && $level !== '') {
            $opr = $opr->where('id_level', $level);
        }

        $opr = $opr->draw();

        return $opr;
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            // Ambil array jawaban
            $jawabanValues = $request->input('jawaban', []);

            // Satukan jadi array terstruktur (increment)
            $jawabanStructured = [];
            $increment = 1;
            foreach ($jawabanValues as $val) {
                // Skip jika benar-benar kosong (optional)
                if ($val === null || trim($val) === '') {
                    continue;
                }
                $jawabanStructured[] = [
                    $increment => $val
                ];
                $increment++;
            }

            // $runJava = $this->runJavaCode($jawabanValues, $request->input('soal_id'));

            // if (!$runJava['status']) {
            //     throw new \Exception('Java execution failed: ' . ($runJava['error'] ?? 'Unknown error'));
            // }

            $payload = [
                // id otomatis oleh boot() (UUID)
                'id_level' => $request->input('level_id'),
                'id_soal'  => $request->input('soal_id'),
                'jawaban'  => $jawabanStructured,
                'output'   => $request->input('output', ''),
                'bobot'    => (int)$request->input('bobot'),
            ];

            $data = $this->model->create($payload);

            DB::commit();

            // Dispatch job per kelas
            $kelasList = Kelas::all();
            foreach ($kelasList as $kelas) {
                GeneratePencapaianKonversi::dispatch($data, $kelas->id);
            }

            return BaseResponse::created($data);
        } catch (\Exception $e) {
            DB::rollBack();
            return BaseResponse::errorTransaction($e);
        }
    }

    public function runJavaCode($request)
    {
        try {
            $codes = $request->input('codes', []);
            $soalId = $request->input('soal_id');

            // Bikin soalId aman untuk nama class Java
            $safeSoalId = str_replace('-', '_', $soalId);

            // Gabungkan value dari array codes
            $mainCode = "";
            foreach ($codes as $code) {
                if (isset($code['value']) && trim($code['value']) !== '') {
                    $mainCode .= "        " . $code['value'] . "\n";
                }
            }

            $lines = explode("\n", $mainCode);
            $fixed = [];
            $varTypes = [];

            // Step 1: ambil deklarasi variabel
            foreach ($lines as $line) {
                $trim = trim($line);

                // cocokkan deklarasi, misal: int uang_bayar;  float pajak_jual;
                if (preg_match('/^(int|float|double)\s+([a-zA-Z_][a-zA-Z0-9_]*)/', $trim, $m)) {
                    $varTypes[$m[2]] = $m[1]; // simpan "uang_bayar" => "int"
                }
            }

            // Step 2: cek assignment dan tambahkan cast jika perlu
            foreach ($lines as $line) {
                $trim = trim($line);

                if (preg_match('/^([a-zA-Z_][a-zA-Z0-9_]*)\s*=\s*(.+);$/', $trim, $m)) {
                    $var = $m[1];
                    $expr = $m[2];

                    if (isset($varTypes[$var])) {
                        $targetType = $varTypes[$var];

                        if ($targetType === 'int') {
                            $line = "        $var = (int)($expr);";
                        } elseif ($targetType === 'float') {
                            $line = "        $var = (float)($expr);";
                        } elseif ($targetType === 'double') {
                            $line = "        $var = (double)($expr);";
                        }
                    }
                }

                $fixed[] = $line;
            }

            $mainCode = implode("\n", $fixed);

            // Buat kode java lengkap
            $javaCode = <<<EOD
            public class Main_$safeSoalId {
                public static void main(String[] args) {
                    $mainCode
                }
            }
            EOD;

            // Tentukan path file sementara
            $dirPath = storage_path("app/java/$soalId");
            if (!is_dir($dirPath)) {
                mkdir($dirPath, 0777, true);
            }

            $filePath = $dirPath . "/Main_$safeSoalId.java";
            file_put_contents($filePath, $javaCode);

            // Compile Java
            $compile = new Process(['javac', $filePath], $dirPath);
            $compile->run();
            if (!$compile->isSuccessful()) {
                throw new ProcessFailedException($compile);
            }

            // Jalankan Java
            $process = new Process(['java', '-cp', $dirPath, "Main_$safeSoalId"], $dirPath);
            $process->run();
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $output = $process->getOutput();

            // Hapus file class jika berhasil
            @unlink($dirPath . "/Main_$safeSoalId.class");

            $output = [
                'status' => true,
                'output' => $output,
                'path'   => $filePath
            ];
            return BaseResponse::json($output);
        } catch (\Exception $e) {
            return BaseResponse::errorTransaction($e);
        }
    }


    public function getSoalByLevel($request)
    {
        $levelId = $request->query('level_id');

        // Ambil id_soal yang sudah ada di tabel konversi
        $usedSoalIds = $this->model->pluck('id_soal')->toArray();

        // Jika ada request soal_id, ambil soal dengan id tersebut tanpa filter usedSoalIds
        $soalId = $request->query('soal_id');
        if (!empty($soalId)) {
            $soal = $this->soalModel
            ->where('id', $soalId)
            ->where('id_level', $levelId)
            ->get(['id', 'judul']);
        } else {
            // Ambil soal sesuai level, kecuali yang sudah ada di konversi
            $soal = $this->soalModel
            ->where('id_level', $levelId)
            ->whereNotIn('id', $usedSoalIds)
            ->get(['id', 'judul']);
        }

        return BaseResponse::json($soal);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $opr = $this->model->findOrFail($id);
            $data = $this->model->destroy($id);
            DB::commit();

            // Dispatch job per kelas
            $kelasList = Kelas::all();
            foreach ($kelasList as $kelas) {
                DeletePencapaianKonversi::dispatch($opr, $kelas->id);
            }

            return BaseResponse::deleted($data);
        } catch (\Exception $e) {
            DB::rollBack();
            return BaseResponse::errorTransaction($e);
        }
    }

    public function updateKonversi($request)
    {
        DB::beginTransaction();
        try {
            // Ambil array jawaban
            $jawabanValues = $request->input('jawaban', []);

            // Satukan jadi array terstruktur (increment)
            $jawabanStructured = [];
            $increment = 1;
            foreach ($jawabanValues as $val) {
                // Skip jika benar-benar kosong (optional)
                if ($val === null || trim($val) === '') {
                    continue;
                }
                $jawabanStructured[] = [
                    $increment => $val
                ];
                $increment++;
            }

            $payload = [
                // id otomatis oleh boot() (UUID)
                'id_level' => $request->input('level_id'),
                'id_soal'  => $request->input('soal_id'),
                'jawaban'  => $jawabanStructured,
                'output'   => $request->input('output', ''),
                'bobot'    => (int)$request->input('bobot'),
            ];

            $data = $this->model->where('id', $request->id)->update($payload);

            DB::commit();
            return BaseResponse::updated($data);
        } catch (\Exception $e) {
            DB::rollBack();
            return BaseResponse::errorTransaction($e);
        }
    }

    public function submitKonversi($request)
    {
        DB::beginTransaction();
        try {
            $idMahasiswa = $this->mahasiswaModel->where('id_user', Auth::id())->value('id');
            $soalKonversi = $this->model->where('id', $request->input('id_soal_konversi'))->first();

            if(!$soalKonversi){
                DB::rollBack();
                return BaseResponse::errorMessage('Soal konversi tidak ditemukan');
            }

            $kunciJawaban = $soalKonversi->jawaban;
            $kodeLangkah = $request->input('kode_langkah', []);

            $errors = [];

            // Cek setiap baris: bandingkan array kata per baris, abaikan spasi
            foreach ($kunciJawaban as $idx => $baris) {
                $nomorBaris = array_key_first($baris);
                $isiKunci   = $baris[$nomorBaris];
                $jawabanUser = $kodeLangkah[$idx] ?? null;

                // Normalisasi: hapus semua spasi
                $kunciNoSpace = str_replace(' ', '', $isiKunci);
                $userNoSpace  = str_replace(' ', '', $jawabanUser);

                // Cek persis sama (case sensitive, termasuk tanda baca)
                if ($userNoSpace !== $kunciNoSpace) {
                    $errors[] = [
                        'message' => "Jawaban salah pada baris ke {$nomorBaris}",
                        'index'   => $idx
                    ];
                }
            }

            if (!empty($errors)) {
                DB::rollBack();

                $nyawa = Nyawa::where('id_user', Auth::id())->first();

                if ($nyawa->nyawa > 0) {
                    $nyawa->nyawa -= 1;

                    // kalau nyawa belum penuh dan tidak ada timer → set regen
                    if ($nyawa->next_regen_at === null && $nyawa->nyawa < $nyawa->max_nyawa) {
                        $nyawa->next_regen_at = now()->addMinutes(10);
                    }

                    $nyawa->save();
                }

                $decoy = $this->buildDecoyForGaming($idMahasiswa, $soalKonversi);

                return BaseResponse::json([
                    'success' => false,
                    'message' => [
                        'message' => 'Terdapat jawaban salah',
                        'errors'  => $errors,
                    ],
                    'decoy' => $decoy,
                ]);
            }

            // Analisa similarity per langkah
            $debugData = $this->analyzeDebug(
                $kodeLangkah,
                array_map(fn($item) => array_values($item)[0], $kunciJawaban)
            );

            // Hitung rata-rata similarity
            $total_similarity = 0;
            $valid_steps = 0;
            foreach ($debugData as $step) {
                if (isset($step['similarity'])) {
                    $total_similarity += $step['similarity'];
                    $valid_steps++;
                }
            }
            $average_similarity = $valid_steps > 0 ? ($total_similarity / $valid_steps) : 0;
            $final_score = round($average_similarity * $soalKonversi->bobot, 2);
            $formatted_score = number_format($final_score, 2, '.', '');

            // Jika similarity < 1, anggap ada jawaban salah
            // if ($average_similarity < 1) {
            //     DB::rollBack();
            //     return BaseResponse::errorMessage([
            //         'message' => 'Terdapat jawaban salah atau kurang tepat',
            //         'similarity' => $average_similarity,
            //         'debug' => $debugData
            //     ]);
            // }

            // Jika semua jawaban benar, jalankan runJavaCode
            $runJava = $this->runJavaCode(new \Illuminate\Http\Request([
                'codes' => collect($kodeLangkah)->map(fn($value) => ['value' => $value])->toArray(),
                'soal_id' => $soalKonversi->id_soal
            ]));

            // Ambil output dari JsonResponse
            $runJavaData = $runJava->getData(true);

            // Normalisasi output dari runJavaCode (bisa terbungkus di key 'data')
            $javaOutput = $runJavaData['data']['output'] ?? ($runJavaData['output'] ?? '');
            $runJavaData['output'] = $javaOutput;

            // Upsert UjianKonversi agar pasti ter-update/terbentuk
            $ujianKonversi = $this->ujianKonversiModel->updateOrCreate(
                [
                    'id_soal_konversi' => $soalKonversi->id,
                    'id_mahasiswa'     => $idMahasiswa,
                ],
                [
                    'id_level'         => $soalKonversi->id_level,
                    'jawaban'          => $kodeLangkah,
                    'output'           => $javaOutput,
                    'nilai'            => $formatted_score,
                    'waktu'            => $request->input('waktu', null),
                ]
            );

            // Upsert DebugKonversi agar debug selalu terbarui
            $this->debugKonversiModel->updateOrCreate(
                [
                    'id_soal_konversi' => $soalKonversi->id,
                    'id_mahasiswa'     => $idMahasiswa,
                ],
                [
                    'id_level'          => $soalKonversi->id_level,
                    'id_soal'           => $soalKonversi->id_soal,
                    'id_ujian_konversi' => $ujianKonversi->id,
                    'debug'             => $debugData,
                ]
            );

            $dataPencapaian = Pencapaian::where('id_mahasiswa', $idMahasiswa)
                ->where('id_level', $soalKonversi->id_level)
                ->where('id_soal', $soalKonversi->id_soal)
                ->where('id_soal_konversi', $soalKonversi->id)
                ->where('category', 'konversi')
                ->first();

            $returnKonversi = null;
            if ($dataPencapaian && $dataPencapaian->status == 0) {
                $dataPencapaian->update([
                    'status' => 1,
                    'updated_at' => now(),
                ]);

                $returnKonversi = [
                    'id' => $dataPencapaian->id,
                ];
            }

            DB::commit();
            return BaseResponse::json([
                'status' => true,
                'message' => 'Selamat! Semua jawaban benar.',
                'java_output' => $runJavaData['output'] ?? '',
                'konversi' => $returnKonversi,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return BaseResponse::errorMessage($e->getMessage());
        }
    }
    private function buildDecoyForGaming($idMahasiswa, $soalKonversi): ?array
    {
        $label = $this->labelSkorModel
            ->where('id_level', $soalKonversi->id_level)
            ->where('id_soal', $soalKonversi->id_soal)
            ->where('id_mahasiswa', $idMahasiswa)
            ->orderByDesc('created_at')
            ->value('label');

        if ($label !== 'Gaming the System') {
            return null;
        }

        $decoyService = new DecoyAnswerService();

        // Extract kunci lines from the structured jawaban array
        $kunciLines = array_map(function ($item) {
            return trim((string) (is_array($item) ? reset($item) : $item));
        }, $soalKonversi->jawaban ?? []);

        $decoyLines = $decoyService->makeDecoyLines($kunciLines);

        if (empty(array_filter($decoyLines))) {
            return null;
        }

        return [
            'kode_langkah' => $decoyLines,
        ];
    }

    protected function generateTokens(string $text): array
    {
        $pattern = '/(\s+|[a-zA-Z0-9_]+|[+\-*\/=<>!&|^~(){}[\],.;:])/';
        preg_match_all($pattern, $text, $matches);
        return $matches[0] ?? [];
    }

    // 2. K-Gram generator
    protected function generateKGrams(string $text, int $k = 2): array
    {
        $length = strlen($text);
        $kgrams = [];
        for ($i = 0; $i <= $length - $k; $i++) {
            $kgrams[] = substr($text, $i, $k);
        }
        return $kgrams;
    }

    // 3. Hash calculation
    protected function calculateHashes(array $kgrams): array
    {
        return array_map(fn($gram) => crc32($gram), $kgrams);
    }

    // 4. Apply window
    protected function applyWindow(array $hashes, int $windowSize = 3): array
    {
        $windows = [];
        $n = count($hashes);
        for ($i = 0; $i <= $n - $windowSize; $i++) {
            $windows[] = array_slice($hashes, $i, $windowSize);
        }
        return $windows;
    }

    // 5. Fingerprint generator
    protected function generateFingerprints(array $windows): array
    {
        $fingerprints = [];
        foreach ($windows as $window) {
            if (!empty($window)) {
                $fingerprints[] = min($window);
            }
        }
        return array_unique($fingerprints);
    }

    // 6. Jaccard similarity
    protected function calculateJaccard(array $set1, array $set2): float
    {
        if (empty($set1) && empty($set2)) return 1.0;
        $intersection = count(array_intersect($set1, $set2));
        $union = count(array_unique(array_merge($set1, $set2)));
        return $union > 0 ? $intersection / $union : 0.0;
    }

    // 7. Analisa debug per jawaban
    public function analyzeDebug(array $userJawaban, array $kunciJawaban): array
    {
        $alphabet = range('a', 'z');
        $debugData = [];

        foreach ($userJawaban as $idx => $answer) {
            $correct = $kunciJawaban[$idx] ?? null;
            $field   = 'jawaban_' . $alphabet[$idx];

            if (!$answer && !$correct) continue;

            $stepDebug = [
                'step'           => $idx + 1,
                'user_answer'    => $answer,
                'correct_answer' => $correct,
                'similarity'     => 0,
                'user_tokens'    => [],
                'correct_tokens' => [],
                'user_kgrams'    => [],
                'correct_kgrams' => [],
                'user_hashes'    => [],
                'correct_hashes' => [],
                'user_windows'   => [],
                'correct_windows'=> [],
                'user_fingerprints' => [],
                'correct_fingerprints' => [],
            ];

            if ($answer && $correct) {
                // token
                $userTokens    = $this->generateTokens($answer);
                $correctTokens = $this->generateTokens($correct);

                // kgram
                $userKgrams    = $this->generateKGrams($answer);
                $correctKgrams = $this->generateKGrams($correct);

                // hash
                $userHashes    = $this->calculateHashes($userKgrams);
                $correctHashes = $this->calculateHashes($correctKgrams);

                // window
                $userWindows   = $this->applyWindow($userHashes);
                $correctWindows= $this->applyWindow($correctHashes);

                // fingerprints
                $userFP        = $this->generateFingerprints($userWindows);
                $correctFP     = $this->generateFingerprints($correctWindows);

                // similarity
                $similarity    = $this->calculateJaccard($userFP, $correctFP);

                // simpan detail
                $stepDebug['user_tokens']        = $userTokens;
                $stepDebug['correct_tokens']     = $correctTokens;
                $stepDebug['user_kgrams']        = $userKgrams;
                $stepDebug['correct_kgrams']     = $correctKgrams;
                $stepDebug['user_hashes']        = $userHashes;
                $stepDebug['correct_hashes']     = $correctHashes;
                $stepDebug['user_windows']       = $userWindows;
                $stepDebug['correct_windows']    = $correctWindows;
                $stepDebug['user_fingerprints']  = $userFP;
                $stepDebug['correct_fingerprints']= $correctFP;
                $stepDebug['similarity']         = $similarity;
            }

            $debugData[] = $stepDebug;
        }

        return $debugData;
    }

    public function tableUjianKonversi($request)
    {
        $opr = $this->mahasiswaModel->setView('v_mahasiswa');

        $kelas = $request->input('kelas');
        if (!is_null($kelas) && $kelas !== '') {
            $opr = $opr->where('id_kelas', $kelas);
        }

        // $level = $request->input('level');
        // if (!is_null($level) && $level !== '') {
        //     $opr = $opr->where('id_level', $level);
        // }

        // $soal = $request->input('soal');
        // if (!is_null($soal) && $soal !== '') {
        //     $opr = $opr->where('id_soal', $soal);
        // }

        $opr = $opr->draw();

        return $opr;
    }

    public function tableDetail($request)
    {
        $mahasiswaId = $request->input('id_mahasiswa');
        $levelId = $request->input('id_level');
        $soalId = $request->input('id_soal');

        $opr = $this->ujianKonversiModel->setView('v_ujian_konversi')
            ->where('id_mahasiswa', $mahasiswaId)
            ->orderBy('created_at', 'asc');

        if (!is_null($levelId) && $levelId !== '') {
            $opr = $opr->where('id_level', $levelId);
        }

        if (!is_null($soalId) && $soalId !== '') {
            $opr = $opr->where('id_soal', $soalId);
        }

        $opr = $opr->draw();

        return $opr;
    }
}
