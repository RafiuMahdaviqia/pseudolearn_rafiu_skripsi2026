<?php

namespace App\Http\Controllers\Ujian;

use App\Models\Soal;
use App\Models\Nyawa;
use Illuminate\Http\Request;
use App\Services\UjianService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UjianController extends Controller
{
    protected $soalModel;
    protected $ujianService;

    public function __construct()
    {
        $this->soalModel = new Soal();
        $this->ujianService = new UjianService();
    }

    public function index(Request $request)
    {
        $id = $request->query('id');
        $soal = $this->soalModel->find($id);

        $idUser = Auth::id();
        $nyawa = Nyawa::where('id_user', $idUser)->first();

        return view('pages.ujian.index', [
            'title' => 'Ujian Pseudocode',
            'soal' => $soal,
            'id_level' => $soal->id_level,
            'lives' => $nyawa->nyawa,
            'max_lives' => $nyawa->max_nyawa,
            'next_regen_at' => $nyawa->next_regen_at
        ]);
    }

    public function submit(Request $request)
    {
        $opr = $this->ujianService->submit($request);

        return $opr;
    }

    public function sendLog(Request $request)
    {
        $opr = $this->ujianService->sendLog($request);

        return $opr;
    }
}
