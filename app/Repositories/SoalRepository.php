<?php

namespace App\Repositories;

use App\Models\Soal;
use App\Models\Kelas;
use App\Models\Konversi;
use App\Models\Mahasiswa;
use App\Core\BaseResponse;
use App\Jobs\DeletePencapaianSoal;
use Illuminate\Support\Facades\DB;
use App\Jobs\GeneratePencapaianSoal;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class KelasRepository.
 * 
 * @package namespace App\Repositories;
 */
class SoalRepository extends BaseRepository
{

    protected $model;
    protected $konversiModel;
    protected $mahasiswaModel;
    protected $kelasModel;

    public function __construct()
    {
        $this->model = new Soal();
        $this->konversiModel = new Konversi();
        $this->mahasiswaModel = new Mahasiswa();
        $this->kelasModel = new Kelas();
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
        $opr = $this->model->setView('v_soal');

        $level = $request->input('level');
        if (!is_null($level) && $level !== '') {
            $opr = $opr->where('id_level', $level);
        }

        $opr = $opr->draw();

        return $opr;
    }

    public function store($data)
    {
        try {
            DB::beginTransaction();
    
            // Handle kunci_tipe_data (support old form fields OR direct JSON)
            $dataInputArr = [];
            if (isset($data['tipe_data'])) {
                foreach ($data['tipe_data'] as $i => $tipe) {
                    $dataInputArr[] = [
                        'variabel' => $data['variabel'][$i] ?? null,
                        'tipe_data' => $tipe,
                        'konversi' => $data['konversi_tipe_data'][$i] ?? 0,
                    ];
                }
            } elseif (!empty($data['kunci_tipe_data'])) {
                $decoded = is_string($data['kunci_tipe_data']) ? json_decode($data['kunci_tipe_data'], true) : $data['kunci_tipe_data'];
                if (is_array($decoded)) {
                    foreach ($decoded as $row) {
                        $dataInputArr[] = [
                            'variabel' => $row['variabel'] ?? null,
                            'tipe_data' => $row['tipe_data'] ?? null,
                            'konversi' => $row['konversi'] ?? 0,
                        ];
                    }
                }
            } else {
                $dataInputArr = [];
            }

            // Handle kunci_algoritma
            $algoritmaArr = [];
            if (isset($data['langkah'])) {
                foreach ($data['langkah'] as $i => $langkah) {
                    $algoritmaArr[] = [
                        'langkah' => $langkah,
                        'clue' => $data['clue'][$i] ?? null,
                        'konversi' => $data['konversi_tipe_data'][$i] ?? 0,
                    ];
                }
            } elseif (!empty($data['kunci_algoritma'])) {
                $decodedAlg = is_string($data['kunci_algoritma']) ? json_decode($data['kunci_algoritma'], true) : $data['kunci_algoritma'];
                if (is_array($decodedAlg)) {
                    foreach ($decodedAlg as $row) {
                        $algoritmaArr[] = [
                            'langkah' => $row['langkah'] ?? null,
                            'clue' => $row['clue'] ?? null,
                            'konversi' => $row['konversi'] ?? 0,
                        ];
                    }
                }
            } else {
                $algoritmaArr = [];
            }

            $getLatestOrder = $this->model->where('id_level', $data['level_id'])->max('order');
            $nextOrder = is_null($getLatestOrder) ? 1 : $getLatestOrder + 1;

            // Mapping data request ke field tabel
            $insertData = [
                'id_level'        => $data['level_id'],
                'judul'           => $data['judul'],
                'soal'            => $data['soal'],
                'difficulty'      => $data['difficulty'],
                'kunci_tipe_data' => json_encode($dataInputArr),
                'kunci_algoritma' => json_encode($algoritmaArr),
                'order'           => $nextOrder,
                'status'          => 1, // Set status aktif secara default
            ];
    
            $soal = $this->model->create($insertData);
    
            if (!$soal) {
                DB::rollBack();
                return BaseResponse::errorMessage('Gagal menyimpan data soal', 500);
            }
    
            DB::commit();

            // Dispatch job per kelas
            $kelasList = Kelas::all();
            foreach ($kelasList as $kelas) {
                GeneratePencapaianSoal::dispatch($soal, $kelas->id);
            }

            return BaseResponse::created($soal);
        } catch (\Exception $e) {
            DB::rollBack();
            return BaseResponse::errorMessage($e->getMessage());
        }
    }

