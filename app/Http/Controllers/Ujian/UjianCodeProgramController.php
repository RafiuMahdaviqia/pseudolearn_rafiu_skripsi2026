<?php

namespace App\Http\Controllers\Ujian;

use App\Models\Soal;
use App\Models\Nyawa;
use App\Models\Konversi;
use Illuminate\Http\Request;
use App\Services\KonversiService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UjianCodeProgramController extends Controller
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

        return view('pages.ujian.ujianCodeProgram', [
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
