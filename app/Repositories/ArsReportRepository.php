<?php

namespace App\Repositories;

use App\Core\BaseResponse;
use App\Models\HistoryConfidence;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\HistoryJawaban;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Models\Level;
use App\Models\Mahasiswa;
use App\Models\Soal;
use App\Models\Ujian;
use App\Models\LogData;

/**
 * Class ArsReportRepository.
 * 
 * @package namespace App\Repositories;
 */
class ArsReportRepository extends BaseRepository
{
    protected $levelModel;
    protected $mahasiswaModel;
    protected $soalModel;

    public function __construct()
    {
        $this->levelModel = new Level();
        $this->mahasiswaModel = new Mahasiswa();
        $this->soalModel = new Soal();
    }

    /**
     * Specify the model class name.
     *
     * @return string
     */
    public function model()
    {
        return Mahasiswa::class;
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

    public function table($request)
    {
        $query = $this->mahasiswaModel
            ->setView('v_mahasiswa')
            ->orderBy('name', 'asc');

        $kelas = $request->input('kelas');
        if (!empty($kelas)) {
            $query->where('id_kelas', $kelas);
        }

        $search = $request->input('search.value');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('nim', 'like', "%{$search}%")
                ->orWhere('kelas_name', 'like', "%{$search}%");
            });
        }

        $recordsTotal = $query->count();

        $start  = $request->input('start', 0);
        $length = $request->input('length', 10);

        $data = $query->skip($start)->take($length)->get();

        $data = $data->map(function ($row) {

            $row->totalArs = 0;
            $row->jumlahSoalTambahan = 0;
            $row->totalWaktu = "00:00:00";

            return $row;
        });

        return [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsTotal,
            "data" => $data,
        ];
    }

    public function tableArsLog($request){
        $idMahasiswa = $request->idMahasiswa;
        $idLevel = $request->idLevel;

        // PSEUDO
        $pseudo = DB::table('ujian')
            ->join('soal', 'soal.id', '=', 'ujian.id_soal')
            ->join('level', 'level.id', '=', 'soal.id_level')
            ->select(
                'level.id as level_id',
                'level.name as level',
                'soal.judul as soal',
                'soal.difficulty as difficulty',
                DB::raw("'pseudo' as jenis_soal"),
                'ujian.waktu as waktu',
                'ujian.created_at'
            )
            ->where('ujian.id_mahasiswa', $idMahasiswa);

        // KONVERSI
        $konversi = DB::table('ujian_konversi')
            ->join('konversi', 'konversi.id', '=', 'ujian_konversi.id_soal_konversi')
            ->join('soal', 'soal.id', '=', 'konversi.id_soal')
            ->join('level', 'level.id', '=', 'soal.id_level')
            ->select(
                'level.id as level_id',
                'level.name as level',
                'soal.judul as soal',
                'soal.difficulty as difficulty',
                DB::raw("'konversi' as jenis_soal"),
                'ujian_konversi.waktu as waktu',
                'ujian_konversi.created_at'
            )
            ->where('ujian_konversi.id_mahasiswa', $idMahasiswa);
        
        // UNION
        $query = $pseudo->unionAll($konversi);

        $query = DB::query()->fromSub($query, 'x');

        if (!empty($idLevel)) {
            $query->where('level_id', $idLevel);
        }

        return $query
            ->orderBy('created_at', 'desc')
            ->get();
    }
}