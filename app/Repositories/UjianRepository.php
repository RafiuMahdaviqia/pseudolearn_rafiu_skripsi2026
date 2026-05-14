<?php

namespace App\Repositories;

use App\Models\Soal;
use App\Models\Level;
use App\Models\Nyawa;
use App\Models\Ujian;
use App\Models\LogData;
use App\Models\LabelSkor;
use App\Models\Mahasiswa;
use App\Core\BaseResponse;
use App\Models\Pencapaian;
use Illuminate\Support\Str;
use App\Models\HistoryJawaban;
use App\Models\HistoryConfidence;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Models\ArsResult;
    
/**
 * Class KelasRepository.
 * 
 * @package namespace App\Repositories;
 */
class UjianRepository extends BaseRepository
{

    protected $model;
    protected $mahasiswaModel;
    protected $logDataModel;
    protected $ujianModel;
    protected $levelModel;
    protected $historyJawabanModel;
    protected $historyConfidenceModel;
    protected $labelSkorModel;

    public function __construct()
    {
        $this->model = new Soal();
        $this->mahasiswaModel = new Mahasiswa();
        $this->logDataModel = new LogData();
        $this->ujianModel = new Ujian();
        $this->levelModel = new Level();
        $this->historyJawabanModel = new HistoryJawaban();
        $this->historyConfidenceModel = new HistoryConfidence();
        $this->labelSkorModel = new LabelSkor();
    }

    /**
     * Specify the model class name.
     *
     * @return string
     */
    public function model()
    {
        return $this->model;
    }

