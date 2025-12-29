<?php

namespace App\Repositories;

use App\Models\Soal;
use App\Core\BaseResponse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\HistoryJawaban;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class KelasRepository.
 * 
 * @package namespace App\Repositories;
 */
class OverlappingRepository extends BaseRepository
{

    protected $model;
    protected $soalModel;

    public function __construct()
    {
        $this->model = new HistoryJawaban();
        $this->soalModel = new Soal();
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
    public function boot()
    {
        // Add your boot logic here
    }

    public function tableSoal($request)
    {
        $opr = $this->model->setView('v_soal')->orderBy('created_at', 'asc');

        $level = $request->input('level');
        if (!is_null($level) && $level !== '') {
            $opr = $opr->where('id_level', $level);
        }

        $opr = $opr->draw();

        return $opr;
    }
    
    public function getAnalysisData(Request $request)
    {
        $idSoal = $request->input('id_soal');
        $filterKelas = $request->input('filter_kelas'); // bisa null atau id kelas
        $soal = $this->soalModel->where('id', $idSoal)->first();

        if (!$idSoal) {
            return BaseResponse::errorMessage('id_soal is required');
        }

        $decodeJson = function ($raw) {
            if (is_array($raw)) return $raw;

            $clean = trim($raw);

            // jika string diawali & diakhiri tanda kutip → hapus
            if ((Str::startsWith($clean, '"') && Str::endsWith($clean, '"')) ||
                (Str::startsWith($clean, "'") && Str::endsWith($clean, "'"))) {
                $clean = substr($clean, 1, -1);
            }

            $decoded = json_decode($clean, true);

            // kalau masih gagal, coba sekali lagi (data bisa escaped)
            if (json_last_error() !== JSON_ERROR_NONE) {
                $decoded = json_decode(stripslashes($clean), true);
            }

            return is_array($decoded) ? $decoded : [];
        };


        $kunciTipe = collect($decodeJson($soal->kunci_tipe_data))
            ->filter(fn($r) => ($r['variabel'] ?? null) !== null)
            ->values()
            ->toArray();

        $kunciAlgo = collect($decodeJson($soal->kunci_algoritma))
            ->values()
            ->toArray();

        // --- Query tipe_data ---
        $rowsTipe = DB::table('history_jawaban as h')
            ->selectRaw('h.index_tipe_data,
                        COALESCE(NULLIF(h.tipe_data, \'\'), \'unknown\') as tipe_data,
                        COUNT(*) as total_count,
                        SUM(CASE WHEN h.status = "benar" THEN 1 ELSE 0 END) as benar_count,
                        SUM(CASE WHEN h.status = "salah" THEN 1 ELSE 0 END) as salah_count')
            ->leftJoin('mahasiswa as m', 'h.id_mahasiswa', '=', 'm.id')
            ->leftJoin('kelas as k', 'm.id_kelas', '=', 'k.id')
            ->where('h.id_soal', $idSoal)
            ->whereNotNull('h.index_tipe_data')
            ->whereNotNull('h.tipe_data')
            ->when($filterKelas, function($q, $filterKelas) {
                $q->where('k.id', $filterKelas);
            })
            ->groupBy('h.index_tipe_data', 'h.tipe_data')
            ->orderBy('h.index_tipe_data')
            ->get();

        $resultTipe = [];
        foreach ($rowsTipe as $r) {
            $idx = (int)$r->index_tipe_data;
            if (!isset($resultTipe[$idx])) {
                $resultTipe[$idx] = [
                    'index' => $idx,
                    'labels' => []
                ];
            }
            $resultTipe[$idx]['labels'][] = [
                'tipe_data' => $r->tipe_data,
                'total' => (int)$r->total_count,
                'benar' => (int)$r->benar_count,
                'salah' => (int)$r->salah_count,
                'pct_benar' => $r->total_count ? round(((int)$r->benar_count / (int)$r->total_count) * 100, 1) : 0
            ];
        }

        // --- Query algoritma ---
        $rowsAlgoritma = DB::table('history_jawaban as h')
            ->selectRaw('h.index_algoritma,
                        COALESCE(NULLIF(h.algoritma, \'\'), \'unknown\') as algoritma,
                        COUNT(*) as total_count,
                        SUM(CASE WHEN h.status = "benar" THEN 1 ELSE 0 END) as benar_count,
                        SUM(CASE WHEN h.status = "salah" THEN 1 ELSE 0 END) as salah_count')
            ->leftJoin('mahasiswa as m', 'h.id_mahasiswa', '=', 'm.id')
            ->leftJoin('kelas as k', 'm.id_kelas', '=', 'k.id')
            ->where('h.id_soal', $idSoal)
            ->whereNotNull('h.index_algoritma')
            ->whereNotNull('h.algoritma')
            ->when($filterKelas, function($q, $filterKelas) {
                $q->where('k.id', $filterKelas);
            })
            ->groupBy('h.index_algoritma', 'h.algoritma')
            ->orderBy('h.index_algoritma')
            ->get();

        $resultAlgoritma = [];
        foreach ($rowsAlgoritma as $r) {
            $idx = (int)$r->index_algoritma;
            if (!isset($resultAlgoritma[$idx])) {
                $resultAlgoritma[$idx] = [
                    'index' => $idx,
                    'labels' => []
                ];
            }
            $resultAlgoritma[$idx]['labels'][] = [
                'algoritma' => $r->algoritma,
                'total' => (int)$r->total_count,
                'benar' => (int)$r->benar_count,
                'salah' => (int)$r->salah_count,
                'pct_benar' => $r->total_count ? round(((int)$r->benar_count / (int)$r->total_count) * 100, 1) : 0
            ];
        }

        return [
            'id_soal' => $idSoal,
            'filter_kelas' => $filterKelas ?: null,
            'data' => [
                'tipe_data' => array_values($resultTipe),
                'algoritma' => array_values($resultAlgoritma)
            ],
            'kunciTipe' => $kunciTipe,
            'kunciAlgo' => $kunciAlgo
        ];
    }

    public function tableDetail($request)
    {
        $index = $request->type == 'tipe_data' ? 'index_tipe_data' : 'index_algoritma';
        $value = $request->type == 'tipe_data' ? 'tipe_data' : 'algoritma';

        $query = $this->model->setView('v_history_jawaban')
            ->where('id_soal', $request->id_soal)
            ->where($index, $request->index)
            ->where($value, $request->value)
            ->orderBy('created_at', 'desc');

        if (!empty($request->kelas)) {
            $query->where('id_kelas', $request->kelas);
        }

        if (!empty($request->input('search.value'))) {
            $searchValue = $request->input('search.value');
            $query->where('name', 'like', '%' . $searchValue . '%');
        }

        // Hitung total records sebelum limit/filter
        $recordsTotal = $query->count();

        // Filter + paging DataTables
        $start  = $request->input('start', 0);
        $length = $request->input('length', 10);

        $data = $query->skip($start)->take($length)->get();

        return [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $recordsTotal,
            "recordsFiltered" => $recordsTotal, // kalau ada filter tambahan, bisa dihitung ulang
            "data"            => $data,
        ];
    }

}
