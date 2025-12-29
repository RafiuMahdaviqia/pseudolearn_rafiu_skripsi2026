<?php

namespace App\Services;

use App\Core\BaseResponse;
use Illuminate\Support\Facades\DB;
use App\Repositories\ConfidenceRepository;

class ConfidenceService
{
    protected $confidenceRepository;

    public function __construct()
    {
        $this->confidenceRepository = new ConfidenceRepository();
    }

    public function table($request)
    {
        $data = $this->confidenceRepository->table($request);
        return $data;
    }

    public function tableConfidence($request)
    {
        $data = $this->confidenceRepository->tableConfidence($request);
        return $data;
    }
}
