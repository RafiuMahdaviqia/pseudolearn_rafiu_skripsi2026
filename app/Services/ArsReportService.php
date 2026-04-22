<?php

namespace App\Services;

use App\Core\BaseResponse;
use Illuminate\Support\Facades\DB;
use App\Repositories\ArsReportRepository;

class ArsReportService
{
    protected $arsReportRepository;

    public function __construct()
    {
        $this->arsReportRepository = new ArsReportRepository();
    }

    public function table($request)
    {
        $data = $this->arsReportRepository->table($request);
        return $data;
    }

    public function tableArsLog($request)
{
    $data = $this->arsReportRepository->tableArsLog($request);
    return $data;
}
}