<?php


namespace App\Http\Controllers\Nyawa;

use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use App\Services\KonversiService;
// use App\Models\Level as LevelModel;
// use App\Models\Soal as SoalModel;
// use App\Models\Konversi;
use App\Models\Nyawa;
use Illuminate\Support\Facades\Auth;

class NyawaController extends Controller
{
    protected $model;

    public function __construct()
    {
        $this->model = new Nyawa();
    }

   // Ambil status nyawa user
    public function status()
    {
        $idUser = Auth::id();
        $nyawa = $this->model->where('id_user', $idUser)->first();

        // Check and regenerate lives (1 life per 10 minutes)
        $nyawa->checkAndRegenerate();

        return response()->json([
            'lives' => $nyawa->nyawa,
            'max_lives' => $nyawa->max_nyawa,
            'next_regen_at' => $nyawa->next_regen_at
        ]);
    }

    // Halaman untuk lihat nyawa
    public function show()
    {
        $nyawa = $this->model->where('id_user', Auth::id())->first();
        return view('lives.index', compact('nyawa'));
    }
}