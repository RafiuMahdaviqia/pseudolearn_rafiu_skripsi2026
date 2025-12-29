<?php

namespace App\Services;

use App\Core\BaseResponse;
use Illuminate\Support\Facades\DB;
use App\Repositories\LogActivityRepository;

class LogActivityService
{
    protected $logActivityRepository;

    public function __construct()
    {
        $this->logActivityRepository = new LogActivityRepository();
    }

    public function table($request)
    {
        $data = $this->logActivityRepository->table($request);
        return $data;
    }

    public function tableDetailLog($request)
    {
        $data = $this->logActivityRepository->tableDetailLog($request);
        return $data;
    }
}
