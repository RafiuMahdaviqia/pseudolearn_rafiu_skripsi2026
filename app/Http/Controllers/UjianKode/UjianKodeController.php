<?php

namespace App\Http\Controllers\UjianKode;

use App\Models\UjianKode;
use App\Models\Soal;
use App\Models\Nyawa;
use App\Models\Konversi;
use Illuminate\Http\Request;
use App\Services\KonversiService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UjianKodeController extends Controller
{
    protected $soalModel;
    protected $konversiModel;
    protected $konversiService;

    public function __construct()
    {
        $this->soalModel = new Soal();
        $this->konversiModel = new Konversi();
        $this->konversiService = new KonversiService();
    }

    public function index(Request $request)
    {
        $id = $request->query('id');
        $soalKonversi = $this->konversiModel->setView('v_konversi')->where('id', $id)->first();
        $soal = $this->soalModel->where('id', $soalKonversi->id_soal)->first();

        $idUser = Auth::id();
        $nyawa = Nyawa::where('id_user', $idUser)->first();

        // Check and regenerate lives (1 life per 10 minutes)
        $nyawa->checkAndRegenerate();

        return view('pages.ujian.ujianKode', [
            'title' => 'Ujian Code Program',
            'soal' => $soal,
            'konversi' => $soalKonversi,
            'lives' => $nyawa->nyawa,
            'max_lives' => $nyawa->max_nyawa,
            'next_regen_at' => $nyawa->next_regen_at
        ]);
    }

    public function submitKonversi(Request $request)
    {
        $opr = $this->konversiService->submitKonversi($request);

        return $opr;
    }
}
