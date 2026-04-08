<?php

namespace App\Repositories;

use App\Models\BankSoalKonversi;
use App\Core\BaseResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

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
                'level.name as level',
                'soal.judul as soal',
                'bank_soal_konversi.jawaban',
                'bank_soal_konversi.output'
            )
            ->leftJoin('level', 'level.id', '=', 'bank_soal_konversi.id_level')
            ->leftJoin('soal', 'soal.id', '=', 'bank_soal_konversi.id_soal');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('jawaban', function ($item) {
                if (empty($item->jawaban)) return '-';
                $jawaban = is_array($item->jawaban)
                    ? $item->jawaban
                    : json_decode($item->jawaban, true);
                if (!$jawaban) return '-';
                return collect($jawaban)
                    ->map(function ($j) {
                        if (is_array($j)) {
                            return implode('', $j);
                        }
                        return (string) $j;
                    })
                    ->implode('<br>');
            })
            ->editColumn('output', function ($item) {
                return $item->output ?? '-';
            })
            ->rawColumns(['jawaban'])
            ->make(true);
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
            $codes = $request->input('codes', []);
            $soalId = $request->input('soal_id');

            $safeSoalId = str_replace('-', '_', $soalId);

            $mainCode = "";
            foreach ($codes as $code) {
                if (isset($code['value']) && trim($code['value']) !== '') {
                    $mainCode .= "        " . $code['value'] . "\n";
                }
            }

            $rawJoined = collect($codes)
                ->pluck('value')
                ->filter(fn($line) => $line !== null && trim($line) !== '')
                ->implode("\n");

            if (preg_match('/\bclass\b/i', $rawJoined) && preg_match('/\bmain\s*\(/i', $rawJoined)) {
                $mainBody = $this->extractMainBody($rawJoined);
                if (!empty($mainBody)) {
                    $mainCode = '';
                    foreach ($mainBody as $line) {
                        $trimmedLine = rtrim($line);
                        if ($trimmedLine === '') {
                            $mainCode .= "\n";
                            continue;
                        }
                        $mainCode .= "        " . $trimmedLine . "\n";
                    }
                }
            }

            $lines = explode("\n", $mainCode);
            $fixed = [];
            $varTypes = [];

            foreach ($lines as $line) {
                $trim = trim($line);
                if (preg_match('/^(int|float|double)\s+([a-zA-Z_][a-zA-Z0-9_]*)/', $trim, $m)) {
                    $varTypes[$m[2]] = $m[1];
                }
            }

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

            $javaCode = <<<EOD
            public class Main_$safeSoalId {
                public static void main(String[] args) {
                    $mainCode
                }
            }
            EOD;

            $dirPath = storage_path("app/java/$soalId");
            if (!is_dir($dirPath)) {
                mkdir($dirPath, 0777, true);
            }

            $filePath = $dirPath . "/Main_$safeSoalId.java";
            file_put_contents($filePath, $javaCode);

            $compile = new Process(['javac', $filePath], $dirPath);
            $compile->run();
            if (!$compile->isSuccessful()) {
                throw new ProcessFailedException($compile);
            }

            $process = new Process(['java', '-cp', $dirPath, "Main_$safeSoalId"], $dirPath);
            $process->run();
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $output = $process->getOutput();
            @unlink($dirPath . "/Main_$safeSoalId.class");

            return BaseResponse::json([
                'status' => true,
                'output' => $output,
                'path'   => $filePath
            ]);
        } catch (\Exception $e) {
            return BaseResponse::errorTransaction($e);
        }
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
