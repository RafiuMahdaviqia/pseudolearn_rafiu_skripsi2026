<?php

namespace App\Services;

use App\Services\ArsEngineService;
use App\Repositories\ArsReportRepository;
use App\Models\ArsResult;

class ArsReportService
{
    protected $arsEngine;
    protected $arsReportRepository;

    public function __construct()
    {
        $this->arsEngine = new ArsEngineService();
        $this->arsReportRepository = new ArsReportRepository();
    }

    public function table($request)
    {
        return $this->arsReportRepository->table($request);
    }

    public function tableArsLog($request)
    {
        return $this->arsReportRepository->tableArsLog($request);
    }

    public function getPairedData($idMahasiswa, $idLevel)
    {
        $raw = $this->arsEngine->getRawData($idMahasiswa, $idLevel);

        $pairs = $this->arsEngine->buildPairing(
            $raw['pseudo'],
            $raw['konversi']
        );

        return $pairs;
    }

    public function processArs($idMahasiswa, $idLevel)
    {
        $raw = $this->arsEngine->getRawData($idMahasiswa, $idLevel);

        $pairs = $this->arsEngine->buildPairing(
            $raw['pseudo'],
            $raw['konversi']
        );

        $arsData = $this->arsEngine->runArs($pairs);

        return $arsData;
    }

    public function getDetailArs($idMahasiswa, $idLevel)
    {
        return $this->arsReportRepository->getDetailArs($idMahasiswa, $idLevel);
    }

    private function saveArsResult($idMahasiswa, $idLevel, $arsData)
    {
        foreach ($arsData['data'] as $item) {

            // is_ars = true
            if (!$item['is_ars']) continue;

            ArsResult::updateOrCreate(
                [
                    'id_mahasiswa' => $idMahasiswa,
                    'id_level'     => $idLevel,
                    'id_soal'      => $item['id_soal'],
                ],
                [
                    'ars_batch'      => $item['batch'] ?? 1,
                    'difficulty'     => $item['difficulty'],
                    'pseudo_label'   => $item['pseudo']['label'],
                    'pseudo_score'   => $item['pseudo']['score'],
                    'konversi_label' => $item['konversi']['label'],
                    'konversi_score' => $item['konversi']['score'],
                ]
            );
        }
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> new-pseudolearn-ars-dda-decoy
