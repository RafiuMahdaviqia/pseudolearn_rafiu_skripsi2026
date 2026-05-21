<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Models\Level;
use App\Models\Mahasiswa;
use App\Models\Soal;

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
        $query = DB::table('mahasiswa as m')
            ->leftJoin('kelas as k', 'k.id', '=', 'm.id_kelas')
            ->leftJoin('ars_result as ar', 'ar.id_mahasiswa', '=', 'm.id')
            ->when($request->kelas, fn($q) => $q->where('m.id_kelas', $request->kelas))
            ->when($request->search['value'] ?? false, function ($q) use ($request) {
                $search = $request->search['value'];
                $q->where(function ($query) use ($search) {
                    $query->where('m.name', 'like', "%{$search}%")
                        ->orWhere('m.nim', 'like', "%{$search}%")
                        ->orWhere('k.name', 'like', "%{$search}%");
                    });
                })
            ->select(
                'm.id',
                'm.nim',
                'm.name',
                'k.name as kelas',

                DB::raw("COALESCE(SUM(CASE WHEN ar.pseudo_label IN ('Struggling','Gaming the System') THEN 1 ELSE 0 END),0) as total_ars"),
                DB::raw("COALESCE(COUNT(DISTINCT ar.id_soal),0) as total_soal"),
                DB::raw("COALESCE(SUM(COALESCE(ar.pseudo_durasi,0) + COALESCE(ar.konversi_durasi,0)),0) as total_waktu")
            )
            ->groupBy('m.id','m.nim','m.name','k.name');

        return [
            "draw" => intval($request->draw),
            "recordsTotal" => $query->count(),
            "recordsFiltered" => $query->count(),
            "data" => $query->get()
        ];
    }

    public function tableArsLog($request)
    {
        $query = DB::table('ars_result as ar')
            ->join('soal as s', 's.id', '=', 'ar.id_soal')
            ->join('level as l', 'l.id', '=', 'ar.id_level')
            ->where('ar.id_mahasiswa', $request->idMahasiswa)
            ->when($request->idLevel, fn($q) =>
                $q->where('ar.id_level', $request->idLevel)
            )
            ->select(
                'l.name as level',
                's.judul as soal',
                'ar.difficulty',
                'ar.ars_batch',
                'ar.pseudo_label',
                'ar.konversi_label',
                'ar.pseudo_durasi',
                'ar.konversi_durasi',
                DB::raw('COALESCE(ar.pseudo_durasi,0) + COALESCE(ar.konversi_durasi,0) as total_durasi'),
                'ar.created_at'
            )
            ->orderBy('ar.created_at', 'desc');

        return [
            'draw'            => intval($request->draw),
            'recordsTotal'    => $query->count(),
            'recordsFiltered' => $query->count(),
            'data'            => $query->get()
        ];
    }

    public function getDetailArs($idMahasiswa, $idLevel = null)
    {
        $query = DB::table('ars_result as ar')
            ->join('soal as s', 's.id', '=', 'ar.id_soal')
            ->join('level as l', 'l.id', '=', 'ar.id_level')
            ->where('ar.id_mahasiswa', $idMahasiswa);

        if ($idLevel) {
            $query->where('ar.id_level', $idLevel);
        }

        $data = $query->select(
            'l.name as level',
            's.judul as soal',
            'ar.difficulty',
            'ar.pseudo_label',
            'ar.pseudo_durasi',
            'ar.konversi_label',
            'ar.konversi_durasi',
            'ar.created_at'
        )->orderBy('ar.created_at', 'desc')->get();

        //Summary ARS
        $totalArs = DB::table('ars_result')
            ->where('id_mahasiswa', $idMahasiswa)
            ->when($idLevel, fn($q) => $q->where('id_level', $idLevel))
            ->where(function ($q) {
                $q->where('pseudo_label', 'Struggling')
                ->orWhere('pseudo_label', 'Gaming the System');
            })
            ->count();

        $totalWaktu = DB::table('ars_result')
            ->where('id_mahasiswa', $idMahasiswa)
            ->when($idLevel, fn($q) => $q->where('id_level', $idLevel))
            ->select(DB::raw('COALESCE(SUM(pseudo_durasi),0) + COALESCE(SUM(konversi_durasi),0) as total'))
            ->value('total');

        $jumlahSoalTambahan = DB::table('ars_result')
            ->where('id_mahasiswa', $idMahasiswa)
            ->when($idLevel, fn($q) => $q->where('id_level', $idLevel))
            ->count();

        return [
            'summary' => [
                'totalArs' => $totalArs,
                'totalWaktu' => gmdate("H:i:s", $totalWaktu ?? 0),
                'jumlahSoalTambahan' => $jumlahSoalTambahan,
            ],
            'data' => $data
        ];
    }
}
