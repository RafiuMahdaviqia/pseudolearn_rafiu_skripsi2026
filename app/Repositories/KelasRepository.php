<?php

namespace App\Repositories;

use App\Models\Kelas;
use App\Core\BaseResponse;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Models\Mahasiswa;

/**
 * Class KelasRepository.
 * 
 * @package namespace App\Repositories;
 */
class KelasRepository extends BaseRepository
{

    protected $model;
    protected $mahasiswaModel;

    public function __construct()
    {
        $this->model = new Kelas();
        $this->mahasiswaModel = new Mahasiswa();
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


    public function table()
    {
        $opr = $this->model->draw();

        return $opr;
    }

    public function store($data)
    {
        try {
            DB::beginTransaction();
            $kelas = $this->model->create($data);
            if (!$kelas) {
                return BaseResponse::errorMessage('Gagal menyimpan data kelas', 500);
            }
            
            DB::commit();
            return BaseResponse::created($kelas);
        } catch (\Exception $e) {
            DB::rollBack();
            return BaseResponse::errorMessage($e->getMessage());
        }
    }

    public function getById($id)
    {
        $opr = $this->model->findOrFail($id);
        return $opr;
    }

    public function update($id, $data)
    {
        $opr = $this->model->findOrFail($id);
        $data = [
            'name' => $data->name,
            'angkatan' => $data->angkatan,
        ];
        
        $opr->update($data);
        return $opr;
    }

    public function destroy($id)
    {
        $opr = $this->model->findOrFail($id);

        if (!$opr) {
            return BaseResponse::errorMessage('Kelas tidak ditemukan', 404);
        }

        $mahasiswaCount = $this->mahasiswaModel->where('kelas_id', $id)->count();

        if ($mahasiswaCount > 0) {
            return BaseResponse::errorMessage('Kelas tidak dapat dihapus karena memiliki mahasiswa terkait', 400);
        }

        $opr->delete();

        return $opr;
    }

    public function getData()
    {
        $opr = $this->model->get(['id', 'name', 'angkatan']);
        return $opr;
    }
}
