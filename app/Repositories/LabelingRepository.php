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
use App\Models\LabelSkor;
use App\Models\NilaiTest;

/**
 * Class LabelingRepository.
 *
 * @package namespace App\Repositories;
 */
class LabelingRepository extends BaseRepository
{

    protected $model;
    protected $levelModel;
    protected $mahasiswaModel;
    protected $soalModel;
    protected $ujianModel;
    protected $logDataModel;
    protected $historyConfidenceModel;
    protected $labelSkorModel;
    protected $nilaiTestModel;

    public function __construct()
    {
        $this->model = new HistoryConfidence();
        $this->levelModel = new Level();
        $this->mahasiswaModel = new Mahasiswa();
        $this->soalModel = new Soal();
        $this->ujianModel = new Ujian();
        $this->logDataModel = new LogData();
        $this->historyConfidenceModel = new HistoryConfidence();
        $this->labelSkorModel = new LabelSkor();
        $this->nilaiTestModel = new NilaiTest();
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

    public function table($request)
    {
        $query = $this->mahasiswaModel->setView('v_mahasiswa')->orderBy('name', 'asc');

        $kelas = $request->input('kelas');
        if (!is_null($kelas) && $kelas !== '') {
            $query = $query->where('id_kelas', $kelas);
        }

        if (!empty($request->input('search.value'))) {
            $searchValue = $request->input('search.value');
            $query = $query->where('name', 'like', '%' . $searchValue . '%');
        }

        $level = $request->input('level');
        $soal = $request->input('soal');

        // Hitung total records sebelum limit/filter
        $recordsTotal = $query->count();

        // Filter + paging DataTables
        $start  = $request->input('start', 0);
        $length = $request->input('length', 10);

        $data = $query->skip($start)->take($length)->get();

        // Tambahkan data tambahan per mahasiswa
        $data = $data->map(function ($row) use ($level, $soal, $kelas) {
            $id_mahasiswa = $row['id'];

            $ujianQuery = $this->ujianModel->setView('v_ujian')
                ->where('id_mahasiswa', $id_mahasiswa);
            if (!is_null($level) && $level !== '') {
                $ujianQuery = $ujianQuery->where('id_level', $level);
            }
            if (!is_null($soal) && $soal !== '') {
                $ujianQuery = $ujianQuery->where('id_soal', $soal);
            }
            $totalWaktuDetik = $ujianQuery->sum('waktu');

            $logDataQuery = $this->logDataModel->setView('v_log_data')
                ->where('id_mahasiswa', $id_mahasiswa);
            if (!is_null($level) && $level !== '') {
                $logDataQuery = $logDataQuery->where('id_level', $level);
            }
            if (!is_null($soal) && $soal !== '') {
                $logDataQuery = $logDataQuery->where('id_soal', $soal);
            }
            $totalDrag = $logDataQuery->count();

            $nilaiTest = null;
            if (!is_null($level) && $level !== '') {
                $nilaiTest = $this->nilaiTestModel
                    ->where('id_mahasiswa', $id_mahasiswa)
                    ->where('id_level', $level)
                    ->first();
            }

            $labelSkor = null;

            if (
                !is_null($level) && $level !== '' &&
                !is_null($soal) && $soal !== '' &&
                !is_null($kelas) && $kelas !== ''
            ) {
                $labelSkor = $this->labelSkorModel->setView('v_label_skor')
                    ->where('id_mahasiswa', $id_mahasiswa)
                    ->where('id_level', $level)
                    ->where('id_soal', $soal)
                    ->where('id_kelas', $row['id_kelas'])
                    ->first();
            } elseif (
                !is_null($level) && $level !== '' &&
                !is_null($kelas) && $kelas !== '' &&
                (is_null($soal) || $soal === '')
            ) {
                $labelSkor = $this->labelSkorModel->setView('v_label_skor')
                    ->where('id_mahasiswa', $id_mahasiswa)
                    ->where('id_level', $level)
                    ->where('id_kelas', $row['id_kelas'])
                    ->whereNull('id_soal')
                    ->whereNull('label')
                    ->first();
                // Jika ditemukan, masukkan skor ke row['skor']
                if ($labelSkor) {
                    $row['skor'] = $labelSkor->skor;
                }
            }

            $hours = floor($totalWaktuDetik / 3600);
            $minutes = floor(($totalWaktuDetik % 3600) / 60);
            $seconds = $totalWaktuDetik % 60;
            $totalWaktu = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

            $row['totalWaktu'] = $totalWaktu;
            $row['totalWaktuDetik'] = $totalWaktuDetik;
            $row['totalDrag'] = $totalDrag;
            $row['pre_test'] = $nilaiTest ? $nilaiTest->pre_test : null;
            $row['post_test'] = $nilaiTest ? $nilaiTest->post_test : null;
            $row['label'] = $labelSkor ? $labelSkor->label : null;
            $row['skor'] = $labelSkor ? $labelSkor->skor : null;

            return $row;
        });

        return [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $recordsTotal,
            "recordsFiltered" => $recordsTotal,
            "data"            => $data,
        ];
    }

    public function updateTest($data)
    {
        try {
            $updateData = [
                'id_level' => $data->level,
                'id_mahasiswa' => $data->id,
            ];

            if ($data->field === 'pre_test' || $data->field === 'post_test') {
                $updateData[$data->field] = $data->value;
            } else {
                return BaseResponse::errorTransaction();
            }

            $opr = $this->nilaiTestModel->updateOrCreate(
                [
                    'id_level' => $updateData['id_level'],
                    'id_mahasiswa' => $updateData['id_mahasiswa'],
                ],
                $updateData
            );

            return BaseResponse::updated($opr);
        } catch (\Exception $e) {
            return BaseResponse::errorTransaction();
        }
    }

    public function calculateManual($data)
    {
        // Validasi lebih awal agar tidak membuka transaksi jika invalid
        if (empty($data->level) || empty($data->soal)) {
            return BaseResponse::errorMessage('Level dan soal harus diisi.');
        }

        DB::beginTransaction();
        try {
            $level = $data->level;
            $soal = $data->soal;

            $dataMahasiswa = $this->mahasiswaModel->where('id_kelas', $data->kelas)->get();

            $insertData = [];
            foreach ($dataMahasiswa as $mahasiswa) {
                $id_mahasiswa = $mahasiswa->id;

                $totalWaktuDetik = $this->ujianModel->setView('v_ujian')
                    ->where('id_mahasiswa', $id_mahasiswa)
                    ->where('id_level', $level)
                    ->where('id_soal', $soal)
                    ->sum('waktu');

                $totalDrag = $this->logDataModel->setView('v_log_data')
                    ->where('id_mahasiswa', $id_mahasiswa)
                    ->where('id_level', $level)
                    ->where('id_soal', $soal)
                    ->count();

                // Lewati jika totalWaktuDetik dan totalDrag kosong atau 0 semua
                if ((int)$totalWaktuDetik === 0 && (int)$totalDrag === 0) {
                    continue;
                }

                // Penentuan label dan skor dipisah ke fungsi
                [$label, $skor] = $this->determineLabelAndScore($totalDrag, $totalWaktuDetik);

                $insertData[] = [
                    'id' => (string) Str::uuid(),
                    'id_level' => $level,
                    'id_soal' => $soal,
                    'id_mahasiswa' => $id_mahasiswa,
                    'label' => $label,
                    'skor' => $skor,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Hapus data lama untuk kombinasi level & soal & kelas ini
            $this->labelSkorModel
                ->where('id_level', $level)
                ->where('id_soal', $soal)
                ->whereIn('id_mahasiswa', $dataMahasiswa->pluck('id'))
                ->delete();

            // Insert batch
            $opr = true;
            if (!empty($insertData)) {
                $opr = $this->labelSkorModel->insert($insertData);
            }

            DB::commit();
            return BaseResponse::created($opr);
        } catch (\Throwable $e) {
            DB::rollBack();
            return BaseResponse::errorMessage($e->getMessage());
        }
    }

    /**
     * Tentukan label dan skor berdasarkan totalDrag dan totalWaktuDetik.
     *
     * @param int $totalDrag
     * @param int $totalWaktuDetik
     * @return array [label, skor]
     */
    // private function determineLabelAndScore($totalDrag, $totalWaktuDetik)
    // {
    //     if ($totalDrag <= 18 && $totalWaktuDetik < 53) {
    //         return ['Ideal', 90];
    //     } elseif ($totalDrag > 18 && $totalWaktuDetik >= 53) {
    //         return ['Struggling', 30];
    //     } elseif ($totalDrag <= 18 && $totalWaktuDetik >= 53) {
    //         return ['Normal', 70];
    //     } elseif ($totalDrag >= 18 && $totalWaktuDetik < 53) {
    //         return ['Gaming the System', 50];
    //     } else {
    //         return [null, null];
    //     }
    // }
        public function determineLabelAndScore($totalDrag, $totalWaktuDetik)
    {
        // Definisi label dan skor
        $ideal = ['Ideal', 90];
        $struggling = ['Struggling', 30];
        $normal = ['Normal', 70];
        $gamingTheSystem = ['Gaming the System', 50];

        if ($totalDrag <= 18 && $totalWaktuDetik < 53) {
            return $ideal;
        } elseif ($totalDrag > 18 && $totalWaktuDetik >= 53) {
            return $struggling;
        } elseif ($totalDrag <= 18 && $totalWaktuDetik >= 53) {
            return $normal;
        } elseif ($totalDrag >= 18 && $totalWaktuDetik < 53) {
            return $gamingTheSystem;
        } else {
            return [null, null];
        }
    }

    public function calculateAverage($data)
    {
        DB::beginTransaction();
        try {
            $dataMahasiswa = $this->mahasiswaModel->where('id_kelas', $data->kelas)->get();
            $level = $data->level;
            $dataSoal = $this->soalModel->where('id_level', $level)->get();
            $soalIds = $dataSoal->pluck('id')->toArray();
            $jumlahSoal = count($soalIds);

            $insertData = [];
            foreach ($dataMahasiswa as $mahasiswa) {
                $id_mahasiswa = $mahasiswa->id;

                $dataSkor = $this->labelSkorModel
                    ->where('id_mahasiswa', $id_mahasiswa)
                    ->where('id_level', $level)
                    ->whereIn('id_soal', $soalIds)
                    ->get();

                // Cek apakah id_soal pada dataSkor sudah lengkap
                $idSoalSkor = $dataSkor->pluck('id_soal')->unique()->toArray();
                sort($idSoalSkor);
                sort($soalIds);

                if ($idSoalSkor === $soalIds) {
                    $totalSkor = $dataSkor->sum('skor');
                    $countSkor = $dataSkor->count();
                    $averageSkor = $countSkor > 0 ? $totalSkor / $countSkor : 0;

                    // Hanya insert jika averageSkor > 0
                    if ($averageSkor > 0) {
                        $insertData[] = [
                            'id' => (string) Str::uuid(),
                            'id_level' => $level,
                            'id_soal' => null,
                            'id_mahasiswa' => $id_mahasiswa,
                            'label' => null,
                            'skor' => $averageSkor,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
                // Jika tidak lengkap, lewati mahasiswa ini
            }

            // Hapus data lama untuk kombinasi level & kelas ini
            $this->labelSkorModel
                ->where('id_level', $level)
                ->whereIn('id_mahasiswa', $dataMahasiswa->pluck('id'))
                ->whereNull('id_soal')
                ->delete();

            // Insert batch
            if (!empty($insertData)) {
                $opr = $this->labelSkorModel->insert($insertData);
            } else {
                $opr = true;
            }

            DB::commit();
            return BaseResponse::created($opr);
        } catch (\Exception $e) {
            DB::rollBack();
            return BaseResponse::errorTransaction();
        }
    }
}
