<?php

namespace App\Services;

use App\Core\BaseResponse;
use Illuminate\Support\Facades\DB;
use App\Repositories\OverlappingRepository;

class OverlappingService
{
    protected $overlappingRepository;

    public function __construct()
    {
        $this->overlappingRepository = new OverlappingRepository();
    }

    public function tableSoal($request)
    {
        $data = $this->overlappingRepository->tableSoal($request);
        return $data;
    }

    public function getAnalysisData($request)
    {
        $opr = $this->overlappingRepository->getAnalysisData($request);

        return $opr;
    }

    public function tableDetail($request)
    {
        $data = $this->overlappingRepository->tableDetail($request);
        return $data;
    }
}
