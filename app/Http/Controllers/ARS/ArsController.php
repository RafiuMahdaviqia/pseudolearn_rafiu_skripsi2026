<?php

namespace App\Http\Controllers\ARS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ArsReportService;
use App\Models\Soal;
use App\Models\Konversi;
use App\Models\ArsResult;
use App\Models\Level;
use App\Models\Kelas;
use App\Models\Mahasiswa;

class ArsController extends Controller
{
    protected $arsReportService;
    protected $levelModel;
    protected $soalModel;
    protected $kelasModel;
    protected $mahasiswaModel;

    public function __construct()
    {
        $this->arsReportService = new ArsReportService();
        $this->levelModel = new Level();
        $this->soalModel = new Soal();
        $this->kelasModel = new Kelas();
        $this->mahasiswaModel = new Mahasiswa();
    }

    public function index()
    {
        $list_kelas = $this->kelasModel->get(['id', 'name', 'angkatan'])->toArray();
        $list_kelas = collect($list_kelas)->prepend(['id' => '', 'name' => 'Semua Kelas', 'angkatan' => '']);
        $list_kelas = collect($list_kelas)->map(function ($item) {
            return [
                'id' => $item['id'],
                'name' => $item['name'],
                'angkatan' => $item['angkatan']
            ];
        })->values()->toArray();

        $list_level = $this->levelModel->orderBy('order', 'asc')->get(['id', 'name'])
            ->pluck('name', 'id')
            ->toArray();

        $list_level = collect($list_level)->map(function ($name, $id) {
            return [
                'id' => $id,
                'name' => $name
            ];
        })->values()->toArray();

        return view('pages.ARS.index', [
            'title' => 'ARS Report',
            'list_kelas' => $list_kelas,
            'list_level' => $list_level
        ]);
    }

    public function table(Request $request)
    {
        $data = $this->arsReportService->table($request);
        return $data;
    }
}

/*public function submitAnswers($id_mahasiswa, $answers = null)
{
    $levels = ['easy', 'medium', 'hard'];
    $nextQuestions = collect();

    // Ambil 5 soal awal
    $pseudo = Soal::where('difficulty', 'easy')->orderBy('id')->take(5)->get();
    $konversi = Konversi::where('difficulty', 'easy')->orderBy('id')->take(5)->get();

    $initialQuestions = collect();
    foreach ($pseudo as $i => $s) {
        $initialQuestions->push([
            'id_soal' => $s->id,
            'jenis_soal' => 'pseudo',
            'difficulty_sekarang' => 'easy'
        ]);
        $initialQuestions->push([
            'id_soal' => $konversi[$i]->id,
            'jenis_soal' => 'konversi',
            'difficulty_sekarang' => 'easy'
        ]);
    }

    if (is_null($answers)) {
        return $initialQuestions;
    }

    // Group by supaya pseudo + konvers berada di satu paket
    $paketGroups = collect($answers)->chunk(2); 

    foreach ($paketGroups as $paket) {
        $pseudoJawab = $paket->firstWhere('jenis_soal', 'pseudo');
        $konversiJawab = $paket->firstWhere('jenis_soal', 'konversi');

        if (!$pseudoJawab) continue;
        $difNow = $pseudoJawab['difficulty_sekarang'];
        $clusterPseudo = $pseudoJawab['cluster'] ?? 'normal';

        //Last pseudo untuk cumulative difficulty
        $lastPseudo = ArsResult::where('id_mahasiswa', $id_mahasiswa)
                        ->where('jenis_soal', 'pseudo')
                        ->latest()
                        ->first();

        $currentIndex = $lastPseudo ? array_search($lastPseudo->difficulty_sekarang, $levels) : 0;

        $nextIndex = in_array($clusterPseudo, ['ideal','normal'])
                        ? min($currentIndex + 1, count($levels)-1)
                        : $currentIndex;

        $rekomendasi = $levels[$nextIndex];

        // Simpan pseudo
        ArsResult::create([
            'id_mahasiswa' => $id_mahasiswa,
            'id_soal' => $pseudoJawab['id_soal'],
            'jenis_soal' => 'pseudo',
            'difficulty_sekarang' => $difNow,
            'cluster' => $clusterPseudo,
            'rekomendasi_difficulty' => $rekomendasi
        ]);

        // Simpan konversi → ikut pseudo
        if ($konversiJawab) {
            ArsResult::create([
                'id_mahasiswa' => $id_mahasiswa,
                'id_soal' => $konversiJawab['id_soal'],
                'jenis_soal' => 'konversi',
                'difficulty_sekarang' => $difNow,
                'cluster' => $clusterPseudo,
                'rekomendasi_difficulty' => $rekomendasi
            ]);
        }

        // Ambil soal paket berikutnya jika belum hard
        if ($rekomendasi !== 'hard') {
            $pseudoNext = Soal::where('difficulty', $rekomendasi)->orderBy('id')->first();
            $konversiNext = Konversi::where('difficulty', $rekomendasi)->orderBy('id')->first();
            if ($pseudoNext && $konversiNext) {
                $nextQuestions->push([
                    ['id_soal' => $pseudoNext->id, 'jenis_soal' => 'pseudo', 'difficulty_sekarang' => $rekomendasi],
                    ['id_soal' => $konversiNext->id, 'jenis_soal' => 'konversi', 'difficulty_sekarang' => $rekomendasi]
                ]);
            }
        }
    }

    return $nextQuestions;
}
    /**
     * Summary performa mahasiswa
     
    public function getPerformanceSummary($id_mahasiswa)
    {
        $results = ArsResult::where('id_mahasiswa', $id_mahasiswa)
                            ->get()
                            ->groupBy('jenis_soal');

        $summary = [];
        foreach ($results as $jenis => $items) {
            $clusters = $items->pluck('cluster')->countBy();
            $low = ($clusters['gaming'] ?? 0) + ($clusters['struggling'] ?? 0);
            $high = ($clusters['ideal'] ?? 0) + ($clusters['normal'] ?? 0);

            $summary[$jenis] = [
                'low' => $low,
                'high' => $high,
                'total' => $items->count()
            ];
        }

        return $summary;
    }

    /**
     * Soal tambahan jika performa buruk
     
    public function determineAdditional($id_mahasiswa)
    {
        $summary = $this->getPerformanceSummary($id_mahasiswa);
        $additional = collect();

        // Pseudo buruk → ambil paket pseudo + konversi
        if (($summary['pseudo']['low'] ?? 0) > 2) {
            $easy = Soal::where('difficulty','easy')->take(2)->get();
            $medium = Soal::where('difficulty','medium')->take(2)->get();
            $hard = Soal::where('difficulty','hard')->take(1)->get();
            $soals = $easy->merge($medium)->merge($hard);

            foreach ($soals as $s) {
                $additional->push(['id_soal'=>$s->id,'jenis_soal'=>'pseudo','difficulty'=>$s->difficulty]);
                $additional->push(['id_soal'=>$s->id,'jenis_soal'=>'konversi','difficulty'=>$s->difficulty]);
            }
        }

        // Konversi buruk → ambil 10 soal konversi
        elseif (($summary['konversi']['low'] ?? 0) > 2) {
            $soals = Konversi::orderByRaw("FIELD(difficulty,'easy','medium','hard')")->take(10)->get();
            foreach ($soals as $s) {
                $additional->push(['id_soal'=>$s->id,'jenis_soal'=>'konversi','difficulty'=>$s->difficulty]);
            }
        }

        return $additional;
    }
}*/