    public function update($id, $data)
    {
        try {
            DB::beginTransaction();

            $soal = $this->model->findOrFail($id);

            // Handle kunci_tipe_data (support old form fields OR direct JSON)
            $dataInputArr = [];
            if (isset($data['tipe_data'])) {
                foreach ($data['tipe_data'] as $i => $tipe) {
                    $dataInputArr[] = [
                        'variabel' => $data['variabel'][$i] ?? null,
                        'tipe_data' => $tipe,
                        'konversi' => $data['konversi_tipe_data'][$i] ?? 0,
                    ];
                }
            } elseif (!empty($data['kunci_tipe_data'])) {
                $decoded = is_string($data['kunci_tipe_data']) ? json_decode($data['kunci_tipe_data'], true) : $data['kunci_tipe_data'];
                if (is_array($decoded)) {
                    foreach ($decoded as $row) {
                        $dataInputArr[] = [
                            'variabel' => $row['variabel'] ?? null,
                            'tipe_data' => $row['tipe_data'] ?? null,
                            'konversi' => $row['konversi'] ?? 0,
                        ];
                    }
                }
            } else {
                $dataInputArr = json_decode($soal->kunci_tipe_data, true) ?? [];
            }

            // Handle kunci_algoritma
            $algoritmaArr = [];
            if (isset($data['langkah'])) {
                foreach ($data['langkah'] as $i => $langkah) {
                    $algoritmaArr[] = [
                        'langkah' => $langkah,
                        'clue' => $data['clue'][$i] ?? null,
                        'konversi' => $data['konversi_tipe_data'][$i] ?? 0,
                    ];
                }
            } elseif (!empty($data['kunci_algoritma'])) {
                $decodedAlg = is_string($data['kunci_algoritma']) ? json_decode($data['kunci_algoritma'], true) : $data['kunci_algoritma'];
                if (is_array($decodedAlg)) {
                    foreach ($decodedAlg as $row) {
                        $algoritmaArr[] = [
                            'langkah' => $row['langkah'] ?? null,
                            'clue' => $row['clue'] ?? null,
                            'konversi' => $row['konversi'] ?? 0,
                        ];
                    }
                }
            } else {
                $algoritmaArr = json_decode($soal->kunci_algoritma, true) ?? [];
            }

            $updateData = [
                'id_level'        => $data['level_id'] ?? $soal->id_level,
                'judul'           => $data['judul'] ?? $soal->judul,
                'soal'            => $data['soal'] ?? $soal->soal,
                'difficulty'      => $data['difficulty'] ?? $soal->difficulty,
                'kunci_tipe_data' => json_encode($dataInputArr),
                'kunci_algoritma' => json_encode($algoritmaArr),
            ];

            $soal->update($updateData);

            DB::commit();
            return BaseResponse::updated($soal);
        } catch (\Exception $e) {
            DB::rollBack();
            return BaseResponse::errorMessage($e->getMessage());
        }
    }

    public function destroy($id)
    {
        $opr = $this->model->findOrFail($id);
        $opr->delete();

        // Hapus semua konversi yang terkait dengan soal ini
        $this->konversiModel->where('id_soal', $id)->delete();

        // Dispatch job per kelas
        $kelasList = Kelas::all();
        foreach ($kelasList as $kelas) {
            DeletePencapaianSoal::dispatch($opr, $kelas->id);
        }

        return $opr;
    }

    public function getById($id)
    {
        $opr = $this->model->findOrFail($id);
        return $opr;
    }


    // public function getData()
    // {
    //     $opr = $this->model->get(['id', 'name', 'angkatan']);
    //     return $opr;
    // }
}
