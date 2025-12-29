<?php

namespace App\Services;

use App\Models\Soal;
use App\Core\BaseResponse;
use Illuminate\Support\Facades\DB;
use App\Repositories\SoalRepository;
use App\Jobs\RecalculateLevelAverageJob;

class SoalService
{
    protected $soalRepository;
    protected $soalModel;

    public function __construct()
    {
        $this->soalRepository = new SoalRepository();
        $this->soalModel = new Soal();
    }


    public function table($request)
    {
        $opr = $this->soalRepository->table($request);

        return $opr;
    }

    public function store($data)
    {

        if (!empty($data->id)) {
            $opr = $this->soalRepository->update($data->id, $data);
        } else {
            $opr = $this->soalRepository->store($data);
        }

        return $opr;
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $opr = $this->soalRepository->destroy($id);
            DB::commit();
            return BaseResponse::deleted($opr);
        } catch (\Exception $e) {
            DB::rollBack();
            return BaseResponse::errorMessage($e->getMessage());
        }
    }

    public function getById($id)
    {
        try {
            $opr = $this->soalRepository->getById($id);
            if (!$opr) {
                return BaseResponse::errorMessage('Soal tidak ditemukan', 404);
            }

            return BaseResponse::json($opr);
        } catch (\Exception $e) {
            return BaseResponse::errorMessage($e->getMessage());
        }
    }

    public function saveOrder($data)
    {
        try {
            DB::beginTransaction();

            $orders = $data->input('order', []);

            if (!is_array($orders)) {
                throw new \InvalidArgumentException('Format order tidak valid');
            }

            foreach ($orders as $item) {
                if (!isset($item['id'], $item['order'])) {
                    continue;
                }
                $this->soalModel
                    ->where('id', $item['id'])
                    ->update(['order' => (int)$item['order']]);
            }
            DB::commit();
            return BaseResponse::updated('Urutan soal berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return BaseResponse::errorMessage($e->getMessage());
        }
    }

    public function updateStatusSoal($data)
        {
        try {
            DB::beginTransaction();

            $id = $data->input('id');
            $status = (int)$data->input('status');

            if (!in_array($status, [0, 1], true)) {
                throw new \InvalidArgumentException('Status tidak valid');
            }

            $soal = $this->soalModel->find($id);
            if (!$soal) {
                return BaseResponse::errorMessage('Soal tidak ditemukan', 404);
            }

            $soal->status = $status;
            $soal->save();
            $levelId = $soal->id_level;

            DB::commit();

            // Dispatch job recalculation (asynchronous)
            RecalculateLevelAverageJob::dispatch($levelId);

            return BaseResponse::updated('Status soal berhasil diperbarui & perhitungan ulang dijalankan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return BaseResponse::errorMessage($e->getMessage());
        }
    }
}
