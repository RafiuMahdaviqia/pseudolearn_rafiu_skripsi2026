<?php

namespace App\Services;

use App\Models\Kelas;
use App\Core\BaseResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Jobs\UpdatePencapaianLevel;
use App\Repositories\LevelRepository;

class LevelService
{
    protected $levelRepository;

    public function __construct()
    {
        $this->levelRepository = new LevelRepository();
    }

    public function table()
    {
        $opr = $this->levelRepository->table();

        return $opr;
    }

    public function store($data)
    {
        // Validasi file image
        if (!isset($data['logo_level']) || !$data['logo_level']) {
            return BaseResponse::errorMessage('Logo level tidak boleh kosong', 422);
        }

        $image = $data['logo_level'];

        if ($image instanceof UploadedFile) {
            $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg'];
            $maxSize = 2 * 1024 * 1024; // 2MB

            $mime = $image->getMimeType();
            if (!in_array($mime, $allowedMimes)) {
                return BaseResponse::errorMessage('Format gambar tidak valid. Hanya jpg, jpeg, png.', 422);
            }
            if ($image->getSize() > $maxSize) {
                return BaseResponse::errorMessage('Ukuran gambar maksimal 2MB.', 422);
            }

            // Simpan file ke public/assets/media/level_image
            $destinationPath = public_path('assets/media/level_image');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $fileName = uniqid('level_') . '.' . $image->getClientOriginalExtension();
            $image->move($destinationPath, $fileName);
            $relativePath = 'assets/media/level_image/' . $fileName;

            $insertData = $data;
            $insertData['image'] = $relativePath;
        } else {
            // Jika hanya nama file (misal upload sudah dilakukan di tempat lain)
            $insertData = $data;
            $insertData['image'] = $image;
        }

        $getOrderLatest = $this->levelRepository->getLatestOrder();

        $insertData = [
            'name' => $insertData['nama'],
            'feedback_data_type' => $insertData['feedback_tipe_data'] ?? null,
            'feedback_algorithm' => $insertData['feedback_algoritma'] ?? null,
            'image' => $insertData['image'] ?? null,
            'order' => $getOrderLatest + 1, // Increment order based on latest
            'manual_active' => 0 // Default nonaktif
        ];

        $opr = $this->levelRepository->store($insertData);

        return $opr;
    }

    public function getById($id)
    {
        try {
            $opr = $this->levelRepository->getById($id);
            if (!$opr) {
                return BaseResponse::errorMessage('Level tidak ditemukan', 404);
            }

            return BaseResponse::json($opr);
        } catch (\Exception $e) {
            return BaseResponse::errorMessage($e->getMessage());
        }
    }

    public function update($data)
    {
        try {
            DB::beginTransaction();
            $level = $this->levelRepository->getById($data->id);
            if (!$level) {
                return BaseResponse::errorMessage('Level tidak ditemukan', 404);
            }

             // Validasi file image
            if (!isset($data['logo_level']) || !$data['logo_level']) {
                return BaseResponse::errorMessage('Logo level tidak boleh kosong', 422);
            }

            $image = $data['logo_level'];

            if ($image instanceof UploadedFile) {
                $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg'];
                $maxSize = 2 * 1024 * 1024; // 2MB

                $mime = $image->getMimeType();
                if (!in_array($mime, $allowedMimes)) {
                    return BaseResponse::errorMessage('Format gambar tidak valid. Hanya jpg, jpeg, png.', 422);
                }
                if ($image->getSize() > $maxSize) {
                    return BaseResponse::errorMessage('Ukuran gambar maksimal 2MB.', 422);
                }

                // Simpan file ke public/assets/media/level_image
                $destinationPath = public_path('assets/media/level_image');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                $fileName = uniqid('level_') . '.' . $image->getClientOriginalExtension();
                $image->move($destinationPath, $fileName);
                $relativePath = 'assets/media/level_image/' . $fileName;

                $insertData = $data;
                $insertData['image'] = $relativePath;
            } else {
                // Jika hanya nama file (misal upload sudah dilakukan di tempat lain)
                $insertData = $data;
                $insertData['image'] = $image;
            }

            $insertData = [
                'name' => $insertData['nama'] ?? $level->name,
                'feedback_data_type' => $insertData['feedback_tipe_data'] ? $insertData['feedback_tipe_data'] : $level->feedback_data_type,
                'feedback_algorithm' => $insertData['feedback_algoritma'] ? $insertData['feedback_algoritma'] : $level->feedback_algorithm,
                'image' => $insertData['image'] ?? $level->image,
                // 'order' => $insertData['order'] ?? $level->order, // Keep existing order if not provided
                'manual_active' => $insertData['manual_active'] ?? $level->manual_active
            ];

            $opr = $this->levelRepository->update($level->id, $insertData);
            DB::commit();

            // Ambil data level terbaru (agar path image terbaru pasti terpakai)
            $updatedLevel = $this->levelRepository->getById($level->id);
            $newImagePath = $updatedLevel->image;

            // Dispatch job per kelas untuk update pencapaian.img
            $kelasList = Kelas::all();
            foreach ($kelasList as $kelas) {
                UpdatePencapaianLevel::dispatch($updatedLevel->id, $newImagePath, $kelas->id);
            }

            return BaseResponse::updated($opr);
        } catch (\Exception $e) {
            DB::rollBack();
            return BaseResponse::errorMessage($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $opr = $this->levelRepository->destroy($id);
            DB::commit();
            return BaseResponse::deleted($opr);
        } catch (\Exception $e) {
            DB::rollBack();
            return BaseResponse::errorMessage($e->getMessage());
        }
    }

    public function getData()
    {
        try {
            $opr = $this->levelRepository->getData();
            return BaseResponse::json($opr);
        } catch (\Exception $e) {
            return BaseResponse::errorMessage($e->getMessage());
        }
    }
    
    public function updateOrder($request)
    {
        $opr = $this->levelRepository->updateOrder($request->all());
        return $opr;
    }
}
