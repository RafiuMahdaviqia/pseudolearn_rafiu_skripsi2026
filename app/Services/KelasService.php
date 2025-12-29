<?php

namespace App\Services;

use App\Core\BaseResponse;
use Illuminate\Support\Facades\DB;
use App\Repositories\KelasRepository;
use App\Models\Kelas;

class KelasService
{
    protected $kelasRepository;
    protected $kelasModel;

    public function __construct()
    {
        $this->kelasRepository = new KelasRepository();
        $this->kelasModel = new Kelas();
    }

    public function table()
    {
        $opr = $this->kelasRepository->table();

        return $opr;
    }

    public function store($data)
    {
        $exists = $this->kelasModel
            ->whereRaw('LOWER(name) = ?', [strtolower($data->name)])
            ->where('angkatan', $data->angkatan)
            ->exists();

        if ($exists) {
            return BaseResponse::errorMessage('Kelas dengan nama dan angkatan tersebut sudah ada.', 409);
        }

        $insertData = [
            'name' => $data->name,
            'angkatan' => $data->angkatan,
        ];

        $opr = $this->kelasRepository->store($insertData);

        return $opr;
    }

    public function getById($id)
    {
        try {
            $opr = $this->kelasRepository->getById($id);
            if (!$opr) {
                return BaseResponse::errorMessage('Kelas tidak ditemukan', 404);
            }

            return BaseResponse::json($opr);
        } catch (\Exception $e) {
            return BaseResponse::errorMessage($e->getMessage());
        }
    }

    public function update($data)
    {
        try {
            DB::beginTransaction();
            $kelas = $this->kelasRepository->getById($data->id);
            if (!$kelas) {
                return BaseResponse::errorMessage('Kelas tidak ditemukan', 404);
            }

            $opr = $this->kelasRepository->update($data->id, $data);
            DB::commit();
            return BaseResponse::updated($opr);
        } catch (\Exception $e) {
            DB::rollBack();
            return BaseResponse::errorMessage($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $opr = $this->kelasRepository->destroy($id);
            DB::commit();
            return BaseResponse::deleted($opr);
        } catch (\Exception $e) {
            DB::rollBack();
            return BaseResponse::errorMessage($e->getMessage());
        }
    }

    public function getData()
    {
        try {
            $opr = $this->kelasRepository->getData();
            return BaseResponse::json($opr);
        } catch (\Exception $e) {
            return BaseResponse::errorMessage($e->getMessage());
        }
    }
}
