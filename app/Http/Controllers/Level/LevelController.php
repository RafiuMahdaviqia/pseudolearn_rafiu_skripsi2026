<?php

namespace App\Http\Controllers\Level;

use App\Http\Controllers\Controller;
use App\Services\LevelService;
use Illuminate\Http\Request;
use App\Models\Level;


class LevelController extends Controller
{
    protected $levelService;
    protected $levelModel;

    public function __construct()
    {
        $this->levelService = new LevelService();
        $this->levelModel = new Level();
    }

    public function index()
    {
        return view('pages.level.index', [
            'title' => 'Data Level'
        ]);
    }

    public function form($id = null)
    {
        $data = null;
        if ($id) {
            $data = $this->levelService->getById($id);
        }
        return view('pages.level.form', [
            'title' => $id ? 'Edit Level' : 'Form Level',
            'data' => $data
        ]);
    }

    public function order()
    {
        $dataLevel = $this->levelModel->orderBy('order', 'asc')->get();
        
        return view('pages.level.order', [
            'title' => 'Urutan Level',
            'dataLevel' => $dataLevel
        ]);
    }

    public function table()
    {
        $opr = $this->levelService->table();
        return $opr;
    }

    public function store(Request $request)
    {
        $opr = $this->levelService->store($request);
        return $opr;
    }

    public function getById($id)
    {
        $opr = $this->levelService->getById($id);
        return $opr;
    }

    public function update(Request $request)
    {
        $opr = $this->levelService->update($request);
        return $opr;
    }

    public function destroy($id)
    {
        $opr = $this->levelService->destroy($id);
        return $opr;
    }

    public function getData()
    {
        $opr = $this->levelService->getData();
        return $opr;
    }

    public function updateOrder(Request $request)
    {
        $opr = $this->levelService->updateOrder($request);
        return $opr;
    }

    public function updateActive(Request $request)
    {
        $level = $this->levelModel->find($request->input('id'));
        if (!$level) {
            return response()->json(['message' => 'Level not found'], 404);
        }

        $level->manual_active = $request->input('manual_active', $level->manual_active);
        $level->save();

        return response()->json(['message' => 'Level active status updated successfully', 'data' => $level]);
    }
}
