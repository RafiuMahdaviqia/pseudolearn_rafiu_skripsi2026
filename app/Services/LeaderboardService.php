<?php

namespace App\Services;

use App\Core\BaseResponse;
use Illuminate\Support\Facades\DB;
use App\Repositories\LeaderboardRepository;

class LeaderboardService
{
    protected $leaderboardRepository;

    public function __construct()
    {
        $this->leaderboardRepository = new LeaderboardRepository();
    }

    public function table($request)
    {
        $data = $this->leaderboardRepository->table($request);
        return $data;
    }

    public function getRank($rank)
    {
        $data = $this->leaderboardRepository->getRank($rank);
        
        if (
            (is_array($data) && isset($data['total_skor']) && (float)$data['total_skor'] == 0.0) ||
            (is_object($data) && isset($data->total_skor) && (float)$data->total_skor == 0.0)
        ) {
            return null;
        }

        return $data;
    }

    public function getRankByIdMahasiswa($id_mahasiswa)
    {
        $data = $this->leaderboardRepository->getRankByIdMahasiswa($id_mahasiswa);
        
        if (
            (is_array($data) && isset($data['total_skor']) && (float)$data['total_skor'] == 0.0) ||
            (is_object($data) && isset($data->total_skor) && (float)$data->total_skor == 0.0)
        ) {
            return null;
        }

        return $data;
    }
}
