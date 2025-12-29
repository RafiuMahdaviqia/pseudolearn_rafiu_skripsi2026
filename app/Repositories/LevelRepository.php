<?php

namespace App\Repositories;

use App\Models\Level;
use App\Core\BaseResponse;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Models\Soal;
use App\Models\Konversi;

/**
 * Class LevelRepository.
 *
 * @package namespace App\Repositories;
 */
class LevelRepository extends BaseRepository
{

    protected $model;
    protected $soalModel;
    protected $konversiModel;

    public function __construct()
    {
        $this->model = new Level();
        $this->soalModel = new Soal();
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


    public function table()
    {
        $opr = $this->model->draw();

        return $opr;
    }

    public function store($data)
    {
        try {
            DB::beginTransaction();
            $level = $this->model->create($data);
            if (!$level) {
                return BaseResponse::errorMessage('Gagal menyimpan data level', 500);
            }
            
            DB::commit();
            return BaseResponse::created($level);
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
        if (!$opr) {
            return BaseResponse::errorMessage('Level tidak ditemukan', 404);
        }
        // dd($data);
        $opr->update($data);
        return $opr;
    }

    public function destroy($id)
    {
        $opr = $this->model->findOrFail($id);
        if (!$opr) {
            return BaseResponse::errorMessage('Level tidak ditemukan', 404);
        }

        // Hapus semua konversi dan soal terkait level ini tanpa loop
        $soalIds = $this->soalModel->where('id_level', $id)->pluck('id');

        if ($soalIds->isNotEmpty()) {
            // Hapus konversi terkait semua soal
            $this->konversiModel->whereIn('id_soal', $soalIds)->delete();
            // Hapus semua soal
            $this->soalModel->whereIn('id', $soalIds)->delete();
        }

        // Atur ulang urutan level lainnya
        $this->model->where('order', '>', $opr->order)->decrement('order');

        // Hapus gambar jika ada
        if ($opr->image) {
            $imagePath = public_path('assets/media/level_image/' . $opr->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        // Hapus data level
        $opr->delete();

        return $opr;
    }

    public function getData()
    {
        $opr = $this->model->orderBy('order', 'asc')->get();
        return $opr;
    }

    public function getLatestOrder()
    {
        return $this->model->max('order') ?? 0;
    }

    public function updateOrder($data)
    {
        try {
            DB::beginTransaction();
            if (!isset($data['order']) || !is_array($data['order'])) {
                return BaseResponse::errorMessage('Data order tidak valid', 400);
            }
            foreach ($data['order'] as $item) {
                $level = $this->model->findOrFail($item['id']);
                $opr = $level->update(['order' => $item['order']]);
            }
            DB::commit();
            return BaseResponse::updated($opr);
        } catch (\Exception $e) {
            DB::rollBack();
            return BaseResponse::errorMessage($e->getMessage());
        }
    }
}
