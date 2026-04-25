<?php

namespace App\Http\Controllers\UjianKode;

use App\Models\Soal;
use App\Models\Nyawa;
use App\Models\Konversi;
use Illuminate\Http\Request;
use App\Services\UjianKodeService;
use App\Http\Controllers\Controller;
use App\Models\UjianKode;
use Illuminate\Support\Facades\Auth;

class UjianKodeController extends Controller
{
    protected $soalModel;
    protected $konversiModel;
    protected $ujianKodeService;

    public function __construct()
    {
        $this->soalModel = new Soal();
        $this->konversiModel = new UjianKode();
        $this->ujianKodeService = new UjianKodeService();
    }

    public function index(Request $request)
    {
        $id = $request->query('id');
        $bankSoalKonversi = $this->konversiModel->setView('v_bank_soal_konversi')->where('id', $id)->first();

        if (!$bankSoalKonversi) {
            abort(404, 'Soal tidak ditemukan.');
        }

        $soal = $this->soalModel->where('id', $bankSoalKonversi->id_soal)->first();

        if (!$soal) {
            abort(404, 'Data soal tidak ditemukan.');
        }

        $idUser = Auth::id();
        $nyawa  = Nyawa::where('id_user', $idUser)->first();

        if (!$nyawa) {
            abort(404, 'Data nyawa pengguna tidak ditemukan.');
        }

        // Check dan regenerate lives (1 life per 10 minutes)
        $nyawa->checkAndRegenerate();

        return view('pages.Ujian.ujianKode', [
            'title' => 'Ujian Code Program',
            'soal' => $soal,
            'konversi' => $bankSoalKonversi,
            'lives' => $nyawa->nyawa,
            'max_lives' => $nyawa->max_nyawa,
            'next_regen_at' => $nyawa->next_regen_at
        ]);
    }

    public function submitKonversi(Request $request)
    {
        $opr = $this->ujianKodeService->submitKonversi($request);

        return $opr;
    }
}
