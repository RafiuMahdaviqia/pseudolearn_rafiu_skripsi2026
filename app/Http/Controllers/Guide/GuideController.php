<?php


namespace App\Http\Controllers\Guide;

use App\Models\Guide;
use App\Core\BaseResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class GuideController extends Controller
{

    public function __construct()
    {
    }

    public function index()
    {
        return view('pages.guide.admin', [
            'title' => 'Pengaturan Bantuan',
        ]);
    }

    public function getData(Request $request)
    {
        $data = Guide::all();
        return BaseResponse::json($data);
    }

    public function getDataById($id)
    {
        $data = Guide::find($id);
        if (!$data) {
            return BaseResponse::errorMessage('Data tidak ditemukan', 404);
        }

        return BaseResponse::json($data);
    }

    public function saveData(Request $request)
    {
        $inputData = $request->input('data');
        if (is_string($inputData)) {
            $inputData = json_decode($inputData, true);
        }
        if (!is_array($inputData)) {
            return BaseResponse::errorMessage('Format data tidak valid', 422);
        }
    
        $rows = [];
        $now = now();
    
        DB::beginTransaction();

        try {
            // Hapus semua data lama (aman di dalam transaksi)
            Guide::query()->delete();

            foreach ($inputData as $index => $item) {
                $imgPath = null;
                // dd($item);
                if (!empty($item['img']) && str_starts_with($item['img'], 'data:image/')) {
                    if (preg_match('/^data:image\/(\w+);base64,/', $item['img'], $type)) {
                        $ext = $type[1] ?? 'png';
                    } else {
                        $ext = 'png';
                    }
                    $imgData = substr($item['img'], strpos($item['img'], ',') + 1);
                    $imgData = base64_decode($imgData);

                    $filename = uniqid('guide_', true) . '.' . $ext;
                    $dir = public_path('assets/media/guide_image');
                    if (!is_dir($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    file_put_contents($dir . '/' . $filename, $imgData);
                    $imgPath = 'assets/media/guide_image/' . $filename;
                } elseif (!empty($item['img'])) {
                    // kalau sudah path (dari DB), simpan langsung path-nya
                    $imgPath = $item['img'];
                } else {
                    $imgPath = null;
                }


                $rows[] = [
                    'id'         => (string) \Illuminate\Support\Str::uuid(),
                    'order'      => $index + 1, // urutan sesuai posisi elemen di array (mulai dari 1)
                    'judul'      => $item['title'] ?? $item['judul'] ?? null,
                    'desc'       => $item['desc'] ?? null,
                    'img'        => $imgPath,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            $opr = false;
            if (!empty($rows)) {
                $opr = Guide::insert($rows);
            }

            DB::commit();
            return BaseResponse::updated($opr);
        } catch (\Throwable $e) {
            DB::rollBack();
            return BaseResponse::errorMessage('Gagal menyimpan data: ' . $e->getMessage(), 500);
        }
    }

}
