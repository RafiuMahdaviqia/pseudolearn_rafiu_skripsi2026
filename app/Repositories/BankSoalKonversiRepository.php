<?php

namespace App\Repositories;

use App\Models\BankSoalKonversi;
use App\Core\BaseResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

class BankSoalKonversiRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new BankSoalKonversi();
    }

    public function table($request)
{
    $query = $this->model
        ->select(
            'bank_soal_konversi.id',
            'level.name as level_name',
            'soal.judul as soal',
            'bank_soal_konversi.jawaban',
            'bank_soal_konversi.output',
            'bank_soal_konversi.difficulty'
        )
        ->leftJoin('level', 'level.id', '=', 'bank_soal_konversi.id_level')
        ->leftJoin('soal', 'soal.id', '=', 'bank_soal_konversi.id_soal');

    // Filter Level
    $level = $request->input('level');
    if (!is_null($level) && $level !== '') {
        $query->where('bank_soal_konversi.id_level', $level);
    }

    // Order
    $query->orderBy('bank_soal_konversi.difficulty', 'asc')
          ->orderBy('bank_soal_konversi.created_at', 'asc');

    return DataTables::of($query)
        ->addIndexColumn()
        ->filterColumn('level_name', function ($query, $keyword) {
            $query->where('level.name', 'like', "%{$keyword}%");
        })

        ->filterColumn('soal', function ($query, $keyword) {
            $query->where('soal.judul', 'like', "%{$keyword}%");
        })

        ->editColumn('jawaban', function ($item) {
            return $this->formatJawabanHtml($item->jawaban);
        })

        ->editColumn('output', function ($item) {
            return $item->output ?? '-';
        })

        ->rawColumns(['jawaban'])
        ->make(true);
}

    protected function formatJawabanHtml($jawaban): string
    {
        $lines = $this->normalizeJawabanLines($jawaban);

        if (empty($lines)) {
            return '-';
        }

        $formatted = collect($lines)
            ->map(function ($line, $index) {
                $lineNumber = str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);

                return $lineNumber . '. ' . $line;
            })
            ->implode("\n");

        return '<pre class="bank-soal-jawaban-code mb-0">' . e($formatted) . '</pre>';
    }

    protected function normalizeJawabanLines($jawaban): array
    {
        if (is_array($jawaban)) {
            $rawLines = $jawaban;
        } else {
            $text = (string) $jawaban;

            if (trim($text) === '') {
                return [];
            }

            $decoded = json_decode(trim($text), true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $rawLines = $decoded;
            } else {
                $normalized = str_replace(["\r\n", "\r"], "\n", $text);
                $rawLines = explode("\n", $normalized);
            }
        }

        return collect($rawLines)
            ->map(function ($line) {
                if (is_array($line) || is_object($line)) {
                    $line = json_encode($line, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }

                return rtrim((string) $line);
            })
            ->filter(fn($line) => trim($line) !== '')
            ->values()
            ->all();
    }

    public function getOrderListByLevel(string $levelId)
    {
        return DB::table('bank_soal_konversi')
            ->leftJoin('soal', 'soal.id', '=', 'bank_soal_konversi.id_soal')
            ->where('bank_soal_konversi.id_level', $levelId)
            ->orderBy('bank_soal_konversi.difficulty', 'asc')
            ->orderBy('bank_soal_konversi.created_at', 'asc')
            ->select(
                'bank_soal_konversi.id',
                'bank_soal_konversi.difficulty',
                'bank_soal_konversi.id_soal',
                'soal.judul as judul'
            )
            ->get();
    }

    public function saveOrder(array $difficulties): bool
    {
        DB::beginTransaction();
        try {
            foreach ($difficulties as $item) {
                if (!isset($item['id'], $item['difficulty'])) continue;
                DB::table('bank_soal_konversi')
                    ->where('id', $item['id'])
                    ->update(['difficulty' => (int) $item['difficulty']]);
            }
            DB::commit();
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            return false;
        }
    }

    public function getSoalByLevel($levelId)
    {
        return DB::table('soal')
            ->where('id_level', $levelId)
            ->select('id', 'judul')
            ->orderBy('judul')
            ->get();
    }

    public function store($payload)
    {
        return $this->model->create($payload);
    }

    public function update($payload, $id)
    {
        $data = $this->model->find($id);
        if (!$data) return false;
        return $data->update($payload);
    }

    public function destroy($id)
    {
        $record = $this->model->find($id);
        if (!$record) return false;
        return $record->delete();
    }

    public function detail($id)
    {
        return $this->model->find($id);
    }

    public function runJavaCode($request)
    {
        try {
            $codes      = $request->input('codes', []);
            $soalId     = $request->input('soal_id');
            $soalInput  = $request->input('scanner_input', '');

            $safeSoalId = str_replace('-', '_', $soalId);
            $className   = 'Main_' . $safeSoalId;

            // Gabungkan semua baris kode
            $rawJoined = collect($codes)
                ->pluck('value')
                ->filter(fn($line) => $line !== null && trim($line) !== '')
                ->implode("\n");

            // Jika user paste full class, pertahankan seluruh source dan hanya sesuaikan nama class.
            $mainCode = '';
            $isFullClassSource = preg_match('/\bclass\s+[A-Za-z_][A-Za-z0-9_]*/i', $rawJoined) === 1;

            if ($isFullClassSource) {
                $mainCode = $this->renameJavaClassName($rawJoined, $className);
            } else {
                foreach ($codes as $code) {
                    if (isset($code['value']) && trim($code['value']) !== '') {
                        $mainCode .= '        ' . $code['value'] . "\n";
                    }
                }
            }

            // Auto-cast assignment supaya tipe data konsisten
            $lines    = explode("\n", $mainCode);
            $varTypes = [];

            foreach ($lines as $line) {
                $trim = trim($line);
                if (preg_match('/^(int|float|double|long)\s+([a-zA-Z_][a-zA-Z0-9_]*)/', $trim, $m)) {
                    $varTypes[$m[2]] = $m[1];
                }
            }

            $fixed = [];
            foreach ($lines as $line) {
                $trim = trim($line);
                if (preg_match('/^([a-zA-Z_][a-zA-Z0-9_]*)\s*=\s*(.+);$/', $trim, $m)) {
                    $var  = $m[1];
                    $expr = $m[2];
                    if (isset($varTypes[$var])) {
                        $line = '        ' . $var . ' = (' . $varTypes[$var] . ')(' . $expr . ');';
                    }
                }
                $fixed[] = $line;
            }

            $mainCode = implode("\n", $fixed);

            // Deteksi import yang dibutuhkan secara otomatis
            $imports = [];

            $importMap = [
                'Scanner'           => 'java.util.Scanner',
                'ArrayList'         => 'java.util.ArrayList',
                'LinkedList'        => 'java.util.LinkedList',
                'HashMap'           => 'java.util.HashMap',
                'HashSet'           => 'java.util.HashSet',
                'Arrays'            => 'java.util.Arrays',
                'Collections'       => 'java.util.Collections',
                'List'              => 'java.util.List',
                'Map'               => 'java.util.Map',
                'Set'               => 'java.util.Set',
                'Stack'             => 'java.util.Stack',
                'Queue'             => 'java.util.Queue',
                'Iterator'          => 'java.util.Iterator',
                'Random'            => 'java.util.Random',
                'Math'              => null,
                'BufferedReader'    => 'java.io.BufferedReader',
                'InputStreamReader' => 'java.io.InputStreamReader',
                'IOException'       => 'java.io.IOException',
                'FileReader'        => 'java.io.FileReader',
                'FileWriter'        => 'java.io.FileWriter',
                'PrintWriter'       => 'java.io.PrintWriter',
            ];

            foreach ($importMap as $class => $importPath) {
                if ($importPath && preg_match('/\b' . preg_quote($class, '/') . '\b/', $mainCode)) {
                    $imports[] = 'import ' . $importPath . ';';
                }
            }

            $imports     = array_unique($imports);
            sort($imports);
            $importBlock = !empty($imports) ? implode("\n", $imports) . "\n\n" : '';

            // Rakit file Java final
            if ($isFullClassSource) {
                $javaCode = $importBlock . $mainCode;
            } else {
                $javaCode = $importBlock
                    . 'public class ' . $className . ' {' . "\n"
                    . '    public static void main(String[] args) {' . "\n"
                    . $mainCode
                    . '    }' . "\n"
                    . '}' . "\n";
            }

            // Tulis file .java
            $dirPath = storage_path('app/java/' . $soalId);
            if (!is_dir($dirPath)) {
                mkdir($dirPath, 0777, true);
            }

            $filePath = $dirPath . '/' . $className . '.java';
            file_put_contents($filePath, $javaCode);

            // Jalankan langsung source file Java agar tidak bergantung pada javac di server.
            // Java 11+ bisa mengeksekusi source file secara langsung.
            $javaHome = env('JAVA_HOME', '');
            $javaBin  = $javaHome
                ? rtrim($javaHome, '/\\') . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'java'
                : 'java';

            $process = new Process([$javaBin, basename($filePath)], $dirPath);
            $process->setTimeout(15);
            $process->setInput($soalInput); // input dari form ke Scanner
            $process->run();

            if (!$process->isSuccessful()) {
                throw new \RuntimeException(
                    'Eksekusi gagal:' . "\n" . $process->getErrorOutput()
                );
            }

            $output = $process->getOutput();

            return BaseResponse::json([
                'status' => true,
                'output' => trim($output),
                'path'   => $filePath,
            ]);
        } catch (\Exception $e) {
            return BaseResponse::errorMessage($e->getMessage(), 500);
        }
    }

    protected function renameJavaClassName(string $javaCode, string $className): string
    {
        $updatedCode = preg_replace_callback(
            '/\b((?:public\s+)?(?:abstract\s+|final\s+)?)class\s+[A-Za-z_][A-Za-z0-9_]*/i',
            function ($matches) use ($className) {
                return $matches[1] . 'class ' . $className;
            },
            $javaCode,
            1
        );

        return $updatedCode ?? $javaCode;
    }

    protected function extractMainBody(string $javaCode): array
    {
        $normalized = str_replace("\r\n", "\n", $javaCode);
        $mainPos = stripos($normalized, 'main(');
        if ($mainPos === false) {
            return [];
        }

        $openBracePos = strpos($normalized, '{', $mainPos);
        if ($openBracePos === false) {
            return [];
        }

        $depth = 0;
        $body = '';
        $length = strlen($normalized);
        for ($i = $openBracePos; $i < $length; $i++) {
            $ch = $normalized[$i];
            if ($ch === '{') {
                $depth++;
                if ($depth === 1) {
                    continue;
                }
            } elseif ($ch === '}') {
                $depth--;
                if ($depth === 0) {
                    break;
                }
            }

            if ($depth >= 1) {
                $body .= $ch;
            }
        }

        if (trim($body) === '') {
            return [];
        }

        return array_map(
            fn($line) => trim($line),
            explode("\n", $body)
        );
    }
}
