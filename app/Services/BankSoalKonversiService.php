<?php

namespace App\Services;

use App\Repositories\BankSoalKonversiRepository;

class BankSoalKonversiService
{
    protected $bankSoalKonversiRepository;

    public function __construct()
    {
        $this->bankSoalKonversiRepository = new BankSoalKonversiRepository();
    }

    public function table($request)
    {
        return $this->bankSoalKonversiRepository->table($request);
    }

    public function getSoalByLevel($levelId)
    {
        return $this->bankSoalKonversiRepository->getSoalByLevel($levelId);
    }

    public function store($request)
    {
        $jawabanRaw = (string) $request->input('jawaban', '');
        $jawabanLines = preg_split('/\R/', $jawabanRaw) ?: [];
        $jawabanStructured = [];
        $increment = 1;
        foreach ($jawabanLines as $line) {
            $clean = trim($line);
            if ($clean === '') {
                continue;
            }
            $jawabanStructured[] = [$increment => $clean];
            $increment++;
        }

        $payload = [
            'id_level' => $request->input('level_id'),
            'id_soal'  => $request->input('soal_id'),
            'jawaban'  => $jawabanStructured,
            'output'   => $request->input('output'),
        ];
        return $this->bankSoalKonversiRepository->store($payload);
    }

    public function update($request, $id)
    {
        $jawabanRaw = (string) $request->input('jawaban', '');
        $jawabanLines = preg_split('/\R/', $jawabanRaw) ?: [];
        $jawabanStructured = [];
        $increment = 1;
        foreach ($jawabanLines as $line) {
            $clean = trim($line);
            if ($clean === '') {
                continue;
            }
            $jawabanStructured[] = [$increment => $clean];
            $increment++;
        }

        $payload = [
            'id_level' => $request->input('level_id'),
            'id_soal'  => $request->input('soal_id'),
            'jawaban'  => $jawabanStructured,
            'output'   => $request->input('output'),
        ];
        return $this->bankSoalKonversiRepository->update($payload, $id);
    }

    public function destroy($id)
    {
        return $this->bankSoalKonversiRepository->destroy($id);
    }

    public function detail($id)
    {
        return $this->bankSoalKonversiRepository->detail($id);
    }

    public function runKonversi($request)
    {
        return $this->bankSoalKonversiRepository->runJavaCode($request);
    }
}
