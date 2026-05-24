<?php

namespace App\Services;

use App\Core\BaseResponse;
use App\Repositories\BankSoalKonversiRepository;
use Illuminate\Support\Facades\DB;
use App\Repositories\KonversiRepository;

class KonversiService
{
    protected $konversiRepository;
    public function __construct()
    {
        $this->konversiRepository = new BankSoalKonversiRepository();
    }

    public function table($request)
    {
        $data = $this->konversiRepository->table($request);
        return $data;
    }

    public function store($request)
    {
        $data = $this->konversiRepository->store($request);
        return $data;
    }

    public function getSoalByLevel($request){
        $opr = $this->konversiRepository->getSoalByLevel($request);
        return $opr;
    }

    public function destroy($id)
    {
        $data = $this->konversiRepository->destroy($id);
        return $data;
    }

    public function runKonversi($request)
    {
        $data = $this->konversiRepository->runJavaCode($request);
        return $data;
    }

    public function update($request)
    {
        $data = $this->konversiRepository->updateKonversi($request);
        return $data;
    }

    public function submitKonversi($request)
    {
        $opr = $this->konversiRepository->submitKonversi($request);

        return $opr;
    }

    public function tableUjianKonversi($request)
    {
        $data = $this->konversiRepository->tableUjianKonversi($request);
        return $data;
    }

    public function tableDetail($request)
    {
        $data = $this->konversiRepository->tableDetail($request);
        return $data;
    }
}
