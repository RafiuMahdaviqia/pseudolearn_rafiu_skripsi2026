<?php

namespace App\Services;

use App\Core\BaseResponse;
use Illuminate\Support\Facades\DB;
use App\Repositories\BankSoalKonversiRepository;

class BankSoalKonversiService
{
    protected $bankSoalKonversiRepository;

    public function __construct()
    {
        $this->bankSoalKonversiRepository = new BankSoalKonversiRepository();
    }

    public function table($request)
    {
        return $this->bankSoalKonversiRepository->table($request);
    }

    public function getSoalByLevel($levelId)
    {
        return $this->bankSoalKonversiRepository->getSoalByLevel($levelId);
    }

    public function store($request)
    {
        $payload = [
            'id_level' => $request->input('level_id'),
            'id_soal'  => $request->input('soal_id'),
            'jawaban'  => trim($request->input('jawaban', '')), // ✅ plain text langsung
            'output'   => $request->input('output'),
        ];
        return $this->bankSoalKonversiRepository->store($payload);
    }

    public function update($request, $id)
    {
        $payload = [
            'id_level' => $request->input('level_id'),
            'id_soal'  => $request->input('soal_id'),
            'jawaban'  => trim($request->input('jawaban', '')), // ✅ plain text langsung
            'output'   => $request->input('output'),
        ];
        return $this->bankSoalKonversiRepository->update($payload, $id);
    }

    public function destroy($id)
    {
        return $this->bankSoalKonversiRepository->destroy($id);
    }

    public function detail($id)
    {
        return $this->bankSoalKonversiRepository->detail($id);
    }

    public function runKonversi($request)
    {
        return $this->bankSoalKonversiRepository->runJavaCode($request);
    }

    public function getOrderListByLevel(string $levelId)
    {
        return $this->bankSoalKonversiRepository->getOrderListByLevel($levelId);
    }

    public function saveOrder($request)
    {
        try {
            DB::beginTransaction();

            $orders = $request->input('order', []);
            if (!is_array($orders)) {
                return BaseResponse::errorMessage('Format order tidak valid');
            }

            $ok = $this->bankSoalKonversiRepository->saveOrder($orders);
            if (!$ok) {
                DB::rollBack();
                return BaseResponse::errorMessage('Gagal menyimpan urutan bank soal konversi');
            }

            DB::commit();
            return BaseResponse::updated(true);
        } catch (\Throwable $e) {
            DB::rollBack();
            return BaseResponse::errorMessage($e->getMessage());
        }
    }
}
