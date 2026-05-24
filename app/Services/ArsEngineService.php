<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\ArsResult;
use Illuminate\Support\Str;

class ArsEngineService
{

    private function determineLabelAndScore($drag, $time)
    {
        if ($drag < 18 && $time < 53)  return ['Ideal', 90];
        if ($drag >= 18 && $time >= 53) return ['Struggling', 30];
        if ($drag < 18 && $time >= 53)  return ['Normal', 70];
        if ($drag >= 18 && $time < 53)  return ['Gaming the System', 50];

        return [null, null];
    }

    public function getRawData($idMahasiswa, $idLevel)
    {
        $data = DB::table('v_pseudo_konversicode')
            ->where('id_mahasiswa', $idMahasiswa)
            ->when($idLevel, fn($q) => $q->where('id_level', $idLevel))
            ->get();

        return [
            'pseudo' => $data->where('jenis_soal', 'pseudo')->values(),
            'konversi' => $data->where('jenis_soal', 'konversi')->values(),
        ];
    }

    public function buildPairing($pseudo, $konversi)
    {
        $pairs = [];

        $konversiLatest = $konversi
            ->groupBy('id_soal')->map(fn($items) => $items
            ->sortByDesc('attempt_index')
            ->first()
        );

        $pseudoLatest = $pseudo->groupBy('id_soal')->map(fn($items) =>
            $items->sortByDesc('attempt_index')->first()
        );

        foreach ($pseudoLatest as $idSoal => $p) {
            $k = $konversiLatest->get($idSoal);
            if (!$k) continue;

            $pairs[] = [
                'id_soal'    => $p->id_soal,
                'difficulty' => strtolower($p->difficulty),
                'pair_index' => $p->pair_index,
                'pseudo'     => [
                    'langkah' => $p->langkah,
                    'durasi'  => $p->durasi,
                ],
                'konversi'   => [
                    'langkah' => $k->langkah,
                    'durasi'  => $k->durasi,
                ]
            ];
        }
        
        usort($pairs, fn($a, $b) => $a['pair_index'] <=> $b['pair_index']);

        return $pairs;
    }

    public function runArs($pairs)
    {
        $results = [];
        $batch = 1;
        $pairCounter = 0;
        $arsCounter = 0;

        foreach ($pairs as $pair) {

            [$pLabel, $pScore] = $this->determineLabelAndScore(
                $pair['pseudo']['langkah'] ?? 0,
                $pair['pseudo']['durasi'] ?? 0
            );

            [$kLabel, $kScore] = $this->determineLabelAndScore(
                $pair['konversi']['langkah'] ?? 0,
                $pair['konversi']['durasi'] ?? 0
            );

            $isStable =
                in_array($pLabel, ['Ideal', 'Normal']) &&
                in_array($kLabel, ['Ideal', 'Normal']);

            $isArs = false;

            if (!$isStable) {
                if ($pair['difficulty'] !== 'hard') {
                    if (in_array($pLabel, ['Struggling', 'Gaming the System']) ||
                        in_array($kLabel, ['Struggling', 'Gaming the System'])) {
                        $isArs = true;
                    }
                }
            }

            $results[] = [
                'id_soal' => $pair['id_soal'],
                'difficulty' => $pair['difficulty'],

                'pseudo' => [
                    'label' => $pLabel,
                    'score' => $pScore,
                ],

                'konversi' => [
                    'label' => $kLabel,
                    'score' => $kScore,
                ],

                'is_ars' => $isArs,
                'batch' => $batch,
                'pair_index' => $pairCounter + 1,
            ];

            $pairCounter++;

            if ($isArs) {
                $arsCounter++;
            }

            if ($pairCounter === 5) {
                if ($arsCounter > 0) {
                    $batch++;
                }

                $pairCounter = 0;
                $arsCounter = 0;
            }

            if ($pair['difficulty'] === 'hard' && $isStable) {
                break;
            }
        }

        return [
            'total_pair' => count($results),
            'total_ars' => collect($results)->where('is_ars', true)->count(),
            'batch_count' => $batch,
            'data' => $results,
        ];
    }

    public function process($idMahasiswa, $idLevel)
    {
        $raw = $this->getRawData($idMahasiswa, $idLevel);

        $pairs = $this->buildPairing(
            $raw['pseudo'],
            $raw['konversi']
        );

        if (empty($pairs)) {
            return [
                'batch_count' => 0,
                'data' => [],
                'message' => 'No data found'
            ];
        }

        return $this->runArs($pairs);
    }
}
