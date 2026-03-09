<?php

namespace App\Repositories;

use App\Models\BankSoalKonversi;

class BankSoalKonversiRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new BankSoalKonversi();
    }

    public function table($request)
    {
        return $this->model->latest()->get();
    }

    public function store($request)
    {
        return $this->model->create($request->all());
    }

    public function update($request)
    {
        $data = $this->model->find($request->id);
        return $data->update($request->all());
    }

    public function destroy($id)
    {
        return $this->model->find($id)->delete();
    }

    public function detail($id)
    {
        return $this->model->find($id);
    }
}