    /**
     * Boot up the repository, pushing criteria.
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function boot()
    {
        // Add your boot logic here
    }

    public function submit($request){
        try {
            DB::beginTransaction();

            $idMahasiswa = $this->mahasiswaModel->where('id_user', Auth::id())->value('id');
            $soal = $this->model->where('id', $request->input('soal_id'))->first();
            $dataLevel = $this->levelModel->find($soal->id_level);

            if(!$soal){
                return BaseResponse::errorMessage('Soal tidak ditemukan');
            }

            $jawaban = $request->input('jawaban', []);
            $jawabanTipe = $jawaban['tipe_data'] ?? [];
            $jawabanAlgo = $jawaban['algoritma'] ?? [];
            $historyJawabanTipe = [];
            $historyJawabanAlgo = [];

           $decodeJson = function ($raw) {
                if (is_array($raw)) return $raw;

                $clean = trim($raw);

                if ((Str::startsWith($clean, '"') && Str::endsWith($clean, '"')) ||
                    (Str::startsWith($clean, "'") && Str::endsWith($clean, "'"))) {
                    $clean = substr($clean, 1, -1);
                }

                $decoded = json_decode($clean, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $decoded = json_decode(stripslashes($clean), true);
                }

                return is_array($decoded) ? $decoded : [];
            };

            $kunciTipe = collect($decodeJson($soal->kunci_tipe_data))
                ->filter(fn($r) => ($r['variabel'] ?? null) !== null)
                ->values()
                ->toArray();

            $kunciAlgo = collect($decodeJson($soal->kunci_algoritma))
                ->values()
                ->toArray();

            $tipeMismatch = [];
            $isCorrectTipe = true;

            if(count($jawabanTipe) !== count($kunciTipe)){
                $isCorrectTipe = false;
                $tipeMismatch[] = ['reason'=>'length_not_match','expected_count'=>count($kunciTipe),'given_count'=>count($jawabanTipe)];
            } else {
                foreach($kunciTipe as $i => $row){
                    $jawabRow = $jawabanTipe[$i] ?? [];
                    $expectedVariabel = $row['variabel'] ?? null;
                    $expectedTipe     = $row['tipe_data'] ?? null;
                    $givenVariabel    = $jawabRow['variabel'] ?? null;
                    $givenTipe        = $jawabRow['jawaban'] ?? null;

                    if($expectedVariabel !== $givenVariabel || strtolower($expectedTipe) !== strtolower($givenTipe)){
                        $isCorrectTipe = false;
                        $tipeMismatch[] = [
                            'index'=>$i,
                            'expected'=>['variabel'=>$expectedVariabel,'tipe_data'=>$expectedTipe],
                            'given'=>['variabel'=>$givenVariabel,'tipe_data'=>$givenTipe]
                        ];
                    }

                    $historyJawabanTipe[] = [
                        'id' => (string) Str::uuid(),
                        'id_level' => $soal->id_level,
                        'id_soal' => $soal->id,
                        'id_mahasiswa' => $idMahasiswa,
                        'index_tipe_data' => $i,
                        'tipe_data' => $givenTipe,
                        'index_algoritma' => null,
                        'algoritma' => null,
                        'status' => $isCorrectTipe ? 'benar' : 'salah',
                        'created_at' => now(),
                        'updated_at' => now(),
                        'deleted_at' => null,
                    ];
                }
            }

            $algoMismatch = [];
            $isCorrectAlgo = true;

            $kunciLangkah = array_map(fn($r)=>trim($r['langkah'] ?? ''), $kunciAlgo);
            $jawabLangkah = array_map(fn($r)=>trim($r['langkah'] ?? ''), $jawabanAlgo);

            if(count($jawabLangkah) !== count($kunciLangkah)){
                $isCorrectAlgo = false;
                $algoMismatch[] = ['reason'=>'length_not_match','expected_count'=>count($kunciLangkah),'given_count'=>count($jawabLangkah)];
            } else {
                foreach($kunciLangkah as $i => $exp){
                    $given = $jawabLangkah[$i] ?? '';
                    if($exp !== $given){
                        $isCorrectAlgo = false;
                        $algoMismatch[] = [
                            'index'=>$i,
                            'expected'=>$exp,
                            'given'=>$given
                        ];
                    }

                    $historyJawabanAlgo[] = [
                        'id' => (string) Str::uuid(),
                        'id_level' => $soal->id_level,
                        'id_soal' => $soal->id,
                        'id_mahasiswa' => $idMahasiswa,
                        'index_tipe_data' => null,
                        'tipe_data' => null,
                        'index_algoritma' => $i,
                        'algoritma' => $given,
                        'status' => $isCorrectAlgo ? 'benar' : 'salah',
                        'created_at' => now(),
                        'updated_at' => now(),
                        'deleted_at' => null,
                    ];
                }
            }

            $isCorrectAll = $isCorrectTipe && $isCorrectAlgo;

            $dataUjian = [
                'id' => (string) Str::uuid(),
                'id_level' => $dataLevel->id,
                'id_soal' => $soal->id,
                'id_mahasiswa' => $idMahasiswa,
                'waktu' => $request->waktu,
                'status' => $isCorrectAll ? 1 : 0,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ];

            $dataHistoryConfidence = [
                'id_level' => $soal->id_level,
                'id_soal' => $soal->id,
                'id_mahasiswa' => $idMahasiswa,
                'id_ujian' => $dataUjian['id'],
                'status_jawaban' => $isCorrectAll ? 1 : 0,
                'status_confidence' => $request->confidence,
            ];

            $this->historyJawabanModel->insert(array_merge($historyJawabanTipe, $historyJawabanAlgo));
            $this->ujianModel->insert($dataUjian);
            $this->historyConfidenceModel->create($dataHistoryConfidence);
            
            $dataPencapaian = Pencapaian::where('id_mahasiswa', $idMahasiswa)
                ->where('id_level', $soal->id_level)
                ->where('id_soal', $soal->id)
                ->where('category', 'soal')
                ->first();

            $returnPencapaian = null;
            $returnPencapaianBadge = null;
            
            if ($dataPencapaian && $dataPencapaian->status == 0 && $isCorrectAll) {
                $dataPencapaian->update([
                    'status' => 1,
                    'updated_at' => now(),
                ]);

                $returnPencapaian = [
                    'id' => $dataPencapaian->id,
                ];
            }

            DB::commit();

            if($isCorrectAll) {
                $ujianQuery = $this->ujianModel->setView('v_ujian')
                    ->where('id_mahasiswa', $idMahasiswa)
                    ->where('id_level', $soal->id_level)
                    ->where('id_soal', $soal->id);
                $totalWaktuDetik = $ujianQuery->sum('waktu');

                $logDataQuery = $this->logDataModel->setView('v_log_data')
                    ->where('id_mahasiswa', $idMahasiswa)
                    ->where('id_level', $soal->id_level)
                    ->where('id_soal', $soal->id);
                $totalDrag = $logDataQuery->count();

                [$label, $skor] = $this->determineLabelAndScore($totalDrag, $totalWaktuDetik);

                $existing = $this->labelSkorModel
                    ->where('id_level', $soal->id_level)
                    ->where('id_soal', $soal->id)
                    ->where('id_mahasiswa', $idMahasiswa)
                    ->first();

                if ($existing) {
                    $existing->update([
                        'label' => $label,
                        'skor' => $skor,
                        'updated_at' => now(),
                    ]);
                } else {
                    $this->labelSkorModel->create([
                        'id' => (string) Str::uuid(),
                        'id_level' => $soal->id_level,
                        'id_soal' => $soal->id,
                        'id_mahasiswa' => $idMahasiswa,
                        'label' => $label,
                        'skor' => $skor,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $arsResult = ArsResult::where('id_mahasiswa', $idMahasiswa)
                    ->where('id_level', $soal->id_level)
                    ->where('id_soal', $soal->id)
                    ->first();

                if ($arsResult) {
                    $arsResult->update([
                        'pseudo_label'   => $label,
                        'pseudo_score'   => $skor,
                        'pseudo_langkah' => $totalDrag,      
                        'pseudo_durasi'  => $totalWaktuDetik,
                    ]);
                }
                    
                if($label === 'Ideal' || $label === 'Normal'){
                        $dataPencapaianBadge = Pencapaian::where('id_mahasiswa', $idMahasiswa)
                            ->where('id_level', $soal->id_level)
                            ->where('id_soal', $soal->id)
                            ->where('category', 'badge')
                            ->first();

                        
                        if ($dataPencapaianBadge && $dataPencapaianBadge->status == 0 && $isCorrectAll) {
                            $dataPencapaianBadge->update([
                                'status' => 1,
                                'updated_at' => now(),
                            ]);

                            $returnPencapaianBadge = [
                                'id' => $dataPencapaianBadge->id,
                            ];
                            
                        }
                       
                    }

                $returnData = [
                    'correct' => true,
                    'pencapaian' => $returnPencapaian,
                    'badge' => $returnPencapaianBadge,
                ];

                return response()->json($returnData);
            } else {
                $nyawa = Nyawa::where('id_user', Auth::id())->first();

                if ($nyawa->nyawa > 0) {
                    $nyawa->nyawa -= 1;

                    if ($nyawa->next_regen_at === null && $nyawa->nyawa < $nyawa->max_nyawa) {
                        $nyawa->next_regen_at = now()->addMinutes(10);
                    }

                    $nyawa->save();
                }

                return BaseResponse::json([
                    'correct' => $isCorrectAll,
                    'correct_tipe_data' => $isCorrectTipe,
                    'correct_algoritma' => $isCorrectAlgo,
                    'tipe_mismatch' => $dataLevel->feedback_data_type ?? null,
                    'algoritma_mismatch' => $dataLevel->feedback_algorithm ?? null,
                    'id_level' => $soal->id_level
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return BaseResponse::errorMessage($e->getMessage());
        }
    }

    /**
     * Tentukan label dan skor berdasarkan totalDrag dan totalWaktuDetik.
     *
     * @param int $totalDrag
     * @param int $totalWaktuDetik
     * @return array [label, skor]
     */
    private function determineLabelAndScore($totalDrag, $totalWaktuDetik)
    {
        if ($totalDrag <= 18 && $totalWaktuDetik < 53) {
            return ['Ideal', 90];
        } elseif ($totalDrag > 18 && $totalWaktuDetik >= 53) {
            return ['Struggling', 30];
        } elseif ($totalDrag <= 18 && $totalWaktuDetik >= 53) {
            return ['Normal', 70];
        } elseif ($totalDrag >= 18 && $totalWaktuDetik < 53) {
            return ['Gaming the System', 50];
        } else {
            return [null, null];
        }
    }

    public function sendLog($request)
    {
        try {
            DB::beginTransaction();

            $idUser = Auth::id();
            $idMahasiswa = $this->mahasiswaModel->where('id_user', $idUser)->value('id');

            $data = [
                'id_soal' => $request['soal_id'],
                'id_mahasiswa' => $idMahasiswa,
                'index' => $request['index'],
                'itemText' => $request['item'],
                'timer_second' => $request['timer_second'],
                'type' => $request['jenis'],
                'variabel' => $request['variabel']
            ];

            $opr = $this->logDataModel->create($data);

            DB::commit();
            return BaseResponse::created($opr);
        } catch (\Exception $e) {
            DB::rollBack();
            return BaseResponse::errorTransaction($e);
        }
    }
}
