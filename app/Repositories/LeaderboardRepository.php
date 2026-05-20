<?php

namespace App\Repositories;

use App\Models\Soal;
use App\Models\Level;
use App\Models\Ujian;
use App\Entities\User;
use App\Models\Konversi;
use App\Models\LabelSkor;
use App\Models\Mahasiswa;
use App\Core\BaseResponse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\UjianKonversi;
use App\Models\HistoryJawaban;
use App\Models\HistoryConfidence;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class KelasRepository.
 * 
 * @package namespace App\Repositories;
 */
class LeaderboardRepository extends BaseRepository
{

    protected $model;
    protected $levelModel;
    protected $mahasiswaModel;
    protected $soalModel;
    protected $labelSkorModel;
    protected $ujianKonversiModel;
    protected $ujianModel;
    protected $konversiModel;

    public function __construct()
    {
        $this->model = new HistoryConfidence();
        $this->levelModel = new Level();
        $this->mahasiswaModel = new Mahasiswa();
        $this->soalModel = new Soal();
        $this->labelSkorModel = new LabelSkor();
        $this->ujianKonversiModel = new UjianKonversi();
        $this->ujianModel = new Ujian();
        $this->konversiModel = new Konversi();
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

    public function table(Request $request)
    {
        $searchValue = trim((string) $request->input('search.value', ''));

        // Total data sebelum filter
        $recordsTotal = $this->mahasiswaModel
            ->setView('v_mahasiswa')
            ->count();

        // Data terfilter + sudah diberi rank (berdasarkan aturan default)
        $leaderboard = $this->getLeaderboard($searchValue !== '' ? $searchValue : null);
        $recordsFiltered = count($leaderboard);

        // Order (DataTables) - dukung multi kolom dan hormati 'orderable'
        $columns = $request->input('columns', []);
        $orders  = $request->input('order', []);
        $validOrders = [];

        if (is_array($orders) && is_array($columns)) {
            foreach ($orders as $o) {
                $colIdx = (int) ($o['column'] ?? -1);
                if ($colIdx < 0 || !isset($columns[$colIdx])) {
                    continue;
                }
                $col = $columns[$colIdx];

                $orderable = isset($col['orderable']) && (
                    $col['orderable'] === true ||
                    $col['orderable'] === 'true' ||
                    $col['orderable'] === 1 ||
                    $col['orderable'] === '1'
                );
                if (!$orderable) {
                    continue;
                }

                $key = $col['data'] ?? null;
                if (!$key) {
                    continue;
                }

                $dir = strtolower((string) ($o['dir'] ?? 'asc'));
                $dir = in_array($dir, ['asc', 'desc'], true) ? $dir : 'asc';

                $validOrders[] = ['key' => $key, 'dir' => $dir];
            }
        }

        if (!empty($validOrders)) {
            usort($leaderboard, function ($a, $b) use ($validOrders) {
                foreach ($validOrders as $ord) {
                    $key = $ord['key'];
                    $va = $a[$key] ?? null;
                    $vb = $b[$key] ?? null;

                    // Numeric vs string compare
                    if (is_numeric($va) && is_numeric($vb)) {
                        $cmp = $va <=> $vb;
                    } else {
                        $cmp = strcasecmp((string) $va, (string) $vb);
                    }

                    if ($cmp !== 0) {
                        return $ord['dir'] === 'desc' ? -$cmp : $cmp;
                    }
                }
                return 0;
            });
        }

        // Paging DataTables
        $start = max(0, (int) $request->input('start', 0));
        $lengthInput = (int) $request->input('length', 10);
        $length = $lengthInput < 0 ? $recordsFiltered : $lengthInput; // -1 => semua

        $data = array_slice($leaderboard, $start, $length);

        return [
            "draw"            => (int) $request->input('draw', 0),
            "recordsTotal"    => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data"            => array_values($data),
        ];
    }

    public function getLeaderboard(?string $name = null)
    {
        $builder = $this->mahasiswaModel
            ->setView('v_mahasiswa')
            ->orderBy('name', 'asc');

        if ($name !== null) {
            $builder->where('name', 'like', '%' . $name . '%');
        }

        $query = $builder->get()->toArray();

        // Ambil semua level, urutkan dari tertinggi
        $levels = Level::orderBy('order', 'desc')->get();

        $dataAlgopoin = [];
        foreach ($query as $mahasiswa) {
            $mahasiswaId = $mahasiswa['id'];
            $mahasiswaName = $mahasiswa['name'];
            $idUser = $mahasiswa['id_user'];
            $userAvatar = User::find($idUser)->avatar ?? null;

            $found = false;
            foreach ($levels as $level) {
                $levelId = $level->id;

                // HANYA hitung soal & konversi AKTIF (status=1)
                $jumlahSoal = Soal::where('id_level', $levelId)
                    ->where('status', 1)
                    ->count();

                $jumlahKonversi = $this->konversiModel
                    ->setView('v_bank_soal_konversi')
                    ->where('id_level', $levelId)
                    ->where('status', 1)
                    ->count();

                // Hitung jumlah soal aktif yg sudah dikerjakan (distinct id_soal) dengan join/filter aktif
                $soalSelesai = Ujian::where('id_mahasiswa', $mahasiswaId)
                    ->where('id_level', $levelId)
                    ->whereIn('id_soal', function($q) use ($levelId) {
                        $q->select('id')
                          ->from('soal')
                          ->where('id_level', $levelId)
                          ->where('status', 1);
                    })
                    ->distinct('id_soal')
                    ->count('id_soal');

                // Hitung jumlah konversi aktif yg sudah dikerjakan
                $konversiSelesai = UjianKonversi::where('id_mahasiswa', $mahasiswaId)
                    ->where('id_level', $levelId)
                    ->whereIn('id_soal_konversi', function ($q) use ($levelId) {
                        $q->select('k.id')
                          ->from('konversi as k')
                          ->join('soal as s', 's.id', '=', 'k.id_soal')
                          ->where('k.id_level', $levelId)
                          ->where('s.status', 1);    // hanya konversi yg soal-nya aktif
                    })
                    ->distinct('id_soal_konversi')
                    ->count('id_soal_konversi');

                if (
                    $jumlahSoal > 0 && $jumlahKonversi > 0 &&
                    $soalSelesai == $jumlahSoal &&
                    $konversiSelesai == $jumlahKonversi
                ) {
                    $algopoin = (float) $this->labelSkorModel->where('id_mahasiswa', $mahasiswaId)
                        ->whereNull('id_soal')
                        ->whereNull('label')
                        ->where('id_level', $levelId)
                        ->sum('skor');

                    $avgNilaiUjianKonversi = (float) $this->ujianKonversiModel
                        ->where('id_mahasiswa', $mahasiswaId)
                        ->where('id_level', $levelId)
                        ->avg('nilai');

                    $totalSkor = round($algopoin + $avgNilaiUjianKonversi);
                    $waktuUjian = (int) $this->ujianModel
                        ->where('id_mahasiswa', $mahasiswaId)
                        ->where('id_level', $levelId)
                        ->sum('waktu');
                    $waktuUjianKonversi = (int) $this->ujianKonversiModel
                        ->where('id_mahasiswa', $mahasiswaId)
                        ->where('id_level', $levelId)
                        ->sum('waktu');
                    $totalWaktu = $waktuUjian + $waktuUjianKonversi;

                    $dataAlgopoin[] = [
                        'mahasiswa_id' => $mahasiswaId,
                        'algopoin' => $algopoin,
                        'avg_nilai_ujian_konversi' => $avgNilaiUjianKonversi,
                        'total_skor' => $totalSkor,
                        'mahasiswa_name' => $mahasiswaName,
                        'total_waktu' => $totalWaktu,
                        'id_user' => $idUser,
                        'user_avatar' => $userAvatar,
                        'level' => $level->order,
                    ];
                    $found = true;
                    break; // Ambil hanya level tertinggi yang sudah selesai
                }
            }
    
            // Jika tidak ada level yang memenuhi, tetap tampilkan dengan skor 0
            if (!$found) {
                $dataAlgopoin[] = [
                    'mahasiswa_id' => $mahasiswaId,
                    'algopoin' => 0,
                    'avg_nilai_ujian_konversi' => 0,
                    'total_skor' => 0,
                    'mahasiswa_name' => $mahasiswaName,
                    'total_waktu' => 0,
                    'id_user' => $idUser,
                    'user_avatar' => $userAvatar,
                    'level' => null,
                ];
            }
        }

        // Urutan default untuk penentuan rank
        usort($dataAlgopoin, function ($a, $b) {
            $cmp = $b['total_skor'] <=> $a['total_skor'];
            if ($cmp === 0) {
                return $a['total_waktu'] <=> $b['total_waktu'];
            }
            return $cmp;
        });

        foreach ($dataAlgopoin as $i => &$row) {
            $row['rank'] = $i + 1;
        }
        unset($row);

        return $dataAlgopoin;
    }

    public function getRank($rank)
    {
        $leaderboard = $this->getLeaderboard();
        foreach ($leaderboard as $entry) {
            if ($entry['rank'] === $rank) {
                return $entry;
            }
        }
        return null; // Jika tidak ditemukan
    }

    public function getRankByIdMahasiswa($id_mahasiswa)
    {
        $leaderboard = $this->getLeaderboard();
        foreach ($leaderboard as $entry) {
            if ($entry['mahasiswa_id'] === $id_mahasiswa) {
                return $entry;
            }
        }
        return null; // Jika tidak ditemukan
    }
}
