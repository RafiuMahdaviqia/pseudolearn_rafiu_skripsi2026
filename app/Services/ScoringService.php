<?php

namespace App\Services;

use App\Core\BaseResponse;
use Illuminate\Support\Facades\DB;
use App\Repositories\LabelingRepository;

class ScoringService
{
    protected $labelingRepository;

    public function __construct()
    {
        $this->labelingRepository = new LabelingRepository();
    }

    public function table($request)
    {
        $data = $this->labelingRepository->table($request);
        return $data;
    }

    public function updateTest($data)
    {
        $opr = $this->labelingRepository->updateTest($data);
        return $opr;
    }

    public function calculateManual($data)
    {
        $opr = $this->labelingRepository->calculateManual($data);
        return $opr;
    }

    public function calculateAverage($data)
    {
        $opr = $this->labelingRepository->calculateAverage($data);
        return $opr;
    }
}
