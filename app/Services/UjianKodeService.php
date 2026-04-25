<?php

namespace App\Services;

use App\Repositories\UjianKodeRepository;

class UjianKodeService
{
    protected $ujianKodeRepository;

    public function __construct()
    {
        $this->ujianKodeRepository = new UjianKodeRepository();
    }

    public function submitKonversi($request)
    {
        return $this->ujianKodeRepository->submitKonversi($request);
    }
}
