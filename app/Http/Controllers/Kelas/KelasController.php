<?php

namespace App\Http\Controllers\Kelas;

use App\Http\Controllers\Controller;
use App\Services\KelasService;
use Illuminate\Http\Request;


class KelasController extends Controller
{
    protected $kelasService;

    public function __construct()
    {
        $this->kelasService = new KelasService();
    }

    public function index()
    {
        return view('pages.kelas.index', [
            'title' => 'Data Kelas'
        ]);
    }

    public function table()
    {
        $opr = $this->kelasService->table();
        return $opr;
    }

    public function store(Request $request)
    {
        $opr = $this->kelasService->store($request);
        return $opr;
    }

    public function getById($id)
    {
        $opr = $this->kelasService->getById($id);
        return $opr;
    }

    public function update(Request $request)
    {
        $opr = $this->kelasService->update($request);
        return $opr;
    }

    public function destroy($id)
    {
        $opr = $this->kelasService->destroy($id);
        return $opr;
    }

    public function getData()
    {
        $opr = $this->kelasService->getData();
        return $opr;
    }
}
