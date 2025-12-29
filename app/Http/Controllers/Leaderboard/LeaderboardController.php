<?php

namespace App\Http\Controllers\Leaderboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\LeaderboardService;

class LeaderboardController extends Controller
{
    protected $leaderboardService;

    public function __construct()
    {
        $this->leaderboardService = new LeaderboardService();
    }

    public function index()
    {
        $rank1 = $this->leaderboardService->getRank(1) ?? [];
        $rank2 = $this->leaderboardService->getRank(2) ?? [];
        $rank3 = $this->leaderboardService->getRank(3) ?? [];

        return view('pages.leaderboard.index', [
            'title' => 'Leaderboard', // Judul untuk ditampilkan di navbar
            'rank1' => $rank1,
            'rank2' => $rank2,
            'rank3' => $rank3
        ]);
    }

    public function table(Request $request)
    {
        $data = $this->leaderboardService->table($request);
        return response()->json($data);
    }
}
