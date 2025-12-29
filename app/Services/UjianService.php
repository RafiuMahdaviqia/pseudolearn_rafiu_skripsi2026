<?php

namespace App\Services;

use App\Core\BaseResponse;
use Illuminate\Support\Facades\DB;
use App\Repositories\UjianRepository;

class UjianService
{
    protected $ujianRepository;


    public function __construct()
    {
        $this->ujianRepository = new UjianRepository();
    }
    public function submit($request){
        $opr = $this->ujianRepository->submit($request);

        return $opr;
    }

    public function sendLog($request)
    {
        $opr = $this->ujianRepository->sendLog($request);

        return $opr;
    }
}
